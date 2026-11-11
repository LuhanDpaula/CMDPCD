<?php

// Obtém a URI da rota pré-calculada pelo Front Controller
if (isset($routeUri)) {
    $requestUri = $routeUri;
} else {
    // Fallback caso o roteador seja incluído isoladamente
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (defined('BASE_URL') && BASE_URL !== '' && strpos($requestUri, BASE_URL) === 0) {
        $requestUri = substr($requestUri, strlen(BASE_URL));
    }
}

// Normaliza o caminho: garante que comece com '/' e não termine com '/' (exceto se for '/')
$requestUri = '/' . trim($requestUri, '/');
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Mapa de rotas do sistema
$routes = [
    'GET' => [
        '/' => 'HomeController@index',
        '/index.html' => 'HomeController@index',
        '/quem-somos' => 'HomeController@quemSomos',
        '/quem-somos.html' => 'HomeController@quemSomos',
        '/iniciativas' => 'HomeController@iniciativas',
        '/iniciativas.html' => 'HomeController@iniciativas',
        '/noticias' => 'HomeController@noticias',
        '/noticias.html' => 'HomeController@noticias',
        '/noticia' => 'HomeController@noticia',
        '/noticia.html' => 'HomeController@noticia',
        '/transparencia' => 'HomeController@transparencia',
        '/transparencia.html' => 'HomeController@transparencia',
        '/apoio' => 'HomeController@apoio',
        '/apoio.html' => 'HomeController@apoio',
        '/contato' => 'HomeController@contato',
        '/contato.html' => 'HomeController@contato',
        '/cadastro-pcd' => 'PcdController@cadastro',
        '/cadastro-pcd.html' => 'PcdController@cadastro',
        
        '/admin' => 'AuthController@loginForm',
        '/admin.html' => 'AuthController@loginForm',
        '/admin/dashboard' => 'UsuarioController@dashboard',
        
        '/admin/usuarios' => 'UsuarioController@index',
        '/admin/usuarios/create' => 'UsuarioController@create',
        '/admin/usuarios/edit' => 'UsuarioController@edit',
        '/admin/conteudos' => 'ConteudoController@index',
        '/admin/conteudos/noticias' => 'ConteudoController@indexNoticias',
        '/admin/conteudos/noticias/create' => 'ConteudoController@createNoticia',
        '/admin/conteudos/noticias/edit' => 'ConteudoController@editNoticia',
        '/admin/conteudos/membros' => 'ConteudoController@indexMembros',
        '/admin/conteudos/membros/create' => 'ConteudoController@createMembro',
        '/admin/conteudos/membros/edit' => 'ConteudoController@editMembro',
        '/admin/conteudos/documentos' => 'ConteudoController@indexDocumentos',
        '/admin/conteudos/documentos/create' => 'ConteudoController@createDocumento',
        '/admin/conteudos/documentos/edit' => 'ConteudoController@editDocumento',
        
        '/admin/mensagens' => 'MensagemController@index',
        '/admin/estatisticas' => 'UsuarioController@estatisticas',
        
        '/admin/pcds' => 'PcdController@index',
        '/admin/pcds/create' => 'PcdController@create',
        '/admin/pcds/edit' => 'PcdController@edit',
        '/admin/pcds/documento' => 'PcdController@documento',

        // Chat "Posso ajudar?" (MongoDB)
        '/chat/categorias' => 'ChatController@categorias',
        '/chat/perguntas' => 'ChatController@perguntas',
        '/chat/resposta' => 'ChatController@resposta',
        '/chat/buscar' => 'ChatController@buscar',

        // Perguntas frequentes no painel (MongoDB) - admin_total e editor
        '/admin/conteudos/faq' => 'FaqController@index',
        '/admin/conteudos/faq/create' => 'FaqController@create',
        '/admin/conteudos/faq/edit' => 'FaqController@edit',
        '/admin/conteudos/faq/sem-resposta' => 'FaqController@semResposta',

        // Pedidos de ajuda (MongoDB) - somente admin_total
        '/admin/pedidos' => 'PedidoController@index',
        '/admin/pedidos/ver' => 'PedidoController@ver',

        // Auditoria e Logs (MongoDB + Fallback) - somente admin_total
        '/admin/logs' => 'LogController@index',
    ],
    'POST' => [
        '/cadastro-pcd' => 'PcdController@store',
        '/contato/enviar' => 'HomeController@submitContato',
        '/admin/login' => 'AuthController@login',
        '/admin/logout' => 'AuthController@logout',
        '/admin/usuarios/store' => 'UsuarioController@store',
        '/admin/usuarios/update' => 'UsuarioController@update',
        '/admin/usuarios/delete' => 'UsuarioController@delete',
        '/admin/pcds/store' => 'PcdController@storeAdmin',
        '/admin/pcds/update' => 'PcdController@update',
        '/admin/pcds/delete' => 'PcdController@delete',
        '/admin/pcds/deferir' => 'PcdController@deferir',
        '/admin/pcds/indeferir' => 'PcdController@indeferir',
        '/admin/conteudos/noticias/store' => 'ConteudoController@storeNoticia',
        '/admin/conteudos/noticias/update' => 'ConteudoController@updateNoticia',
        '/admin/conteudos/noticias/delete' => 'ConteudoController@deleteNoticia',
        '/admin/conteudos/membros/store' => 'ConteudoController@storeMembro',
        '/admin/conteudos/membros/update' => 'ConteudoController@updateMembro',
        '/admin/conteudos/membros/delete' => 'ConteudoController@deleteMembro',
        '/admin/conteudos/documentos/store' => 'ConteudoController@storeDocumento',
        '/admin/conteudos/documentos/update' => 'ConteudoController@updateDocumento',
        '/admin/conteudos/documentos/delete' => 'ConteudoController@deleteDocumento',
        '/admin/mensagens/lido' => 'MensagemController@markAsRead',
        '/admin/mensagens/delete' => 'MensagemController@delete',
        '/chat/avaliar' => 'ChatController@avaliar',
        '/chat/pedido' => 'ChatController@pedido',
        '/admin/conteudos/faq/store' => 'FaqController@store',
        '/admin/conteudos/faq/update' => 'FaqController@update',
        '/admin/conteudos/faq/toggle' => 'FaqController@toggle',
        '/admin/conteudos/faq/delete' => 'FaqController@delete',
        '/admin/conteudos/faq/sem-resposta/descartar' => 'FaqController@descartarSemResposta',
        '/admin/pedidos/registrar' => 'PedidoController@registrar',
        '/admin/pedidos/prioridade' => 'PedidoController@prioridade',
        '/admin/pedidos/excluir' => 'PedidoController@excluir',
    ]
];

// ============================================================
// PROTEÇÃO CSRF
// ------------------------------------------------------------
// Todas as ações de mutação de estado (exclusão, deferimento,
// logout e alteração) são executadas exclusivamente via POST
// com token no corpo ou cabeçalho HTTP (X-CSRF-Token).
// Isso impede vazamento de tokens em URLs GET e ataques via crawler/prefetch.
// ============================================================
$needsCsrf = ($requestMethod === 'POST');

if ($needsCsrf && !csrf_verify()) {
    http_response_code(403);
    $wantsJson = stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false
        || !empty($_SERVER['HTTP_X_REQUESTED_WITH']);
    if ($wantsJson) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'status' => 'error', 'message' => 'Sua sessão expirou. Recarregue a página e tente novamente.']);
        exit;
    }
    $_SESSION['error_msg'] = 'Sua sessão expirou ou a ação não pôde ser confirmada. Tente novamente.';
    $_SESSION['login_error'] = 'Sua sessão expirou. Tente novamente.';
    $back = isset($_SESSION['user_id']) ? '/admin/dashboard' : '/admin';
    if ($requestUri === '/cadastro-pcd') $back = '/cadastro-pcd';
    redirect($back);
}

// Roteamento
if (isset($routes[$requestMethod][$requestUri])) {
    $action = $routes[$requestMethod][$requestUri];
    list($controllerName, $method) = explode('@', $action);
    
    // Instancia o Controller e executa o método
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        if (method_exists($controller, $method)) {
            $controller->$method();
        } else {
            throw new Exception("Método $method não encontrado no controller $controllerName.");
        }
    } else {
        throw new Exception("Controller $controllerName não encontrado.");
    }
} elseif ($requestMethod === 'GET' && preg_match('#^/noticia/([0-9]+)$#', $requestUri, $matches)) {
    $_GET['id'] = $matches[1];
    $controller = new HomeController();
    $controller->noticia();
} elseif ($requestMethod === 'GET' && isset($routes['POST'][$requestUri])) {
    // Bloqueia tentativas de invocar ações mutáveis diretamente via barra de navegação/GET
    http_response_code(405);
    $_SESSION['error_msg'] = 'Método não permitido para esta operação. Use o botão do painel administrativo.';
    redirect('/admin/dashboard');
} else {
    // Rota não encontrada - renderiza 404
    http_response_code(404);
    view('404');
}
