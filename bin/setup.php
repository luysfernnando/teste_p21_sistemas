<?php

// Define o diretório raiz do projeto
$rootDir = __DIR__;

// Lista de diretórios para criar
$directories = [
    'public/uploads',
    'public/uploads/products',
    'logs',
    'cache',
    'tmp'
];

// Cria os diretórios
foreach ($directories as $dir) {
    $path = $rootDir . '/' . $dir;
    if (!is_dir($path)) {
        if (mkdir($path, 0777, true)) {
            echo "Diretório criado: {$dir}\n";
            // Cria um arquivo .gitkeep para manter o diretório no git
            file_put_contents($path . '/.gitkeep', '');
        } else {
            echo "Erro ao criar diretório: {$dir}\n";
        }
    } else {
        echo "Diretório já existe: {$dir}\n";
    }
}

// Define permissões para o diretório de uploads
$uploadsDir = $rootDir . '/public/uploads';
if (is_dir($uploadsDir)) {
    chmod($uploadsDir, 0777);
    chmod($uploadsDir . '/products', 0777);
    echo "Permissões definidas para o diretório de uploads\n";
}

echo "\nSetup concluído!\n"; 