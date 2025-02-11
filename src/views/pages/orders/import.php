<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-cart-arrow-down text-primary"></i>
            Importar Pedidos
        </h1>
        <a href="<?= APP_URL ?>/pedidos" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Voltar
        </a>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['flash']['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Upload de XML</h5>
                    <form action="/pedidos/importar" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="xml_file" class="form-label">Arquivo XML</label>
                            <input type="file" 
                                   class="form-control" 
                                   id="xml_file" 
                                   name="xml_file" 
                                   accept=".xml"
                                   required>
                            <div class="form-text">Selecione um arquivo XML com os pedidos.</div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload"></i> Importar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Estrutura do XML</h5>
                    <p>O arquivo XML deve seguir a seguinte estrutura:</p>
                    <pre class="bg-light p-3 rounded"><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;orders&gt;
    &lt;order&gt;
        &lt;customer&gt;
            &lt;name&gt;Nome do Cliente&lt;/name&gt;
            &lt;email&gt;email@cliente.com&lt;/email&gt;
            &lt;phone&gt;(00) 00000-0000&lt;/phone&gt;
            &lt;address&gt;Endereço do Cliente&lt;/address&gt;
        &lt;/customer&gt;
        &lt;items&gt;
            &lt;item&gt;
                &lt;name&gt;Nome do Produto&lt;/name&gt;
                &lt;description&gt;Descrição do Produto&lt;/description&gt;
                &lt;quantity&gt;1&lt;/quantity&gt;
                &lt;price&gt;99.90&lt;/price&gt;
            &lt;/item&gt;
        &lt;/items&gt;
        &lt;total&gt;99.90&lt;/total&gt;
    &lt;/order&gt;
&lt;/orders&gt;</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_DIR . '/src/views/layouts/footer.php'; ?> 