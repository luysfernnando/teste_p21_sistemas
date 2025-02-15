    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-cart-shopping text-primary"></i>
            Pedidos
        </h1>
        <div>
            <a href="<?= APP_URL ?>/pedidos/importar" class="btn btn-success me-2">
                <i class="fas fa-file-code me-1"></i>
                Importar XML
            </a>
            <a href="<?= APP_URL ?>/pedidos/novo" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Novo Pedido
            </a>
        </div>
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
            <?php if (empty($orders)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-cart-shopping text-muted mb-3" style="font-size: 48px;"></i>
                    <h4 class="text-muted">Nenhum pedido cadastrado</h4>
                    <p class="text-muted">Comece criando um novo pedido ou importando um arquivo XML.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Número</th>
                                <th>Cliente</th>
                                <th>Data</th>
                                <th>Quantidade</th>
                                <th>Valor Total</th>
                                <th>Status</th>
                                <th width="100">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= htmlspecialchars($order['order_number']) ?></td>
                                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                    <td><?= number_format($order['total_quantity'], 0, ',', '.') ?></td>
                                    <td>R$ <?= number_format($order['total_amount'], 2, ',', '.') ?></td>
                                    <td>
                                        <span class="badge bg-<?= $this->getStatusColor($order['status']) ?>">
                                            <?= $this->getStatusLabel($order['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="/pedidos/visualizar/<?= $order['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once ROOT_DIR . '/src/views/layouts/footer.php'; ?> 