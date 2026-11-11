<?php

class PcdController {
    private $pcdModel;

    public function __construct() {
        $this->pcdModel = new Pcd();
    }

    /**
     * Exibe o formulário de cadastro público (wizard).
     */
    public function cadastro() {
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");
        $tiposDeficiencia = $this->pcdModel->getTiposDeficiencia();
        view('cadastro-pcd', ['tiposDeficiencia' => $tiposDeficiencia]);
    }

    /**
     * Salva o cadastro do PCD vindo do formulário público (via AJAX Fetch ou POST).
     */
    public function store() {
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        $accept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
        
        $isJson = (strpos($accept, 'application/json') !== false) || (strpos($contentType, 'application/json') !== false);
        
        if (strpos($contentType, "application/json") !== false) {
            $content = trim(file_get_contents("php://input"));
            $data = json_decode($content, true);
        } else {
            $data = $_POST;
        }
        
        if (!is_array($data)) {
            $data = [];
        }
        // Campos de controle NUNCA vêm do formulário público
        unset($data['status'], $data['origem_cadastro_id'], $data['id']);

        // Anti-spam: no máximo 5 cadastros por hora vindos do mesmo IP
        $rlKey = 'cadastro_pcd';
        $rateOk = rate_limit_check($rlKey, 5, 3600);

        // Mensagem neutra: NÃO confirma se um CPF já está na base.
        // (Revelar isso diria a qualquer pessoa que alguém tem deficiência - dado sensível pela LGPD.)
        $msgDuplicado = 'Não foi possível concluir o cadastro com os dados informados. Se você já fez seu cadastro anteriormente, entre em contato com o Conselho.';

        if ($isJson) {
            header('Content-Type: application/json');

            if (!$rateOk) {
                echo json_encode(['success' => false, 'message' => 'Muitas tentativas de cadastro. Tente novamente mais tarde.']);
                exit;
            }
            
            if (empty($data['nome_completo']) || empty($data['cpf']) || empty($data['data_nascimento'])) {
                echo json_encode(['success' => false, 'message' => 'Nome, CPF e data de nascimento são obrigatórios.']);
                exit;
            }

            if (!valid_cpf($data['cpf'])) {
                echo json_encode(['success' => false, 'message' => 'CPF inválido.']);
                exit;
            }

            rate_limit_hit($rlKey, 3600);

            if ($this->pcdModel->existsCpf($data['cpf'])) {
                echo json_encode(['success' => false, 'message' => $msgDuplicado]);
                exit;
            }

            // Validar arquivos obrigatórios no cadastro público
            $requiredFiles = ['doc_rg', 'doc_residencia', 'doc_laudo'];
            foreach ($requiredFiles as $fileKey) {
                if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
                    echo json_encode(['success' => false, 'message' => 'Os documentos anexados (RG/CNH, Comprovante de Residência e Laudo Médico) são obrigatórios.']);
                    exit;
                }
            }

            try {
                // Parser robusto para deficiências
                $defIds = [];
                if (isset($data['deficiencias_ids'])) {
                    $defIds = $data['deficiencias_ids'];
                } elseif (isset($data['deficiencia_id'])) {
                    $defIds = $data['deficiencia_id'];
                } elseif (isset($data['deficiencias'])) {
                    $defIds = $data['deficiencias'];
                }
                if (!is_array($defIds)) {
                    if (empty($defIds)) {
                        $defIds = [];
                    } elseif (is_string($defIds) && strpos($defIds, ',') !== false) {
                        $defIds = array_map('trim', explode(',', $defIds));
                    } else {
                        $defIds = [$defIds];
                    }
                }
                $data['deficiencias_ids'] = array_filter(array_map('intval', $defIds));
                $data['status'] = 'pendente';
                
                $this->preValidateUploads(); // valida arquivos ANTES de gravar o PCD
                $pcdId = $this->pcdModel->create($data);
                $this->handleFileUploads($pcdId, null);

                AuditLog::registrar('pcd_cadastro_publico', 'pcds', [
                    'pcd_id' => $pcdId,
                    'nome'   => $data['nome_completo'],
                ], 'Novo pré-cadastro de PCD enviado pelo portal público.');
                
                echo json_encode(['success' => true]);
                exit;
            } catch (Throwable $e) {
                echo json_encode(['success' => false, 'message' => 'Erro ao salvar cadastro: ' . safeError($e)]);
                exit;
            }
        } else {
            // Caso seja submetido como POST padrão
            if (!$rateOk) {
                $_SESSION['error_msg'] = "Muitas tentativas de cadastro. Tente novamente mais tarde.";
                redirect('/cadastro-pcd');
            }
            if (empty($data['nome_completo']) || empty($data['cpf']) || empty($data['data_nascimento'])) {
                $_SESSION['error_msg'] = "Nome, CPF e data de nascimento são obrigatórios.";
                redirect('/cadastro-pcd');
            }
            if (!valid_cpf($data['cpf'])) {
                $_SESSION['error_msg'] = "CPF inválido.";
                redirect('/cadastro-pcd');
            }
            rate_limit_hit($rlKey, 3600);
            if ($this->pcdModel->existsCpf($data['cpf'])) {
                $_SESSION['error_msg'] = $msgDuplicado;
                redirect('/cadastro-pcd');
            }

            // Validar arquivos obrigatórios no cadastro público padrão
            $requiredFiles = ['doc_rg', 'doc_residencia', 'doc_laudo'];
            foreach ($requiredFiles as $fileKey) {
                if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
                    $_SESSION['error_msg'] = "Os documentos anexados (RG/CNH, Comprovante de Residência e Laudo Médico) são obrigatórios.";
                    redirect('/cadastro-pcd');
                }
            }
            try {
                // Parser robusto para deficiências
                $defIds = [];
                if (isset($data['deficiencias_ids'])) {
                    $defIds = $data['deficiencias_ids'];
                } elseif (isset($data['deficiencia_id'])) {
                    $defIds = $data['deficiencia_id'];
                } elseif (isset($data['deficiencias'])) {
                    $defIds = $data['deficiencias'];
                }
                if (!is_array($defIds)) {
                    if (empty($defIds)) {
                        $defIds = [];
                    } elseif (is_string($defIds) && strpos($defIds, ',') !== false) {
                        $defIds = array_map('trim', explode(',', $defIds));
                    } else {
                        $defIds = [$defIds];
                    }
                }
                $data['deficiencias_ids'] = array_filter(array_map('intval', $defIds));
                $data['status'] = 'pendente';

                $this->preValidateUploads(); // valida arquivos ANTES de gravar o PCD
                $pcdId = $this->pcdModel->create($data);
                $this->handleFileUploads($pcdId, null);

                AuditLog::registrar('pcd_cadastro_publico', 'pcds', [
                    'pcd_id' => $pcdId,
                    'nome'   => $data['nome_completo'],
                ], 'Novo pré-cadastro de PCD enviado pelo portal público.');

                redirect('/cadastro-pcd?sucesso=1');
            } catch (Throwable $e) {
                $_SESSION['error_msg'] = "Erro: " . safeError($e);
                redirect('/cadastro-pcd');
            }
        }
    }

    /**
     * Lista de PCDs no painel administrativo.
     */
    public function index() {
        checkPermission(['admin_total', 'admin_parcial']);
        $pcds = $this->pcdModel->all();
        
        $success = $_SESSION['success_msg'] ?? null;
        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);

        view('admin/pcds/index', [
            'activeTab' => 'cadastros',
            'pcds' => $pcds,
            'success' => $success,
            'error' => $error
        ]);
    }

    /**
     * Exibe formulário de criação de PCD no painel administrativo.
     */
    public function create() {
        checkPermission(['admin_total']);
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");
        $tiposDeficiencia = $this->pcdModel->getTiposDeficiencia();
        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['error_msg']);

        view('admin/pcds/create', [
            'activeTab' => 'cadastros',
            'tiposDeficiencia' => $tiposDeficiencia,
            'error' => $error
        ]);
    }

    /**
     * Salva o PCD criado pelo administrador (POST).
     */
    public function storeAdmin() {
        checkPermission(['admin_total']);
        $data = $_POST;

        if (empty($data['nome_completo']) || empty($data['cpf']) || empty($data['rg']) || empty($data['data_nascimento'])) {
            $_SESSION['error_msg'] = "Preencha todos os campos obrigatórios.";
            redirect('/admin/pcds/create');
        }

        if ($this->pcdModel->existsCpf($data['cpf'])) {
            $_SESSION['error_msg'] = "Este CPF já está cadastrado.";
            redirect('/admin/pcds/create');
        }

        // Validar arquivos obrigatórios no cadastro administrativo
        $requiredFiles = ['doc_rg', 'doc_residencia', 'doc_laudo'];
        foreach ($requiredFiles as $fileKey) {
            if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['error_msg'] = "Os documentos anexados (RG/CNH, Comprovante de Residência e Laudo Médico) são obrigatórios.";
                redirect('/admin/pcds/create');
            }
        }

        // Parser robusto para deficiências
        $defIds = [];
        if (isset($data['deficiencias_ids'])) {
            $defIds = $data['deficiencias_ids'];
        } elseif (isset($data['deficiencia_id'])) {
            $defIds = $data['deficiencia_id'];
        } elseif (isset($data['deficiencias'])) {
            $defIds = $data['deficiencias'];
        }
        if (!is_array($defIds)) {
            if (empty($defIds)) {
                $defIds = [];
            } elseif (is_string($defIds) && strpos($defIds, ',') !== false) {
                $defIds = array_map('trim', explode(',', $defIds));
            } else {
                $defIds = [$defIds];
            }
        }
        $data['deficiencias_ids'] = array_filter(array_map('intval', $defIds));
        $data['status'] = 'deferido';

        try {
            $this->preValidateUploads(); // valida arquivos ANTES de gravar o PCD
                $pcdId = $this->pcdModel->create($data);
            
            // Processa uploads vinculando ao administrador logado
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $this->handleFileUploads($pcdId, $userId);

            // Auditoria
            AuditLog::registrar('pcd_criado_admin', 'pcds', [
                'pcd_id' => $pcdId,
                'nome'   => $data['nome_completo'],
                'cpf'    => substr(preg_replace('/\D/', '', $data['cpf']), 0, 3) . '.***.***-**'
            ], 'Novo PCD cadastrado via painel administrativo.');
            
            $_SESSION['success_msg'] = "PCD cadastrado com sucesso!";
            redirect('/admin/pcds');
        } catch (Throwable $e) {
            $_SESSION['error_msg'] = "Erro ao cadastrar PCD: " . safeError($e);
            redirect('/admin/pcds/create');
        }
    }

    /**
     * Exibe formulário de edição de PCD no painel administrativo.
     */
    public function edit() {
        checkPermission(['admin_total', 'admin_parcial']);
        $id = $_GET['id'] ?? null;
        if (!$id) {
            redirect('/admin/pcds');
        }

        $pcd = $this->pcdModel->find($id);
        if (!$pcd) {
            $_SESSION['error_msg'] = "PCD não encontrado.";
            redirect('/admin/pcds');
        }

        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");
        $tiposDeficiencia = $this->pcdModel->getTiposDeficiencia();
        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['error_msg']);

        // Extrai apenas os IDs das deficiências do PCD para facilitar na view
        $pcdDefIds = array_column($pcd['deficiencias'], 'id');

        view('admin/pcds/edit', [
            'activeTab' => 'cadastros',
            'pcd' => $pcd,
            'pcdDefIds' => $pcdDefIds,
            'tiposDeficiencia' => $tiposDeficiencia,
            'error' => $error
        ]);
    }

    /**
     * Salva as edições do PCD (POST).
     */
    public function update() {
        checkPermission(['admin_total']);
        $id = $_POST['id'] ?? null;
        if (!$id) {
            redirect('/admin/pcds');
        }

        $data = $_POST;
        if (empty($data['nome_completo']) || empty($data['cpf']) || empty($data['rg']) || empty($data['data_nascimento'])) {
            $_SESSION['error_msg'] = "Preencha todos os campos obrigatórios.";
            redirect('/admin/pcds/edit?id=' . $id);
        }

        if ($this->pcdModel->existsCpf($data['cpf'], $id)) {
            $_SESSION['error_msg'] = "Este CPF já está cadastrado para outro PCD.";
            redirect('/admin/pcds/edit?id=' . $id);
        }

        // Parser robusto para deficiências
        $defIds = [];
        if (isset($data['deficiencias_ids'])) {
            $defIds = $data['deficiencias_ids'];
        } elseif (isset($data['deficiencia_id'])) {
            $defIds = $data['deficiencia_id'];
        } elseif (isset($data['deficiencias'])) {
            $defIds = $data['deficiencias'];
        }
        if (!is_array($defIds)) {
            if (empty($defIds)) {
                $defIds = [];
            } elseif (is_string($defIds) && strpos($defIds, ',') !== false) {
                $defIds = array_map('trim', explode(',', $defIds));
            } else {
                $defIds = [$defIds];
            }
        }
        $data['deficiencias_ids'] = array_filter(array_map('intval', $defIds));
        $existingPcd = $this->pcdModel->find($id);
        if (!$existingPcd) {
            $_SESSION['error_msg'] = "PCD não encontrado.";
            redirect('/admin/pcds');
        }
        $data['status'] = in_list($data['status'] ?? null, ['pendente', 'deferido', 'indeferido'], $existingPcd['status'] ?? 'deferido');

        try {
            $this->pcdModel->update($id, $data);
            
            // Processa uploads vinculando ao administrador logado
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $this->handleFileUploads($id, $userId);

            // Auditoria
            AuditLog::registrar('pcd_atualizado', 'pcds', [
                'pcd_id' => $id,
                'nome'   => $data['nome_completo']
            ], 'Cadastro de PCD atualizado por administrador.');
            
            $_SESSION['success_msg'] = "PCD atualizado com sucesso!";
            redirect('/admin/pcds');
        } catch (Throwable $e) {
            $_SESSION['error_msg'] = "Erro ao atualizar PCD: " . safeError($e);
            redirect('/admin/pcds/edit?id=' . $id);
        }
    }

    /**
     * Remove um PCD (soft delete).
     */
    public function delete() {
        checkPermission(['admin_total']);
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if ($id) {
            try {
                $pcd = $this->pcdModel->find($id);
                $this->pcdModel->delete($id);

                AuditLog::registrar('pcd_excluido', 'pcds', [
                    'pcd_id' => $id,
                    'nome'   => $pcd['nome_completo'] ?? ''
                ], 'Cadastro de PCD excluído do sistema.');

                $_SESSION['success_msg'] = "Cadastro de PCD excluído com sucesso!";
            } catch (Throwable $e) {
                $_SESSION['error_msg'] = "Erro ao excluir PCD: " . safeError($e);
            }
        }
        redirect('/admin/pcds');
    }

    /**
     * Defere (aprova) um PCD pendente.
     */
    public function deferir() {
        checkPermission(['admin_total']);
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if ($id) {
            try {
                $pcd = $this->pcdModel->find($id);
                $this->pcdModel->updateStatus($id, 'deferido');

                AuditLog::registrar('pcd_deferido', 'pcds', [
                    'pcd_id' => $id,
                    'nome'   => $pcd['nome_completo'] ?? ''
                ], 'Cadastro de PCD deferido (aprovado).');

                $_SESSION['success_msg'] = "Cadastro de PCD deferido com sucesso!";
            } catch (Throwable $e) {
                $_SESSION['error_msg'] = "Erro ao deferir PCD: " . safeError($e);
            }
        }
        redirect('/admin/pcds');
    }

    /**
     * Indefere (rejeita e exclui) um PCD pendente.
     */
    public function indeferir() {
        checkPermission(['admin_total']);
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if ($id) {
            try {
                $pcd = $this->pcdModel->find($id);
                $this->pcdModel->delete($id);

                AuditLog::registrar('pcd_indeferido', 'pcds', [
                    'pcd_id' => $id,
                    'nome'   => $pcd['nome_completo'] ?? ''
                ], 'Cadastro de PCD indeferido e removido do sistema.');

                $_SESSION['success_msg'] = "Cadastro de PCD indeferido e excluído com sucesso!";
            } catch (Throwable $e) {
                $_SESSION['error_msg'] = "Erro ao indeferir PCD: " . safeError($e);
            }
        }
        redirect('/admin/pcds');
    }

    /**
     * Entrega um documento do PCD (RG, comprovante, laudo) SOMENTE para administradores logados.
     * Os arquivos ficam em /storage/pcd_docs (fora da pasta pública), então não existe
     * nenhuma URL direta para eles: a única forma de abrir é por esta rota, que checa permissão.
     */
    public function documento() {
        checkPermission(['admin_total', 'admin_parcial']);

        // Proteção LGPD contra extração em massa (Anti-Scraping / Exfiltração de laudos médicos):
        // Limita a 50 requisições de documentos a cada 10 minutos por usuário
        $userId = $_SESSION['user_id'] ?? 0;
        $rlKey = 'pcd_doc_view_' . $userId;
        $maxDocs = 50;
        $windowSeconds = 600;

        $docId = (int)($_GET['id'] ?? 0);

        if (!rate_limit_check($rlKey, $maxDocs, $windowSeconds)) {
            AuditLog::registrar('alerta_seguranca_exfiltracao', 'pcds', [
                'usuario_id'     => $userId,
                'doc_id_tentado' => $docId,
                'limite'         => $maxDocs,
                'janela_seg'     => $windowSeconds
            ], 'ALERTA LGPD: Limite de taxa excedido (Rate Limit) na visualização de documentos sensíveis.');

            http_response_code(429);
            header('Retry-After: ' . $windowSeconds);
            header('Content-Type: text/html; charset=utf-8');
            exit('<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><title>Limite Atingido - LGPD</title><style>body{font-family:sans-serif;padding:3rem;text-align:center;background:#0f172a;color:#f8fafc}h2{color:#ef4444}p{color:#94a3b8;max-width:500px;margin:1rem auto}</style></head><body><h2>⚠️ Limite de Acesso Excedido</h2><p>Por motivos de segurança e conformidade com a <strong>LGPD (Proteção de Dados Sensíveis de Saúde)</strong>, a consulta em lote de documentos e laudos médicos foi temporariamente suspensa para a sua sessão.</p><p>Aguarde alguns minutos antes de consultar novos documentos.</p></body></html>');
        }

        rate_limit_hit($rlKey, $windowSeconds);

        $doc = $docId ? $this->pcdModel->findDocument($docId) : null;
        if (!$doc) {
            http_response_code(404);
            exit('Documento não encontrado.');
        }

        // Auditoria estrita LGPD: registra o acesso a documento médico/pessoal
        AuditLog::registrar('pcd_documento_visualizado', 'pcds', [
            'doc_id'         => $doc['id'],
            'pcd_id'         => $doc['pcd_id'],
            'tipo_documento' => $doc['tipo_documento'],
        ], 'Visualização de documento comprobatório/laudo de PCD (LGPD).');

        // Usa somente o nome do arquivo (basename) para impedir "path traversal" (../../)
        $fileName = basename($doc['caminho_arquivo']);
        $candidates = [
            PCD_DOCS_DIR . DIRECTORY_SEPARATOR . $fileName,
            __DIR__ . '/../../public/uploads/' . $fileName,   // arquivos antigos, antes da migração
        ];
        $path = null;
        foreach ($candidates as $c) {
            if (is_file($c)) { $path = $c; break; }
        }
        if (!$path) {
            http_response_code(404);
            exit('Arquivo não encontrado no servidor.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($path);
        $allowed = ['application/pdf', 'image/jpeg', 'image/png'];
        if (!in_array($mime, $allowed, true)) {
            http_response_code(415);
            exit('Tipo de arquivo não permitido.');
        }

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($path));
        header('Content-Disposition: inline; filename="' . preg_replace('/[^A-Za-z0-9._-]/', '_', $doc['tipo_documento']) . '_' . (int)$doc['pcd_id'] . '.' . pathinfo($fileName, PATHINFO_EXTENSION) . '"');
        header('Cache-Control: no-store, private');
        header("Content-Security-Policy: default-src 'none'; img-src 'self'; style-src 'unsafe-inline'; sandbox");
        readfile($path);
        exit;
    }

    /** Valida os arquivos enviados antes de criar qualquer registro no banco. */
    private function preValidateUploads() {
        $allowed = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'];
        $labels = ['doc_rg' => 'RG/CNH', 'doc_residencia' => 'comprovante de residência', 'doc_laudo' => 'laudo médico'];
        foreach ($labels as $input => $label) {
            if (isset($_FILES[$input]) && $_FILES[$input]['error'] === UPLOAD_ERR_OK) {
                validate_upload($_FILES[$input], $allowed, 5 * 1024 * 1024, $label);
            }
        }
    }

    /**
     * Processa os uploads de arquivos e os salva no banco de dados.
     * Os documentos vão para a pasta PRIVADA /storage/pcd_docs (não acessível pela web).
     */
    private function handleFileUploads($pcdId, $userId) {
        $allowed = [
            'application/pdf' => 'pdf',
            'image/jpeg'      => 'jpg',
            'image/png'       => 'png',
        ];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!is_dir(PCD_DOCS_DIR)) {
            mkdir(PCD_DOCS_DIR, 0750, true);
        }

        $filesToProcess = [
            'doc_rg' => ['rg', 'RG/CNH'],
            'doc_residencia' => ['comprovante_residencia', 'comprovante de residência'],
            'doc_laudo' => ['laudo_medico', 'laudo médico'],
        ];

        foreach ($filesToProcess as $fileInputName => [$docType, $label]) {
            if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES[$fileInputName];

                // Valida tamanho e TIPO REAL do arquivo (não confia na extensão)
                $ext = validate_upload($file, $allowed, $maxSize, $label);

                // Nome aleatório: não contém ID nem tipo, então não dá para adivinhar
                $newFileName = random_filename('pcd_', $ext);
                $destination = PCD_DOCS_DIR . DIRECTORY_SEPARATOR . $newFileName;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    @chmod($destination, 0640);
                    // Se estiver atualizando (edição), marca o documento anterior do mesmo tipo como deletado
                    $this->pcdModel->softDeleteDocumentByType($pcdId, $docType);

                    // Salva apenas uma referência interna (não é uma URL pública)
                    $this->pcdModel->addDocument($pcdId, $userId, $docType, 'storage/pcd_docs/' . $newFileName);
                } else {
                    throw new AppException("Falha ao salvar o arquivo de $label.");
                }
            }
        }
    }
}
