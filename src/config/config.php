<?php

// Configurações do Banco de Dados
define('DB_HOST', 'mysql');
define('DB_NAME', 'p21_sistemas');
define('DB_USER', 'p21_user');
define('DB_PASS', 'p21_pass');

// Configurações da Aplicação
define('APP_NAME', 'Loja Mágica de Tecnologia');
define('APP_URL', 'http://localhost');
define('APP_VERSION', '1.0.0');

// Diretórios
define('ROOT_DIR', dirname(__DIR__, 2));
define('UPLOAD_DIR', ROOT_DIR . '/uploads');
define('TEMPLATE_DIR', ROOT_DIR . '/src/views');

// Configurações de E-mail
define('MAIL_HOST', 'smtp.example.com');
define('MAIL_PORT', 587);
define('MAIL_USER', 'seu-email@example.com');
define('MAIL_PASS', 'sua-senha');
define('MAIL_FROM', 'noreply@lojamagica.com');
define('MAIL_FROM_NAME', 'Loja Mágica de Tecnologia');

// Configurações de Debug
define('DEBUG_MODE', true);
error_reporting(DEBUG_MODE ? E_ALL : 0);
ini_set('display_errors', DEBUG_MODE ? 1 : 0);

// Incluir a classe Database
require_once __DIR__ . '/Database.php';

// Funções de Utilidade
function dd($data) {
    if (!DEBUG_MODE) return;
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

// Função para carregar classes automaticamente
spl_autoload_register(function ($class) {
    $prefix = '';
    $base_dir = ROOT_DIR . '/src/';
    
    $file = $base_dir . str_replace('\\', '/', $class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
}); 