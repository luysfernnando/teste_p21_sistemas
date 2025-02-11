<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-envelope text-primary"></i>
            Novo Template de E-mail
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
                    <i class="fas fa-envelope me-1"></i>
                    Criar Template
                </div>
                <div class="card-body">
                    <form action="/comunicados/criar-template" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome do Template</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Tipo</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="promotion">Promoção</option>
                                <option value="news">Novidades</option>
                                <option value="order_status">Status do Pedido</option>
                                <option value="custom">Personalizado</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Assunto do E-mail</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>

                        <div class="mb-3">
                            <label for="body" class="form-label">Conteúdo do E-mail</label>
                            <textarea class="form-control" id="body" name="body" rows="10" required></textarea>
                            <small class="text-muted">
                                Você pode usar as seguintes variáveis:
                                <br>
                                {nome_cliente} - Nome do cliente
                                <br>
                                {numero_pedido} - Número do pedido
                                <br>
                                {status_pedido} - Status atual do pedido
                            </small>
                        </div>

                        <div class="mb-3">
                            <a href="/comunicados" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Salvar Template</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Inicializar o editor rico (você pode usar TinyMCE, CKEditor ou similar)
    document.addEventListener('DOMContentLoaded', function() {
        // TODO: Implementar editor rico
    });
</script> 