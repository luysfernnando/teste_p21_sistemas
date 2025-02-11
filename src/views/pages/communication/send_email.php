<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-envelope text-primary"></i>
            Enviar E-mail
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
                    <i class="fas fa-paper-plane me-1"></i>
                    Nova Mensagem
                </div>
                <div class="card-body">
                    <form action="/comunicados/enviar-email" method="POST">
                        <div class="mb-3">
                            <label for="template_id" class="form-label">Template</label>
                            <select class="form-select" id="template_id" name="template_id" required>
                                <option value="">Selecione um template...</option>
                                <?php foreach ($templates as $template): ?>
                                <option value="<?= $template['id'] ?>">
                                    <?= htmlspecialchars($template['name']) ?> (<?= htmlspecialchars($template['type']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Destinatários</label>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="select-all" class="form-check-input">
                                            </th>
                                            <th>Nome</th>
                                            <th>E-mail</th>
                                            <th>Último Pedido</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($customers as $customer): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="customer_ids[]" value="<?= $customer['id'] ?>" class="form-check-input customer-select">
                                            </td>
                                            <td><?= htmlspecialchars($customer['name']) ?></td>
                                            <td><?= htmlspecialchars($customer['email']) ?></td>
                                            <td>
                                                <?php
                                                    // TODO: Implementar lógica para mostrar o último pedido
                                                    echo "N/A";
                                                ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div id="preview" class="border p-3 rounded" style="display: none;">
                                <h5>Prévia do E-mail</h5>
                                <hr>
                                <div id="preview-content"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <a href="/comunicados" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Enviar E-mail</button>
                            <button type="button" class="btn btn-info" id="btn-preview">Visualizar Prévia</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manipular seleção de todos os checkboxes
    const selectAll = document.getElementById('select-all');
    const customerCheckboxes = document.querySelectorAll('.customer-select');

    selectAll.addEventListener('change', function() {
        customerCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
    });

    // Atualizar "selecionar todos" quando checkboxes individuais são alterados
    customerCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(customerCheckboxes).every(cb => cb.checked);
            selectAll.checked = allChecked;
        });
    });

    // Manipular prévia do template
    const btnPreview = document.getElementById('btn-preview');
    const previewDiv = document.getElementById('preview');
    const previewContent = document.getElementById('preview-content');
    const templateSelect = document.getElementById('template_id');

    btnPreview.addEventListener('click', function() {
        const templateId = templateSelect.value;
        if (!templateId) {
            alert('Por favor, selecione um template primeiro.');
            return;
        }

        previewDiv.style.display = 'block';
        previewContent.innerHTML = 'Carregando prévia...';

        // Busca o conteúdo do template via AJAX
        fetch(`/comunicados/prever-template?template_id=${templateId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                previewContent.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                return;
            }

            previewContent.innerHTML = `
                <div class="mb-3">
                    <strong>Assunto:</strong> ${data.subject}
                </div>
                <div class="mb-3">
                    <strong>Conteúdo:</strong><br>
                    ${data.content}
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Esta é uma prévia do e-mail. As variáveis serão substituídas pelos dados reais ao enviar.
                </div>
            `;
        })
        .catch(error => {
            console.error('Erro:', error);
            previewContent.innerHTML = `
                <div class="alert alert-danger">
                    Erro ao carregar prévia. Por favor, tente novamente.
                </div>
            `;
        });
    });
});
</script> 