<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-plug text-primary"></i>
        Integrações
    </h1>
    <div>
        <button type="button" 
                class="btn btn-primary"
                data-bs-toggle="modal" 
                data-bs-target="#xmlDocModal">
            <i class="fas fa-book me-1"></i>
            Documentação XML
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($integrations)): ?>
            <div class="text-center py-5">
                <i class="fas fa-plug text-muted mb-3" style="font-size: 48px;"></i>
                <h4 class="text-muted">Nenhuma integração registrada</h4>
                <p class="text-muted">As integrações aparecerão aqui quando forem recebidas.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Parceiro</th>
                            <th>Status</th>
                            <th>Data de Criação</th>
                            <th>Data de Processamento</th>
                            <th width="150">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($integrations as $integration): ?>
                            <tr>
                                <td><?= $integration['id'] ?></td>
                                <td><?= htmlspecialchars($integration['partner_name']) ?></td>
                                <td>
                                    <span class="badge bg-<?= Helpers::getIntegrationStatusColor($integration['status']) ?>">
                                        <?= Helpers::getIntegrationStatus($integration['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($integration['created_at'])) ?></td>
                                <td>
                                    <?= $integration['processed_at'] 
                                        ? date('d/m/Y H:i', strtotime($integration['processed_at']))
                                        : '-' 
                                    ?>
                                </td>
                                <td>
                                    <a href="<?= APP_URL ?>/integracao/visualizar/<?= $integration['id'] ?>" 
                                       class="btn btn-sm btn-info me-1"
                                       data-bs-toggle="tooltip"
                                       title="Visualizar XML">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <?php if ($integration['status'] !== 'processed'): ?>
                                        <form action="<?= APP_URL ?>/integracao/reprocessar/<?= $integration['id'] ?>" 
                                              method="post"
                                              class="d-inline">
                                            <button type="submit" 
                                                    class="btn btn-sm btn-warning me-1"
                                                    data-bs-toggle="tooltip"
                                                    title="Reprocessar">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <form action="<?= APP_URL ?>/integracao/excluir/<?= $integration['id'] ?>" 
                                          method="post"
                                          class="d-inline">
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger"
                                                data-bs-toggle="tooltip"
                                                title="Excluir"
                                                data-confirm="Tem certeza que deseja excluir esta integração?">
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

<!-- Modal de Documentação -->
<div class="modal fade" id="xmlDocModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-book text-primary me-2"></i>
                    Documentação da Integração XML
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>Endpoint</h6>
                <div class="bg-light p-3 rounded mb-3">
                    <code>POST <?= APP_URL ?>/integracao/receber</code>
                </div>

                <h6>Headers</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-sm">
                        <tr>
                            <td><code>Content-Type</code></td>
                            <td>application/xml</td>
                        </tr>
                        <tr>
                            <td><code>X-Partner-Name</code></td>
                            <td>Nome do parceiro</td>
                        </tr>
                    </table>
                </div>

                <h6>Exemplo de XML</h6>
                <pre class="bg-light p-3 rounded mb-3"><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;pedidos&gt;
    &lt;pedido&gt;
        &lt;id_loja&gt;001&lt;/id_loja&gt;
        &lt;nome_loja&gt;Torre de Cristal&lt;/nome_loja&gt;
        &lt;localizacao&gt;Planeta Zirak&lt;/localizacao&gt;
        &lt;produto&gt;Cristais Místicos&lt;/produto&gt;
        &lt;quantidade&gt;50&lt;/quantidade&gt;
    &lt;/pedido&gt;
&lt;/pedidos&gt;</code></pre>

                <h6>Exemplo de Resposta</h6>
                <pre class="bg-light p-3 rounded mb-3"><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;response&gt;
    &lt;success&gt;true&lt;/success&gt;
    &lt;message&gt;XML processado com sucesso&lt;/message&gt;
&lt;/response&gt;</code></pre>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    O XML será validado contra um schema XSD antes do processamento.
                    Certifique-se de seguir exatamente a estrutura demonstrada acima.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div> 