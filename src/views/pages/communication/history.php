<div class="container-fluid">
<div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-envelope text-primary"></i>
            Histórico de E-mails
        </h1>
        <a href="<?= APP_URL ?>/comunicados" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Voltar
        </a>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i>
                    E-mails Enviados
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Template</th>
                                    <th>Cliente</th>
                                    <th>Pedido</th>
                                    <th>Assunto</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($history as $email): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($email['created_at'])) ?></td>
                                    <td><?= htmlspecialchars($email['template_name']) ?></td>
                                    <td><?= htmlspecialchars($email['customer_name']) ?></td>
                                    <td>
                                        <?php if ($email['order_number']): ?>
                                            <a href="/orders/visualizar/<?= $email['order_id'] ?>">#<?= htmlspecialchars($email['order_number']) ?></a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($email['subject']) ?></td>
                                    <td>
                                        <?php if ($email['status'] === 'sent'): ?>
                                            <span class="badge bg-success">Enviado</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger" title="<?= htmlspecialchars($email['error_message']) ?>">Falha</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 