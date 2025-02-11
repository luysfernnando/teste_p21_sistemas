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

// Configurações de E-mail
define('SMTP_HOST', 'mailhog');
define('SMTP_PORT', 1025);
define('SMTP_USER', '');  // MailHog não precisa de autenticação
define('SMTP_PASS', '');  // MailHog não precisa de autenticação
define('SMTP_FROM_EMAIL', 'noreply@p21sistemas.com.br');
define('SMTP_FROM_NAME', 'P21 Sistemas');
define('COMPANY_NAME', 'P21 Sistemas');

// Configurações de Debug
define('DEBUG_MODE', true);
error_reporting(DEBUG_MODE ? E_ALL : 0);
ini_set('display_errors', DEBUG_MODE ? 1 : 0);

// Configuração do fuso horário
date_default_timezone_set('America/Sao_Paulo');

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
    // Lista de diretórios onde procurar as classes
    $directories = [
        ROOT_DIR . '/src/controllers/',
        ROOT_DIR . '/src/models/',
        ROOT_DIR . '/src/utils/',
        ROOT_DIR . '/src/config/'
    ];

    // Procura a classe em cada diretório
    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
}); 