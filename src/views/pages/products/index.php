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

<?php if (empty($products)): ?>
    <div class="text-center py-5">
        <i class="fas fa-box text-muted mb-3" style="font-size: 48px;"></i>
        <h4 class="text-muted">Nenhum produto cadastrado</h4>
        <p class="text-muted">Comece cadastrando um novo produto.</p>
    </div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
        <?php foreach ($products as $product): ?>
            <div class="col">
                <div class="card h-100">
                    <!-- Imagem do Produto -->
                    <div class="card-img-top position-relative" style="height: 200px; background-color: #f8f9fa;">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?= APP_URL ?>/<?= htmlspecialchars($product['image']) ?>" 
                                 class="w-100 h-100 object-fit-cover" 
                                 alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php else: ?>
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                <i class="fas fa-image text-muted" style="font-size: 48px;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Badge de Estoque -->
                        <div class="position-absolute top-0 end-0 m-2">
                            <?php if ($product['stock'] > 0): ?>
                                <span class="badge bg-success">Em Estoque: <?= $product['stock'] ?></span>
                            <?php else: ?>
                                <span class="badge bg-danger">Fora de Estoque</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="card-text text-muted small">
                            <?= nl2br(htmlspecialchars(substr($product['description'], 0, 100))) ?>
                            <?= strlen($product['description']) > 100 ? '...' : '' ?>
                        </p>
                        <h4 class="text-primary mb-3">
                            R$ <?= number_format($product['price'], 2, ',', '.') ?>
                        </h4>
                        
                        <div class="d-flex gap-2">
                            <a href="<?= APP_URL ?>/produtos/editar/<?= $product['id'] ?>" 
                               class="btn btn-info flex-grow-1">
                                <i class="fas fa-edit me-1"></i>
                                Editar
                            </a>
                            <form action="<?= APP_URL ?>/produtos/excluir/<?= $product['id'] ?>" 
                                  method="post" 
                                  class="d-inline-block flex-grow-1"
                                  onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                                <button type="submit" 
                                        class="btn btn-danger w-100">
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