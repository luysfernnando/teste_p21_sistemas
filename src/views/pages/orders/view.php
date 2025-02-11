<?php require_once ROOT_DIR . '/src/views/layouts/header.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Pedido #<?= htmlspecialchars($order['order_number']) ?></h1>
        <div>
            <div class="btn-group">
                <button type="button" 
                        class="btn btn-outline-primary dropdown-toggle" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false">
                    Ações
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <form action="/pedidos/status/<?= $order['id'] ?>" method="post" class="d-inline">
                            <input type="hidden" name="status" value="processing">
                            <button type="submit" class="dropdown-item">
                                <i class="bi bi-play-circle"></i> Iniciar Processamento
                            </button>
                        </form>
                    </li>
                    <li>
                        <form action="/pedidos/status/<?= $order['id'] ?>" method="post" class="d-inline">
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="dropdown-item">
                                <i class="bi bi-check-circle"></i> Marcar como Concluído
                            </button>
                        </form>
                    </li>
                    <li>
                        <form action="/pedidos/status/<?= $order['id'] ?>" method="post" class="d-inline">
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-x-circle"></i> Cancelar Pedido
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            <a href="/pedidos" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Voltar
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

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Itens do Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th class="text-center">Quantidade</th>
                                    <th class="text-end">Preço Unit.</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                                        <td class="text-center"><?= $item['quantity'] ?></td>
                                        <td class="text-end">
                                            R$ <?= number_format($item['unit_price'], 2, ',', '.') ?>
                                        </td>
                                        <td class="text-end">
                                            R$ <?= number_format($item['quantity'] * $item['unit_price'], 2, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td class="text-end">
                                        <strong>
                                            R$ <?= number_format($order['total_amount'], 2, ',', '.') ?>
                                        </strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações do Pedido</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7">
                            <span class="badge bg-<?= $this->getStatusColor($order['status']) ?>">
                                <?= $this->getStatusLabel($order['status']) ?>
                            </span>
                        </dd>

                        <dt class="col-sm-5">Data:</dt>
                        <dd class="col-sm-7">
                            <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                        </dd>

                        <dt class="col-sm-5">Cliente:</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($order['customer_name']) ?></dd>

                        <dt class="col-sm-5">Email:</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($order['customer_email']) ?></dd>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Histórico</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <div class="timeline-date">
                                    <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                </div>
                                <div>Pedido criado</div>
                            </div>
                        </div>
                        <?php if ($order['status'] !== 'pending'): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-<?= $this->getStatusColor($order['status']) ?>"></div>
                                <div class="timeline-content">
                                    <div class="timeline-date">
                                        <?= date('d/m/Y H:i', strtotime($order['updated_at'])) ?>
                                    </div>
                                    <div>Status atualizado para <?= $this->getStatusLabel($order['status']) ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 1.5rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0.5rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}

.timeline-marker {
    position: absolute;
    left: -1.5rem;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-date {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.timeline-content {
    padding-left: 0.5rem;
}
</style>

<?php require_once ROOT_DIR . '/src/views/layouts/footer.php'; ?> 