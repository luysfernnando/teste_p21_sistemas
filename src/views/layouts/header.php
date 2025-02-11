<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?= APP_URL ?>/css/style.css" rel="stylesheet">

    <style>
        .nav-link {
            position: relative;
            color: rgba(255, 255, 255, 0.85) !important;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #fff !important;
        }

        .nav-link.active {
            color: #fff !important;
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #fff;
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?= APP_URL ?>">
                <i class="fas fa-magic me-2"></i>
                <?= APP_NAME ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <?php
                    $current_page = $_SERVER['REQUEST_URI'];
                    $current_page = strtok($current_page, '?'); // Remove query string
                    $current_page = trim($current_page, '/'); // Remove trailing slashes
                    $current_page = explode('/', $current_page)[0]; // Get first segment
                    
                    $menu_items = [
                        '' => ['icon' => 'fas fa-home', 'text' => 'Início'],
                        'clientes' => ['icon' => 'fas fa-users', 'text' => 'Clientes'],
                        'produtos' => ['icon' => 'fas fa-box', 'text' => 'Produtos'],
                        'pedidos' => ['icon' => 'fas fa-shopping-cart', 'text' => 'Pedidos'],
                        'comunicados' => ['icon' => 'fas fa-envelope', 'text' => 'Comunicados'],
                        'integracao' => ['icon' => 'fas fa-plug', 'text' => 'Integração']
                    ];

                    foreach ($menu_items as $route => $item):
                        $is_active = ($current_page === $route) || 
                                   ($current_page === '' && $route === '');
                    ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $is_active ? 'active' : '' ?>" 
                               href="<?= APP_URL ?>/<?= $route ?>">
                                <i class="<?= $item['icon'] ?> me-1"></i>
                                <?= $item['text'] ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container">
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show">
                <?= $_SESSION['flash']['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?> 