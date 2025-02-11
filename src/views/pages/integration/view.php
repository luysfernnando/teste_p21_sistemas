<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-plug text-primary"></i>
        Visualizar Integração #<?= $integration['id'] ?>
    </h1>
    <a href="<?= APP_URL ?>/integracao" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>
        Voltar
    </a>
</div>

<div class="row">
    <!-- Detalhes da Integração -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    Detalhes
                </h5>
            </div>
            <div class="card-body">
                <dl>
                    <dt>Parceiro</dt>
                    <dd><?= htmlspecialchars($integration['partner_name']) ?></dd>

                    <dt>Status</dt>
                    <dd>
                        <span class="badge bg-<?= Helpers::getIntegrationStatusColor($integration['status']) ?>">
                            <?= Helpers::getIntegrationStatus($integration['status']) ?>
                        </span>
                    </dd>

                    <dt>Data de Criação</dt>
                    <dd><?= date('d/m/Y H:i', strtotime($integration['created_at'])) ?></dd>

                    <dt>Data de Processamento</dt>
                    <dd>
                        <?= $integration['processed_at'] 
                            ? date('d/m/Y H:i', strtotime($integration['processed_at']))
                            : '-' 
                        ?>
                    </dd>
                </dl>

                <?php if ($integration['status'] !== 'processed'): ?>
                    <form action="<?= APP_URL ?>/integracao/reprocessar/<?= $integration['id'] ?>" 
                          method="post"
                          class="mt-3">
                        <button type="submit" 
                                class="btn btn-warning w-100"
                                data-confirm="Tem certeza que deseja reprocessar esta integração?">
                            <i class="fas fa-sync-alt me-1"></i>
                            Reprocessar
                        </button>
                    </form>
                <?php endif; ?>

                <form action="<?= APP_URL ?>/integracao/excluir/<?= $integration['id'] ?>" 
                      method="post"
                      class="mt-2">
                    <button type="submit" 
                            class="btn btn-danger w-100"
                            data-confirm="Tem certeza que deseja excluir esta integração?">
                        <i class="fas fa-trash me-1"></i>
                        Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- XML -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-code text-primary me-2"></i>
                    XML Recebido
                </h5>
            </div>
            <div class="card-body">
                <pre class="bg-light p-3 rounded" style="max-height: 500px; overflow: auto;"><code><?= htmlspecialchars($integration['xml_data']) ?></code></pre>
                
                <div class="d-grid mt-3">
                    <button type="button" 
                            class="btn btn-outline-primary"
                            onclick="copyXml()">
                        <i class="fas fa-copy me-1"></i>
                        Copiar XML
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyXml() {
    const xml = `<?= str_replace('`', '\`', $integration['xml_data']) ?>`;
    navigator.clipboard.writeText(xml).then(() => {
        showAlert('XML copiado para a área de transferência!', 'success');
    }).catch(() => {
        showAlert('Erro ao copiar XML.', 'error');
    });
}
</script> 