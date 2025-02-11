<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-envelope text-primary"></i>
            Comunicados
        </h1>
        <a href="<?= APP_URL ?>/comunicados/enviar-email" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>
            Novo Comunicado
        </a>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-envelope me-1"></i>
                    Templates de E-mail
                    <a href="/comunicados/criar-template" class="btn btn-primary btn-sm float-end">
                        <i class="fas fa-plus"></i> Novo Template
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Assunto</th>
                                <th>Criado em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($templates as $template): ?>
                            <tr>
                                <td><?= htmlspecialchars($template['name']) ?></td>
                                <td><?= htmlspecialchars($template['type']) ?></td>
                                <td><?= htmlspecialchars($template['subject']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($template['created_at'])) ?></td>
                                <td>
                                    <a href="/comunicados/editar-template/<?= $template['id'] ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-paper-plane me-1"></i>
                    Enviar E-mail
                    <a href="/comunicados/enviar-email" class="btn btn-success btn-sm float-end">
                        <i class="fas fa-envelope"></i> Nova Mensagem
                    </a>
                </div>
                <div class="card-body">
                    <p>Envie e-mails para seus clientes usando os templates cadastrados.</p>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i>
                    Histórico de E-mails
                    <a href="/comunicados/historico" class="btn btn-info btn-sm float-end">
                        <i class="fas fa-search"></i> Ver Histórico
                    </a>
                </div>
                <div class="card-body">
                    <p>Visualize todos os e-mails enviados e seus status.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cog me-1"></i>
                    Configurações de E-mail
                </div>
                <div class="card-body">
                    <form action="/comunicados/salvar-configuracoes" method="POST">
                        <div class="mb-3">
                            <label for="status_template_id" class="form-label">Template Padrão para Atualização de Status</label>
                            <select class="form-select" id="status_template_id" name="status_template_id" required>
                                <option value="">Selecione um template...</option>
                                <?php foreach ($templates as $template): ?>
                                <option value="<?= $template['id'] ?>" <?= ($template['id'] == $statusTemplateId) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($template['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                Este template será usado automaticamente quando o status de um pedido for atualizado.
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Salvar Configurações</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 