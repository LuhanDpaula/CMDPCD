<?php

class ConteudoController {
    private $noticiaModel;
    private $membroModel;
    private $documentoModel;

    public function __construct() {
        checkAuth(); // Protege todos os métodos deste controller
        $this->noticiaModel = new Noticia();
        $this->membroModel = new MembroConselho();
        $this->documentoModel = new DocumentoPublico();
    }

    /**
     * Painel central do gerenciador de conteúdos.
     */
    public function index() {
        checkPermission(['admin_total', 'editor', 'admin_parcial']);
        $noticiasCount = count($this->noticiaModel->all());
        $membrosCount = count($this->membroModel->allActive());
        $presidentesCount = count($this->membroModel->allPresidents());
        $documentosCount = count($this->documentoModel->all());

        $success = $_SESSION['success_msg'] ?? null;
        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);

        view('admin/conteudos', [
            'activeTab' => 'conteudos',
            'noticiasCount' => $noticiasCount,
            'membrosCount' => $membrosCount,
            'presidentesCount' => $presidentesCount,
            'documentosCount' => $documentosCount,
            'success' => $success,
            'error' => $error
        ]);
    }

    // ==========================================
    // NOTÍCIAS CRUD
    // ==========================================

    public function indexNoticias() {
        checkPermission(['admin_total', 'editor', 'admin_parcial']);
        $noticias = $this->noticiaModel->all();
        $success = $_SESSION['success_msg'] ?? null;
        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);

        view('admin/conteudos/noticias/index', [
            'activeTab' => 'conteudos',
            'noticias' => $noticias,
            'success' => $success,
            'error' => $error
        ]);
    }

    public function createNoticia() {
        checkPermission(['admin_total', 'editor']);
        $error = $_SESSION['error_msg'] ?? null;
        $old = $_SESSION['old_input'] ?? [];
        unset($_SESSION['error_msg'], $_SESSION['old_input']);

        view('admin/conteudos/noticias/create', [
            'activeTab' => 'conteudos',
            'error' => $error,
            'old' => $old
        ]);
    }

    public function storeNoticia() {
        checkPermission(['admin_total', 'editor']);
        $data = $_POST;
        if (empty($data['titulo']) || empty($data['conteudo']) || empty($data['tema'])) {
            $_SESSION['error_msg'] = "Preencha todos os campos obrigatórios.";
            $_SESSION['old_input'] = $data;
            redirect('/admin/conteudos/noticias/create');
        }

        try {
            $data['usuario_id'] = $_SESSION['user_id'];
            $data['conteudo'] = sanitize_html($data['conteudo']);   // remove scripts/HTML perigoso
            $data['status'] = in_list($data['status'] ?? null, ['rascunho', 'publicado', 'arquivado'], 'rascunho');
            
            // Processa upload da imagem de capa se enviada
            if (isset($_FILES['imagem_capa']) && $_FILES['imagem_capa']['error'] === UPLOAD_ERR_OK) {
                $data['imagem_capa'] = $this->uploadFile('imagem_capa');
            }

            $this->noticiaModel->create($data);

            AuditLog::registrar('noticia_criada', 'conteudos', [
                'titulo' => $data['titulo'],
                'tema'   => $data['tema'],
                'status' => $data['status']
            ], 'Nova notícia publicada no portal.');

            $_SESSION['success_msg'] = "Notícia cadastrada com sucesso!";
            redirect('/admin/conteudos/noticias');
        } catch (Throwable $e) {
            $_SESSION['error_msg'] = "Erro ao cadastrar notícia: " . safeError($e);
            $_SESSION['old_input'] = $data;
            redirect('/admin/conteudos/noticias/create');
        }
    }

    public function editNoticia() {
        checkPermission(['admin_total', 'editor', 'admin_parcial']);
        $id = $_GET['id'] ?? null;
        if (!$id) {
            redirect('/admin/conteudos/noticias');
        }

        $noticia = $this->noticiaModel->find($id);
        if (!$noticia) {
            $_SESSION['error_msg'] = "Notícia não encontrada.";
            redirect('/admin/conteudos/noticias');
        }

        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['error_msg']);

        view('admin/conteudos/noticias/edit', [
            'activeTab' => 'conteudos',
            'noticia' => $noticia,
            'error' => $error
        ]);
    }

    public function updateNoticia() {
        checkPermission(['admin_total', 'editor']);
        $id = $_POST['id'] ?? null;
        if (!$id) {
            redirect('/admin/conteudos/noticias');
        }

        $noticia = $this->noticiaModel->find($id);
        if (!$noticia) {
            $_SESSION['error_msg'] = "Notícia não encontrada.";
            redirect('/admin/conteudos/noticias');
        }

        $data = $_POST;
        if (empty($data['titulo']) || empty($data['conteudo']) || empty($data['tema'])) {
            $_SESSION['error_msg'] = "Preencha todos os campos obrigatórios.";
            redirect('/admin/conteudos/noticias/edit?id=' . $id);
        }

        try {
            $data['status'] = in_list($data['status'] ?? null, ['rascunho', 'publicado', 'arquivado'], $noticia['status']);
            $data['conteudo'] = sanitize_html($data['conteudo']);   // remove scripts/HTML perigoso
            // Mantém a imagem atual como padrão
            $data['imagem_capa'] = $noticia['imagem_capa'];

            // Se uma nova imagem foi enviada, faz o upload e substitui
            if (isset($_FILES['imagem_capa']) && $_FILES['imagem_capa']['error'] === UPLOAD_ERR_OK) {
                $data['imagem_capa'] = $this->uploadFile('imagem_capa');
            }

            $this->noticiaModel->update($id, $data);

            AuditLog::registrar('noticia_atualizada', 'conteudos', [
                'noticia_id' => $id,
                'titulo'     => $data['titulo'],
                'status'     => $data['status']
            ], 'Notícia atualizada.');

            $_SESSION['success_msg'] = "Notícia atualizada com sucesso!";
            redirect('/admin/conteudos/noticias');
        } catch (Throwable $e) {
            $_SESSION['error_msg'] = "Erro ao atualizar notícia: " . safeError($e);
            redirect('/admin/conteudos/noticias/edit?id=' . $id);
        }
    }

    public function deleteNoticia() {
        checkPermission(['admin_total', 'editor']);
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if ($id) {
            try {
                $noticia = $this->noticiaModel->find($id);
                $this->noticiaModel->delete($id);

                AuditLog::registrar('noticia_excluida', 'conteudos', [
                    'noticia_id' => $id,
                    'titulo'     => $noticia['titulo'] ?? ''
                ], 'Notícia excluída do portal.');

                $_SESSION['success_msg'] = "Notícia excluída com sucesso!";
            } catch (Throwable $e) {
                $_SESSION['error_msg'] = "Erro ao excluir notícia: " . safeError($e);
            }
        }
        redirect('/admin/conteudos/noticias');
    }

    // ==========================================
    // MEMBROS E PRESIDENTES CRUD
    // ==========================================

    public function indexMembros() {
        checkPermission(['admin_total', 'editor', 'admin_parcial']);
        $membros = $this->membroModel->all();
        $success = $_SESSION['success_msg'] ?? null;
        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);

        view('admin/conteudos/membros/index', [
            'activeTab' => 'conteudos',
            'membros' => $membros,
            'success' => $success,
            'error' => $error
        ]);
    }

    public function createMembro() {
        checkPermission(['admin_total', 'editor']);
        $error = $_SESSION['error_msg'] ?? null;
        $old = $_SESSION['old_input'] ?? [];
        unset($_SESSION['error_msg'], $_SESSION['old_input']);

        view('admin/conteudos/membros/create', [
            'activeTab' => 'conteudos',
            'error' => $error,
            'old' => $old
        ]);
    }

    public function storeMembro() {
        checkPermission(['admin_total', 'editor']);
        $data = $_POST;
        if (empty($data['nome_completo']) || empty($data['funcao']) || empty($data['data_inicio'])) {
            $_SESSION['error_msg'] = "Preencha todos os campos obrigatórios.";
            $_SESSION['old_input'] = $data;
            redirect('/admin/conteudos/membros/create');
        }

        try {
            // Processa upload da foto se enviada
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $data['foto'] = $this->uploadFile('foto');
            }

            // Ativo por padrão se vier marcado, ou senão 0
            $data['ativo'] = isset($_POST['ativo']) ? 1 : 0;

            $this->membroModel->create($data);

            AuditLog::registrar('membro_criado', 'conselho', [
                'nome'   => $data['nome_completo'],
                'funcao' => $data['funcao']
            ], 'Novo membro/presidente do conselho registrado.');

            $_SESSION['success_msg'] = "Membro/Presidente cadastrado com sucesso!";
            redirect('/admin/conteudos/membros');
        } catch (Throwable $e) {
            $_SESSION['error_msg'] = "Erro ao cadastrar membro/presidente: " . safeError($e);
            $_SESSION['old_input'] = $data;
            redirect('/admin/conteudos/membros/create');
        }
    }

    public function editMembro() {
        checkPermission(['admin_total', 'editor', 'admin_parcial']);
        $id = $_GET['id'] ?? null;
        if (!$id) {
            redirect('/admin/conteudos/membros');
        }

        $membro = $this->membroModel->find($id);
        if (!$membro) {
            $_SESSION['error_msg'] = "Membro/Presidente não encontrado.";
            redirect('/admin/conteudos/membros');
        }

        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['error_msg']);

        view('admin/conteudos/membros/edit', [
            'activeTab' => 'conteudos',
            'membro' => $membro,
            'error' => $error
        ]);
    }

    public function updateMembro() {
        checkPermission(['admin_total', 'editor']);
        $id = $_POST['id'] ?? null;
        if (!$id) {
            redirect('/admin/conteudos/membros');
        }

        $membro = $this->membroModel->find($id);
        if (!$membro) {
            $_SESSION['error_msg'] = "Membro/Presidente não encontrado.";
            redirect('/admin/conteudos/membros');
        }

        $data = $_POST;
        if (empty($data['nome_completo']) || empty($data['funcao']) || empty($data['data_inicio'])) {
            $_SESSION['error_msg'] = "Preencha todos os campos obrigatórios.";
            redirect('/admin/conteudos/membros/edit?id=' . $id);
        }

        try {
            // Mantém a foto atual por padrão
            $data['foto'] = $membro['foto'];

            // Se nova foto enviada
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $data['foto'] = $this->uploadFile('foto');
            }

            $data['ativo'] = isset($_POST['ativo']) ? 1 : 0;

            $this->membroModel->update($id, $data);

            AuditLog::registrar('membro_atualizado', 'conselho', [
                'membro_id' => $id,
                'nome'      => $data['nome_completo'],
                'funcao'    => $data['funcao']
            ], 'Membro do conselho atualizado.');

            $_SESSION['success_msg'] = "Membro/Presidente atualizado com sucesso!";
            redirect('/admin/conteudos/membros');
        } catch (Throwable $e) {
            $_SESSION['error_msg'] = "Erro ao atualizar membro/presidente: " . safeError($e);
            redirect('/admin/conteudos/membros/edit?id=' . $id);
        }
    }

    public function deleteMembro() {
        checkPermission(['admin_total', 'editor']);
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if ($id) {
            try {
                $membro = $this->membroModel->find($id);
                $this->membroModel->delete($id);

                AuditLog::registrar('membro_excluido', 'conselho', [
                    'membro_id' => $id,
                    'nome'      => $membro['nome_completo'] ?? ''
                ], 'Membro do conselho excluído.');

                $_SESSION['success_msg'] = "Membro/Presidente excluído com sucesso!";
            } catch (Throwable $e) {
                $_SESSION['error_msg'] = "Erro ao excluir membro/presidente: " . safeError($e);
            }
        }
        redirect('/admin/conteudos/membros');
    }

    // ==========================================
    // DOCUMENTOS CRUD
    // ==========================================

    public function indexDocumentos() {
        checkPermission(['admin_total', 'editor', 'admin_parcial']);
        $documentos = $this->documentoModel->all();
        $success = $_SESSION['success_msg'] ?? null;
        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);

        view('admin/conteudos/documentos/index', [
            'activeTab' => 'conteudos',
            'documentos' => $documentos,
            'success' => $success,
            'error' => $error
        ]);
    }

    public function createDocumento() {
        checkPermission(['admin_total', 'editor']);
        $error = $_SESSION['error_msg'] ?? null;
        $old = $_SESSION['old_input'] ?? [];
        unset($_SESSION['error_msg'], $_SESSION['old_input']);

        view('admin/conteudos/documentos/create', [
            'activeTab' => 'conteudos',
            'error' => $error,
            'old' => $old
        ]);
    }

    public function storeDocumento() {
        checkPermission(['admin_total', 'editor']);
        $data = $_POST;
        if (empty($data['titulo']) || empty($data['tipo']) || empty($data['data_publicacao'])) {
            $_SESSION['error_msg'] = "Preencha todos os campos obrigatórios.";
            $_SESSION['old_input'] = $data;
            redirect('/admin/conteudos/documentos/create');
        }

        try {
            if (!isset($_FILES['caminho_arquivo']) || $_FILES['caminho_arquivo']['error'] !== UPLOAD_ERR_OK) {
                throw new AppException("O upload do arquivo PDF é obrigatório.");
            }

            $data['caminho_arquivo'] = $this->uploadPdf('caminho_arquivo');

            $this->documentoModel->create($data);

            AuditLog::registrar('documento_publicado', 'conteudos', [
                'titulo' => $data['titulo'],
                'tipo'   => $data['tipo'],
            ], 'Novo documento oficial publicado na transparência.');

            $_SESSION['success_msg'] = "Documento público cadastrado com sucesso!";
            redirect('/admin/conteudos/documentos');
        } catch (Throwable $e) {
            $_SESSION['error_msg'] = "Erro ao cadastrar documento: " . safeError($e);
            $_SESSION['old_input'] = $data;
            redirect('/admin/conteudos/documentos/create');
        }
    }

    public function editDocumento() {
        checkPermission(['admin_total', 'editor', 'admin_parcial']);
        $id = $_GET['id'] ?? null;
        if (!$id) {
            redirect('/admin/conteudos/documentos');
        }

        $documento = $this->documentoModel->find($id);
        if (!$documento) {
            $_SESSION['error_msg'] = "Documento não encontrado.";
            redirect('/admin/conteudos/documentos');
        }

        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['error_msg']);

        view('admin/conteudos/documentos/edit', [
            'activeTab' => 'conteudos',
            'documento' => $documento,
            'error' => $error
        ]);
    }

    public function updateDocumento() {
        checkPermission(['admin_total', 'editor']);
        $id = $_POST['id'] ?? null;
        if (!$id) {
            redirect('/admin/conteudos/documentos');
        }

        $documento = $this->documentoModel->find($id);
        if (!$documento) {
            $_SESSION['error_msg'] = "Documento não encontrado.";
            redirect('/admin/conteudos/documentos');
        }

        $data = $_POST;
        if (empty($data['titulo']) || empty($data['tipo']) || empty($data['data_publicacao'])) {
            $_SESSION['error_msg'] = "Preencha todos os campos obrigatórios.";
            redirect('/admin/conteudos/documentos/edit?id=' . $id);
        }

        try {
            $data['caminho_arquivo'] = $documento['caminho_arquivo'];

            if (isset($_FILES['caminho_arquivo']) && $_FILES['caminho_arquivo']['error'] === UPLOAD_ERR_OK) {
                $data['caminho_arquivo'] = $this->uploadPdf('caminho_arquivo');
            }

            $this->documentoModel->update($id, $data);

            AuditLog::registrar('documento_atualizado', 'conteudos', [
                'documento_id' => $id,
                'titulo'       => $data['titulo'],
                'tipo'         => $data['tipo'],
            ], 'Documento público da transparência atualizado.');

            $_SESSION['success_msg'] = "Documento público atualizado com sucesso!";
            redirect('/admin/conteudos/documentos');
        } catch (Throwable $e) {
            $_SESSION['error_msg'] = "Erro ao atualizar documento: " . safeError($e);
            redirect('/admin/conteudos/documentos/edit?id=' . $id);
        }
    }

    public function deleteDocumento() {
        checkPermission(['admin_total', 'editor']);
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if ($id) {
            try {
                $doc = $this->documentoModel->find($id);
                $this->documentoModel->delete($id);

                AuditLog::registrar('documento_excluido', 'conteudos', [
                    'documento_id' => $id,
                    'titulo'       => $doc['titulo'] ?? ''
                ], 'Documento público excluído da transparência.');

                $_SESSION['success_msg'] = "Documento público excluído com sucesso!";
            } catch (Throwable $e) {
                $_SESSION['error_msg'] = "Erro ao excluir documento: " . safeError($e);
            }
        }
        redirect('/admin/conteudos/documentos');
    }

    private function uploadPdf($inputName) {
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        // Valida pelo conteúdo real do arquivo, não só pela extensão
        $ext = validate_upload($_FILES[$inputName], ['application/pdf' => 'pdf'], 10 * 1024 * 1024, 'arquivo PDF');
        return $this->moveToPublicUploads($_FILES[$inputName], random_filename('doc_', $ext));
    }

    // ==========================================
    // AUXILIARES
    // ==========================================

    private function uploadFile($inputName) {
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
        ];
        $ext = validate_upload($_FILES[$inputName], $allowed, 5 * 1024 * 1024, 'imagem');
        return $this->moveToPublicUploads($_FILES[$inputName], random_filename('content_', $ext));
    }

    /** Conteúdo público (fotos, notícias, atas) continua em /public/uploads, mas sem permissão de execução. */
    private function moveToPublicUploads(array $file, string $newFileName) {
        $uploadDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $destination = $uploadDir . $newFileName;
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            @chmod($destination, 0644);
            return 'uploads/' . $newFileName;
        }
        throw new AppException("Falha ao salvar o arquivo enviado no servidor.");
    }
}
