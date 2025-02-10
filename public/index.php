<?php
require_once __DIR__ . '/../src/config/config.php';
require_once ROOT_DIR . '/src/controllers/BaseController.php';

// Função para limpar a URL
function cleanUrl($url) {
    return strtolower(trim($url, '/'));
}

// Obter a URL atual
$url = isset($_GET['url']) ? cleanUrl($_GET['url']) : '';

// Definir rotas
$routes = [
    '' => ['controller' => 'HomeController', 'action' => 'index'],
    'clientes' => ['controller' => 'CustomerController', 'action' => 'index'],
    'clientes/importar' => ['controller' => 'CustomerController', 'action' => 'import'],
    'pedidos' => ['controller' => 'OrderController', 'action' => 'index'],
    'pedidos/novo' => ['controller' => 'OrderController', 'action' => 'create'],
    'produtos' => ['controller' => 'ProductController', 'action' => 'index'],
    'integracao' => ['controller' => 'IntegrationController', 'action' => 'index']
];

// Verificar se a rota existe
if (!isset($routes[$url])) {
    header("HTTP/1.0 404 Not Found");
    require_once TEMPLATE_DIR . '/404.php';
    exit;
}

// Obter controller e action
$route = $routes[$url];
$controllerName = $route['controller'];
$actionName = $route['action'];

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

// Executar a action
$controller->$actionName(); 