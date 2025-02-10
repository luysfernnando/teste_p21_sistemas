<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-file-excel text-success"></i>
        Importar Clientes
    </h1>
    <a href="<?= APP_URL ?>/clientes" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>
        Voltar
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="<?= APP_URL ?>/clientes/importar" method="post" enctype="multipart/form-data">
                    <div class="mb-4">
                        <h5>Instruções</h5>
                        <p class="text-muted">
                            Para importar os clientes, sua planilha deve seguir o seguinte formato:
                        </p>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Telefone</th>
                                        <th>Endereço</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>João Silva</td>
                                        <td>joao@email.com</td>
                                        <td>(11) 98765-4321</td>
                                        <td>Rua Exemplo, 123</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            A primeira linha da planilha deve conter os cabeçalhos conforme exemplo acima.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="excel_file" class="form-label">Arquivo Excel</label>
                        <input type="file" 
                               class="form-control" 
                               id="excel_file" 
                               name="excel_file" 
                               accept=".xls,.xlsx"
                               required>
                        <div class="form-text">
                            Formatos aceitos: .xls, .xlsx
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i>
                            Importar Clientes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Loading -->
<div class="loading">
    <div class="loading-content">
        <div class="loading-spinner mb-3"></div>
        <h5 class="text-muted">Importando clientes...</h5>
        <p class="text-muted">Por favor, aguarde enquanto processamos sua planilha.</p>
    </div>
</div>

<script>
document.querySelector('form').addEventListener('submit', function() {
    showLoading();
});
</script> 