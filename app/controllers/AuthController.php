<?php

class AuthController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    /**
     * Exibe o formulário de login.
     */
    public function loginForm() {
        // Se já estiver logado, redireciona para o painel
        if (isset($_SESSION['user_id'])) {
            redirect('/admin/dashboard');
        }

        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);

        view('admin/login', ['error' => $error]);
    }

    /**
     * Processa a tentativa de login (POST).
     */
    public function login() {
        $loginVal = limit_str($_POST['login'] ?? '', 150);
        $passwordVal = (string)($_POST['senha'] ?? '');   // senha NÃO deve sofrer trim

        // Proteção contra força bruta: no máximo 5 tentativas erradas a cada 15 minutos por IP
        $rlKey = 'login';
        if (!rate_limit_check($rlKey, 5, 15 * 60)) {
            $_SESSION['login_error'] = "Muitas tentativas de login. Aguarde 15 minutos e tente novamente.";
            redirect('/admin');
        }

        if ($loginVal === '' || $passwordVal === '') {
            $_SESSION['login_error'] = "Preencha todos os campos.";
            redirect('/admin');
        }

        // Tenta achar por email ou cpf (consulta preparada no model = sem SQL injection)
        $user = $this->usuarioModel->findByEmailOrCpf($loginVal);

        // password_verify roda mesmo quando o usuário não existe, para não revelar
        // pelo tempo de resposta se um e-mail/CPF está cadastrado
        $hash = $user['senha'] ?? '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
        $senhaOk = password_verify($passwordVal, $hash);

        if ($user && $senhaOk && $user['status'] === 'ativo') {
            rate_limit_clear($rlKey);

            // Evita "session fixation": gera um novo ID de sessão no login
            session_regenerate_id(true);

            // Atualiza o hash se o algoritmo padrão do PHP mudou
            if (password_needs_rehash($user['senha'], PASSWORD_DEFAULT)) {
                $this->usuarioModel->updatePasswordHash($user['id'], password_hash($passwordVal, PASSWORD_DEFAULT));
            }

            // Define variáveis de sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nome_completo'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_perfil_id'] = $user['perfil_id'];
            $_SESSION['user_perfil_nome'] = $user['perfil_nome'];
            $_SESSION['last_activity'] = time();
            unset($_SESSION['_csrf_token']); // novo token após login

            // Atualiza último login no banco
            $this->usuarioModel->updateUltimoLogin($user['id']);

            // Auditoria
            AuditLog::registrar('login_sucesso', 'auth', [
                'usuario_id' => $user['id'],
                'email'      => $user['email'],
                'perfil'     => $user['perfil_nome']
            ], 'Login realizado com sucesso no painel administrativo.');

            redirect('/admin/dashboard');
        }

        rate_limit_hit($rlKey, 15 * 60);

        // Auditoria de falha de login (segurança)
        AuditLog::registrar('login_falha', 'auth', [
            'identificador_tentado' => $loginVal,
        ], 'Falha na autenticação (credenciais inválidas ou usuário inativo).');

        // Mensagem única: não revela se o usuário existe, se está inativo ou se só a senha errou
        $_SESSION['login_error'] = "E-mail/CPF ou senha incorretos.";
        redirect('/admin');
    }

    /**
     * Encerra a sessão (Logout).
     */
    public function logout() {
        AuditLog::registrar('logout', 'auth', [], 'Sessão administrativa finalizada (logout).');

        // Limpa todas as variáveis de sessão
        $_SESSION = [];

        // Destrói o cookie de sessão se houver
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destrói a sessão
        session_destroy();

        redirect('/admin');
    }
}
