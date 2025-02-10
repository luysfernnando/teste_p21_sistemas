<div class="text-center mb-4">
    <h1 class="display-4">
        <i class="fas fa-magic text-primary"></i>
        Bem-vindo à Loja Mágica
    </h1>
    <p class="lead text-muted">Sistema de gestão de clientes e pedidos</p>
</div>

<!-- Cards de Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Clientes</h6>
                        <h2 class="mt-2 mb-0"><?= $stats['customers'] ?></h2>
                    </div>
                    <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Pedidos</h6>
                        <h2 class="mt-2 mb-0"><?= $stats['orders'] ?></h2>
                    </div>
                    <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Produtos</h6>
                        <h2 class="mt-2 mb-0"><?= $stats['products'] ?></h2>
                    </div>
                    <i class="fas fa-box fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Pedidos Pendentes</h6>
                        <h2 class="mt-2 mb-0"><?= $stats['pending_orders'] ?></h2>
                    </div>
                    <i class="fas fa-clock fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Últimos Pedidos -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-cart text-primary me-2"></i>
                        Últimos Pedidos
                    </h5>
                    <a href="<?= APP_URL ?>/pedidos" class="btn btn-sm btn-primary">
                        Ver Todos
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($latest_orders)): ?>
                    <p class="text-muted text-center mb-0">Nenhum pedido registrado.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Cliente</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($latest_orders as $order): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($order['order_number']) ?></td>
                                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                        <td>R$ <?= number_format($order['total_amount'], 2, ',', '.') ?></td>
                                        <td>
                                            <span class="badge bg-<?= $this->getStatusColor($order['status']) ?>">
                                                <?= $this->getStatusLabel($order['status']) ?>
                                            </span>
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

    <!-- Últimos Clientes -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users text-primary me-2"></i>
                        Últimos Clientes
                    </h5>
                    <a href="<?= APP_URL ?>/clientes" class="btn btn-sm btn-primary">
                        Ver Todos
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($latest_customers)): ?>
                    <p class="text-muted text-center mb-0">Nenhum cliente cadastrado.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Telefone</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($latest_customers as $customer): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($customer['name']) ?></td>
                                        <td><?= htmlspecialchars($customer['email']) ?></td>
                                        <td><?= htmlspecialchars($customer['phone']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Últimas Integrações -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-plug text-primary me-2"></i>
                        Últimas Integrações
                    </h5>
                    <a href="<?= APP_URL ?>/integracao" class="btn btn-sm btn-primary">
                        Ver Todas
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($latest_integrations)): ?>
                    <p class="text-muted text-center mb-0">Nenhuma integração registrada.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Parceiro</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($latest_integrations as $integration): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($integration['partner_name']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $this->getIntegrationStatusColor($integration['status']) ?>">
                                                <?= $this->getIntegrationStatusLabel($integration['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($integration['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Funções auxiliares para labels e cores
function getStatusColor($status) {
    $colors = [
        'pending' => 'warning',
        'processing' => 'info',
        'shipped' => 'primary',
        'delivered' => 'success',
        'cancelled' => 'danger'
    ];
    return $colors[$status] ?? 'secondary';
}

function getStatusLabel($status) {
    $labels = [
        'pending' => 'Pendente',
        'processing' => 'Em Processamento',
        'shipped' => 'Enviado',
        'delivered' => 'Entregue',
        'cancelled' => 'Cancelado'
    ];
    return $labels[$status] ?? 'Desconhecido';
}

function getIntegrationStatusColor($status) {
    $colors = [
        'pending' => 'warning',
        'processed' => 'success',
        'failed' => 'danger'
    ];
    return $colors[$status] ?? 'secondary';
}

function getIntegrationStatusLabel($status) {
    $labels = [
        'pending' => 'Pendente',
        'processed' => 'Processado',
        'failed' => 'Falhou'
    ];
    return $labels[$status] ?? 'Desconhecido';
}
?> 