<?php
ob_start();
session_start();

error_log("Request URI: " . $_SERVER['REQUEST_URI']);
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST Data: " . print_r($_POST, true));

require_once __DIR__ . '/../src/config/config.php';
require_once ROOT_DIR . '/src/controllers/BaseController.php';

// Função para limpar a URL
function cleanUrl($url) {
    return strtolower(trim($url, '/'));
}

// Obter a URL atual
$url = isset($_GET['url']) ? cleanUrl($_GET['url']) : '';
if (empty($url) && isset($_SERVER['REQUEST_URI'])) {
    $url = cleanUrl(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
}

// Definir rotas
$routes = [
    '' => ['controller' => 'HomeController', 'action' => 'index'],
    'clientes' => ['controller' => 'CustomerController', 'action' => 'index'],
    'clientes/importar' => ['controller' => 'CustomerController', 'action' => 'import'],
    'clientes/novo' => ['controller' => 'CustomerController', 'action' => 'create'],
    'clientes/editar/(\d+)' => ['controller' => 'CustomerController', 'action' => 'edit'],
    'clientes/excluir/(\d+)' => ['controller' => 'CustomerController', 'action' => 'delete'],
    'produtos' => ['controller' => 'ProductController', 'action' => 'index'],
    'produtos/novo' => ['controller' => 'ProductController', 'action' => 'create'],
    'produtos/editar/(\d+)' => ['controller' => 'ProductController', 'action' => 'edit'],
    'produtos/excluir/(\d+)' => ['controller' => 'ProductController', 'action' => 'delete'],
    'pedidos' => ['controller' => 'OrderController', 'action' => 'index'],
    'pedidos/novo' => ['controller' => 'OrderController', 'action' => 'create'],
    'pedidos/importar' => ['controller' => 'OrderController', 'action' => 'import'],
    'pedidos/visualizar/(\d+)' => ['controller' => 'OrderController', 'action' => 'view'],
    'pedidos/status/(\d+)' => ['controller' => 'OrderController', 'action' => 'updateStatus'],
    'integracao' => ['controller' => 'IntegrationController', 'action' => 'index'],
    'integracao/receber' => ['controller' => 'IntegrationController', 'action' => 'receive'],
    'integracao/visualizar/(\d+)' => ['controller' => 'IntegrationController', 'action' => 'view'],
    'integracao/reprocessar/(\d+)' => ['controller' => 'IntegrationController', 'action' => 'reprocess'],
    'integracao/excluir/(\d+)' => ['controller' => 'IntegrationController', 'action' => 'delete'],
    'comunicados' => ['controller' => 'CommunicationController', 'action' => 'index'],
    'comunicados/criar-template' => ['controller' => 'CommunicationController', 'action' => 'createTemplate'],
    'comunicados/editar-template/(\d+)' => ['controller' => 'CommunicationController', 'action' => 'editTemplate'],
    'comunicados/enviar-email' => ['controller' => 'CommunicationController', 'action' => 'sendEmail'],
    'comunicados/historico' => ['controller' => 'CommunicationController', 'action' => 'history'],
    'comunicados/prever-template' => ['controller' => 'CommunicationController', 'action' => 'previewTemplate'],
    'comunicados/salvar-configuracoes' => ['controller' => 'CommunicationController', 'action' => 'saveSettings']
];

// Verificar se a rota existe
$routeFound = false;
foreach ($routes as $pattern => $route) {
    $pattern = str_replace('/', '\/', $pattern);
    if (preg_match('/^' . $pattern . '$/', $url, $matches)) {
        array_shift($matches); // Remove o match completo
        $routeFound = true;
        $controllerName = $route['controller'];
        $actionName = $route['action'];
        break;
    }
}

if (!$routeFound) {
    header("HTTP/1.0 404 Not Found");
    require_once TEMPLATE_DIR . '/404.php';
    exit;
}

// Carregar o controller
$controllerFile = ROOT_DIR . "/src/controllers/{$controllerName}.php";

if (!file_exists($controllerFile)) {
    die("Controller não encontrado: {$controllerName}");
}

require_once $controllerFile;

// Instanciar o controller e chamar a action
$controller = new $controllerName();
if (!method_exists($controller, $actionName)) {
    die("Action não encontrada: {$actionName}");
}

// Executar a action com os parâmetros capturados da URL
$controller->$actionName(...$matches); 