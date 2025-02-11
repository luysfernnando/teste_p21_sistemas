<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container h-100">
    <div class="row align-items-center justify-content-center" style="min-height: calc(100vh - 200px);">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="text-center">
                <div class="mb-4">
                    <i class="fas fa-ghost text-muted" style="font-size: 100px;"></i>
                </div>
                <h1 class="display-1 text-muted">404</h1>
                <h2 class="mb-4">Página não encontrada</h2>
                <p class="lead text-muted mb-4">
                    Ops! Parece que a página que você está procurando desapareceu misteriosamente...
                </p>
                <a href="<?= APP_URL ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-home me-2"></i>
                    Voltar para o Início
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?> 