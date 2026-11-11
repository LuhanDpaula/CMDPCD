<?php

class UsuarioController {
    private $usuarioModel;
    private $pcdModel;

    public function __construct() {
        checkAuth(); // Protege todas as ações deste controller
        $this->usuarioModel = new Usuario();
        $this->pcdModel = new Pcd();
    }

    /**
     * Dashboard Principal (aba Dashboard).
     */
    public function dashboard() {
        // Estatísticas básicas
        $totalPcds = $this->pcdModel->countApproved();
        $totalUsuarios = count($this->usuarioModel->all());

        // Estatísticas de Mensagens
        $contatoModel = new Contato();
        $totalMensagens = count($contatoModel->all());
        $mensagensNaoLidas = $contatoModel->getUnreadCount();

        // Contagens por tipo de deficiência
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT td.nome, COUNT(p.id) as total 
            FROM tipos_deficiencia td
            JOIN pcd_deficiencias pd ON td.id = pd.tipo_deficiencia_id
            JOIN pcds p ON pd.pcd_id = p.id
            WHERE p.status = 'deferido' AND p.deletado_em IS NULL
            GROUP BY td.id, td.nome
            ORDER BY total DESC
        ");
        $deficienciasStats = $stmt->fetchAll();

        view('admin/dashboard', [
            'activeTab' => 'dashboard',
            'totalPcds' => $totalPcds,
            'totalUsuarios' => $totalUsuarios,
            'totalMensagens' => $totalMensagens,
            'mensagensNaoLidas' => $mensagensNaoLidas,
            'deficienciasStats' => $deficienciasStats
        ]);
    }

    /**
     * Estatísticas detalhadas de PCDs (aba Estatísticas).
     */
    public function estatisticas() {
        $stats = $this->pcdModel->getDetailedStats();
        
        view('admin/estatisticas', [
            'activeTab' => 'estatisticas',
            'stats' => $stats
        ]);
    }

    /**
     * Lista de Usuários (aba Usuários).
     */
    public function index() {
        checkPermission(['admin_total', 'admin_parcial']);
        $usuarios = $this->usuarioModel->all();
        
        $success = $_SESSION['success_msg'] ?? null;
        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);

        view('admin/usuarios/index', [
            'activeTab' => 'users',
            'usuarios' => $usuarios,
            'success' => $success,
            'error' => $error
        ]);
    }

    /**
     * Exibe formulário de criação de usuário.
     */
    public function create() {
        checkPermission(['admin_total']);
        $db = Database::getConnection();
        $perfis = $db->query("SELECT * FROM perfis_acesso ORDER BY nome ASC")->fetchAll();

        $error = $_SESSION['error_msg'] ?? null;
        $old = $_SESSION['old_input'] ?? [];
        unset($_SESSION['error_msg'], $_SESSION['old_input']);

        view('admin/usuarios/create', [
            'activeTab' => 'users',
            'perfis' => $perfis,
            'error' => $error,
            'old' => $old
        ]);
    }

    /**
     * Salva o novo usuário (POST).
     */
    public function store() {
        checkPermission(['admin_total']);
        $data = [
            'nome_completo' => trim($_POST['nome_completo'] ?? ''),
            'cpf' => trim($_POST['cpf'] ?? ''),
            'celular' => trim($_POST['celular'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'senha' => $_POST['senha'] ?? '',
            'senha_confirma' => $_POST['senha_confirma'] ?? '',
            'perfil_id' => (int)($_POST['perfil_id'] ?? 0) ?: '',
            'status' => in_list($_POST['status'] ?? 'ativo', ['ativo', 'inativo', 'bloqueado'], 'ativo')
        ];

        // Validações básicas
        if (empty($data['nome_completo']) || empty($data['cpf']) || empty($data['celular']) || empty($data['email']) || empty($data['senha']) || empty($data['perfil_id'])) {
            $_SESSION['error_msg'] = "Preencha todos os campos obrigatórios.";
            $_SESSION['old_input'] = array_diff_key($data, ['senha' => 1, 'senha_confirma' => 1]);
            redirect('/admin/usuarios/create');
        }

        if (strlen($data['senha']) < 8) {
            $_SESSION['error_msg'] = "A senha deve ter pelo menos 8 caracteres.";
            $_SESSION['old_input'] = array_diff_key($data, ['senha' => 1, 'senha_confirma' => 1]);
            redirect('/admin/usuarios/create');
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_msg'] = "E-mail inválido.";
            $_SESSION['old_input'] = array_diff_key($data, ['senha' => 1, 'senha_confirma' => 1]);
            redirect('/admin/usuarios/create');
        }

        if ($data['senha'] !== $data['senha_confirma']) {
            $_SESSION['error_msg'] = "As senhas não coincidem.";
            $_SESSION['old_input'] = array_diff_key($data, ['senha' => 1, 'senha_confirma' => 1]);
            redirect('/admin/usuarios/create');
        }

        if ($this->usuarioModel->existsCpf($data['cpf'])) {
            $_SESSION['error_msg'] = "Este CPF já está cadastrado.";
            $_SESSION['old_input'] = array_diff_key($data, ['senha' => 1, 'senha_confirma' => 1]);
            redirect('/admin/usuarios/create');
        }

        if ($this->usuarioModel->existsEmail($data['email'])) {
            $_SESSION['error_msg'] = "Este E-mail já está cadastrado.";
            $_SESSION['old_input'] = array_diff_key($data, ['senha' => 1, 'senha_confirma' => 1]);
            redirect('/admin/usuarios/create');
        }

        try {
            $this->usuarioModel->create($data);

            AuditLog::registrar('usuario_criado', 'usuarios', [
                'nome'      => $data['nome_completo'],
                'email'     => $data['email'],
                'perfil_id' => $data['perfil_id']
            ], 'Novo usuário administrativo cadastrado.');

            $_SESSION['success_msg'] = "Usuário cadastrado com sucesso!";
            redirect('/admin/usuarios');
        } catch (Throwable $e) {
            $_SESSION['error_msg'] = "Erro ao cadastrar usuário: " . safeError($e);
            $_SESSION['old_input'] = array_diff_key($data, ['senha' => 1, 'senha_confirma' => 1]);
            redirect('/admin/usuarios/create');
        }
    }

    /**
     * Exibe formulário de edição de usuário.
     */
    public function edit() {
        checkPermission(['admin_total', 'admin_parcial']);
        $id = $_GET['id'] ?? null;
        if (!$id) {
            redirect('/admin/usuarios');
        }

        $usuario = $this->usuarioModel->find($id);
        if (!$usuario) {
            $_SESSION['error_msg'] = "Usuário não encontrado.";
            redirect('/admin/usuarios');
        }

        $db = Database::getConnection();
        $perfis = $db->query("SELECT * FROM perfis_acesso ORDER BY nome ASC")->fetchAll();

        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['error_msg']);

        view('admin/usuarios/edit', [
            'activeTab' => 'users',
            'usuario' => $usuario,
            'perfis' => $perfis,
            'error' => $error
        ]);
    }

    /**
     * Salva as edições do usuário (POST).
     */
    public function update() {
        checkPermission(['admin_total']);
        $id = $_POST['id'] ?? null;
        if (!$id) {
            redirect('/admin/usuarios');
        }

        $data = [
            'nome_completo' => trim($_POST['nome_completo'] ?? ''),
            'cpf' => trim($_POST['cpf'] ?? ''),
            'celular' => trim($_POST['celular'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'senha' => $_POST['senha'] ?? '',
            'senha_confirma' => $_POST['senha_confirma'] ?? '',
            'perfil_id' => (int)($_POST['perfil_id'] ?? 0) ?: '',
            'status' => in_list($_POST['status'] ?? 'ativo', ['ativo', 'inativo', 'bloqueado'], 'ativo')
        ];

        // Validações básicas
        if (empty($data['nome_completo']) || empty($data['cpf']) || empty($data['celular']) || empty($data['email']) || empty($data['perfil_id'])) {
            $_SESSION['error_msg'] = "Preencha todos os campos obrigatórios.";
            redirect('/admin/usuarios/edit?id=' . $id);
        }

        if (!empty($data['senha']) && strlen($data['senha']) < 8) {
            $_SESSION['error_msg'] = "A senha deve ter pelo menos 8 caracteres.";
            redirect('/admin/usuarios/edit?id=' . $id);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_msg'] = "E-mail inválido.";
            redirect('/admin/usuarios/edit?id=' . $id);
        }

        if (!empty($data['senha']) && $data['senha'] !== $data['senha_confirma']) {
            $_SESSION['error_msg'] = "As senhas não coincidem.";
            redirect('/admin/usuarios/edit?id=' . $id);
        }

        if ($this->usuarioModel->existsCpf($data['cpf'], $id)) {
            $_SESSION['error_msg'] = "Este CPF já está em uso por outro usuário.";
            redirect('/admin/usuarios/edit?id=' . $id);
        }

        if ($this->usuarioModel->existsEmail($data['email'], $id)) {
            $_SESSION['error_msg'] = "Este E-mail já está em uso por outro usuário.";
            redirect('/admin/usuarios/edit?id=' . $id);
        }

        try {
            // Remove confirmação antes de passar pro model
            $updateData = $data;
            unset($updateData['senha_confirma']);

            $this->usuarioModel->update($id, $updateData);

            AuditLog::registrar('usuario_atualizado', 'usuarios', [
                'usuario_id' => $id,
                'nome'       => $data['nome_completo'],
                'email'      => $data['email'],
                'status'     => $data['status']
            ], 'Dados de usuário administrativo atualizados.');

            $_SESSION['success_msg'] = "Usuário atualizado com sucesso!";
            redirect('/admin/usuarios');
        } catch (Throwable $e) {
            $_SESSION['error_msg'] = "Erro ao atualizar usuário: " . safeError($e);
            redirect('/admin/usuarios/edit?id=' . $id);
        }
    }

    /**
     * Remove um usuário (soft delete).
     */
    public function delete() {
        checkPermission(['admin_total']);
        $id = $_POST['id'] ?? null;
        
        // Impede de se auto-excluir
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error_msg'] = "Você não pode excluir a si mesmo.";
            redirect('/admin/usuarios');
        }

        // Confirmação de segurança (Sudo Mode): exige a senha do administrador logado
        $adminSenha = (string)($_POST['admin_senha'] ?? '');
        $currentAdmin = $this->usuarioModel->find($_SESSION['user_id']);
        if (!$currentAdmin || empty($adminSenha) || !password_verify($adminSenha, $currentAdmin['senha'])) {
            AuditLog::registrar('alerta_seguranca_exclusao_falha', 'usuarios', [
                'usuario_alvo_id' => $id,
                'tentado_por'     => $_SESSION['user_id'] ?? null,
            ], 'Tentativa de exclusão de usuário bloqueada: senha de confirmação incorreta.');

            $_SESSION['error_msg'] = "Senha incorreta. A exclusão de usuário requer a sua senha de administrador para confirmação.";
            redirect('/admin/usuarios');
        }

        if ($id) {
            try {
                $u = $this->usuarioModel->find($id);
                $this->usuarioModel->delete($id);

                AuditLog::registrar('usuario_excluido', 'usuarios', [
                    'usuario_id' => $id,
                    'nome'       => $u['nome_completo'] ?? ''
                ], 'Usuário administrativo excluído (confirmado por senha).');

                $_SESSION['success_msg'] = "Usuário excluído com sucesso!";
            } catch (Throwable $e) {
                $_SESSION['error_msg'] = "Erro ao excluir usuário: " . safeError($e);
            }
        }
        redirect('/admin/usuarios');
    }

}
