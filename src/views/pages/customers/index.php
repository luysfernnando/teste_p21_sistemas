<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-users text-primary"></i>
        Clientes
    </h1>
    <div>
        <a href="<?= APP_URL ?>/clientes/importar" class="btn btn-success me-2">
            <i class="fas fa-file-excel me-1"></i>
            Importar Excel
        </a>
        <a href="<?= APP_URL ?>/clientes/novo" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>
            Novo Cliente
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($customers)): ?>
            <div class="text-center py-5">
                <i class="fas fa-users text-muted mb-3" style="font-size: 48px;"></i>
                <h4 class="text-muted">Nenhum cliente cadastrado</h4>
                <p class="text-muted">Comece importando uma planilha ou cadastrando um novo cliente.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Histórico de Pedidos</th>
                            <th>Data Último Pedido</th>
                            <th>Valor Último Pedido</th>
                            <th width="150">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?= htmlspecialchars($customer['name']) ?></td>
                                <td><?= htmlspecialchars($customer['email']) ?></td>
                                <td><?= htmlspecialchars($customer['order_history']) ?></td>
                                <td><?= $customer['last_order_date'] ? date('d/m/Y', strtotime($customer['last_order_date'])) : '-' ?></td>
                                <td><?= $customer['last_order_amount'] ? 'R$ ' . number_format($customer['last_order_amount'], 2, ',', '.') : '-' ?></td>
                                <td>
                                    <a href="<?= APP_URL ?>/clientes/editar/<?= $customer['id'] ?>" 
                                       class="btn btn-sm btn-info me-1" 
                                       data-bs-toggle="tooltip" 
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?= APP_URL ?>/clientes/excluir/<?= $customer['id'] ?>" 
                                          method="post" 
                                          class="d-inline-block"
                                          onsubmit="return confirm('Tem certeza que deseja excluir este cliente?');">
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger"
                                                data-bs-toggle="tooltip" 
                                                title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Loading -->
<div class="loading">
    <div class="loading-content">
        <div class="loading-spinner mb-3"></div>
        <h5 class="text-muted">Carregando...</h5>
    </div>
</div> 