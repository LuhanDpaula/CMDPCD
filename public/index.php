<?php
// ============================================================
// CONFIGURAÇÕES DE SEGURANÇA GLOBAIS
// ============================================================

// Nunca mostrar erros/stack traces para o visitante (vazariam caminhos, SQL, etc.)
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

// Cabeçalhos de segurança HTTP
header_remove('X-Powered-By');
header('X-Content-Type-Options: nosniff');          // impede o navegador de "adivinhar" tipo de arquivo
header('X-Frame-Options: DENY');                    // impede o site de ser embutido em iframe (clickjacking)
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? '') == 443);
if ($isHttps) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// Sessão segura: cookie inacessível via JavaScript, só no mesmo site e só por HTTPS quando disponível
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('CMDPCDSESSID');
    session_start();
}

// Expira sessão administrativa após 30 minutos de inatividade
if (isset($_SESSION['user_id'])) {
    $timeout = 30 * 60;
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
        $_SESSION = [];
        session_regenerate_id(true);
        $_SESSION['login_error'] = 'Sua sessão expirou por inatividade. Faça login novamente.';
    }
    $_SESSION['last_activity'] = time();
}

require_once __DIR__ . '/../app/helpers/security.php';
require_once __DIR__ . '/../config/Mongo.php';

// Autoloader para carregar classes automaticamente (Models, Controllers, Config)
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../config/',
        __DIR__ . '/../app/controllers/',
        __DIR__ . '/../app/models/'
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Define a BASE_URL e a URI de rota dinamicamente para suportar execução em subdiretórios,
// domínios raiz, acessos diretos a index.php e rewrites do Apache (.htaccess)
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = $_SERVER['SCRIPT_NAME']; // ex: /CMDPCD_SITE/public/index.php
$scriptDir = rtrim(dirname($scriptName), '/\\'); // ex: /CMDPCD_SITE/public
$projectRootDir = rtrim(dirname($scriptDir), '/\\'); // ex: /CMDPCD_SITE

if (strpos($requestUri, $scriptName) === 0) {
    $baseUrl = $scriptName;
    $routeUri = substr($requestUri, strlen($scriptName));
} elseif (strpos($requestUri, $scriptDir) === 0) {
    $baseUrl = $scriptDir;
    $routeUri = substr($requestUri, strlen($scriptDir));
} elseif ($projectRootDir !== '' && strpos($requestUri, $projectRootDir) === 0) {
    $baseUrl = $projectRootDir;
    $routeUri = substr($requestUri, strlen($projectRootDir));
} else {
    $baseUrl = '';
    $routeUri = $requestUri;
}

define('BASE_URL', $baseUrl);

// Função auxiliar global para renderizar views
function view($viewName, $data = []) {
    extract($data);
    $viewFile = __DIR__ . '/../app/views/' . $viewName . '.php';
    if (file_exists($viewFile)) {
        require $viewFile;
    } else {
        // Redireciona para 404
        require __DIR__ . '/../app/views/404.php';
    }
}

// Helper para redirecionamento fácil
function redirect($path) {
    header("Location: " . BASE_URL . $path);
    exit;
}

// Helper para verificar se está autenticado
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        redirect('/admin');
    }

    // Invalidação ativa de sessão: verifica se o usuário continua ativo e não foi excluído
    $lastCheck = $_SESSION['user_active_check'] ?? 0;
    if (time() - $lastCheck > 60) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT status, perfil_id FROM usuarios WHERE id = ? AND deletado_em IS NULL");
            $stmt->execute([(int)$_SESSION['user_id']]);
            $row = $stmt->fetch();
            if (!$row || $row['status'] !== 'ativo') {
                $_SESSION = [];
                if (ini_get("session.use_cookies")) {
                    $p = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
                }
                session_destroy();
                session_start();
                $_SESSION['login_error'] = 'Sua sessão foi encerrada porque sua conta foi desativada ou alterada pelo administrador.';
                redirect('/admin');
            }
            // Sincroniza perfil caso tenha havido alteração administrativa
            $_SESSION['user_perfil_id'] = (int)$row['perfil_id'];
            $_SESSION['user_active_check'] = time();
        } catch (Throwable $e) {}
    }

    // Páginas do painel contêm dados pessoais: não deixar o navegador/proxy guardar em cache
    header('Cache-Control: no-store, no-cache, must-revalidate, private');
    header('Pragma: no-cache');
}

// Helpers de Perfil e Controle de Acesso
function getPerfilUsuario() {
    if (isset($_SESSION['user_perfil_nome'])) {
        return $_SESSION['user_perfil_nome'];
    }
    if (isset($_SESSION['user_perfil_id'])) {
        $map = [1 => 'admin_total', 2 => 'admin_parcial', 3 => 'editor'];
        return $map[$_SESSION['user_perfil_id']] ?? '';
    }
    return '';
}

function isAdminTotal() {
    return getPerfilUsuario() === 'admin_total';
}

function isAdminParcial() {
    return getPerfilUsuario() === 'admin_parcial';
}

function isEditor() {
    return getPerfilUsuario() === 'editor';
}

function checkPermission($allowedRoles) {
    checkAuth();
    $perfil = getPerfilUsuario();
    if (!in_array($perfil, $allowedRoles)) {
        $_SESSION['error_msg'] = "Você não tem permissão para acessar esta página.";
        redirect('/admin/dashboard');
    }
}

// Carrega as rotas (qualquer erro inesperado vira página genérica, sem expor detalhes)
try {
    require_once __DIR__ . '/../routes/web.php';
} catch (Throwable $e) {
    error_log('[CMDPCD] Erro não tratado: ' . $e->getMessage() . ' em ' . $e->getFile() . ':' . $e->getLine());
    if (!headers_sent()) {
        http_response_code(500);
    }
    echo '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><title>Erro</title></head><body style="font-family:sans-serif;text-align:center;padding:4rem">'
       . '<h1>Ops! Algo deu errado.</h1><p>Tente novamente em instantes.</p><p><a href="' . e(BASE_URL) . '/">Voltar ao início</a></p></body></html>';
}
