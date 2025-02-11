<?php require_once ROOT_DIR . '/src/views/layouts/header.php'; ?>

<div class="container-fluid pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-box text-primary"></i>
            Produtos
        </h1>
        <a href="<?= APP_URL ?>/produtos/novo" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>
            Novo Produto
        </a>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['flash']['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?php if (empty($products)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-box text-muted mb-3" style="font-size: 48px;"></i>
                    <h4 class="text-muted">Nenhum produto cadastrado</h4>
                    <p class="text-muted">Comece cadastrando um novo produto.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4 col-lg-3 mb-4">
                            <div class="card h-100">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="<?= APP_URL ?>/<?= htmlspecialchars($product['image']) ?>" 
                                         class="card-img-top" 
                                         alt="<?= htmlspecialchars($product['name']) ?>"
                                         style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                         style="height: 200px;">
                                        <i class="fas fa-image text-muted fa-3x"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                    <p class="card-text text-muted">
                                        <?= !empty($product['description']) 
                                            ? htmlspecialchars(substr($product['description'], 0, 100)) . '...'
                                            : '<em>Sem descrição</em>' ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="h5 mb-0 text-primary">
                                            R$ <?= number_format($product['price'], 2, ',', '.') ?>
                                        </span>
                                        <span class="badge bg-<?= $product['stock'] > 0 ? 'success' : 'danger' ?>">
                                            <?php
                                            if ($product['stock'] == 0) {
                                                echo 'Sem Estoque';
                                            } elseif ($product['stock'] == 1) {
                                                echo '1 Unidade';
                                            } else {
                                                echo $product['stock'] . ' Unidades';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <div class="d-flex gap-2">
                                        <a href="<?= APP_URL ?>/produtos/editar/<?= $product['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary flex-grow-1">
                                            <i class="fas fa-edit me-1"></i>
                                            Editar
                                        </a>
                                        <form action="<?= APP_URL ?>/produtos/excluir/<?= $product['id'] ?>" 
                                              method="post" 
                                              class="flex-grow-1"
                                              onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                <i class="fas fa-trash me-1"></i>
                                                Excluir
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once ROOT_DIR . '/src/views/layouts/footer.php'; ?> 