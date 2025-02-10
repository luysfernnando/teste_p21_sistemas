<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-user-plus text-primary"></i>
        Novo Cliente
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
                <form action="<?= APP_URL ?>/clientes/novo" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" 
                               class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                               id="name" 
                               name="name" 
                               value="<?= isset($data['name']) ? htmlspecialchars($data['name']) : '' ?>"
                               required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['name'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" 
                               class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                               id="email" 
                               name="email" 
                               value="<?= isset($data['email']) ? htmlspecialchars($data['email']) : '' ?>"
                               required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['email'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="order_history" class="form-label">Histórico de Pedidos</label>
                        <textarea class="form-control" 
                                  id="order_history" 
                                  name="order_history" 
                                  rows="3"><?= isset($data['order_history']) ? htmlspecialchars($data['order_history']) : '' ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="last_order_date" class="form-label">Data do Último Pedido</label>
                        <input type="date" 
                               class="form-control" 
                               id="last_order_date" 
                               name="last_order_date" 
                               value="<?= isset($data['last_order_date']) ? htmlspecialchars($data['last_order_date']) : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label for="last_order_amount" class="form-label">Valor do Último Pedido</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" 
                                   class="form-control" 
                                   id="last_order_amount" 
                                   name="last_order_amount" 
                                   step="0.01" 
                                   value="<?= isset($data['last_order_amount']) ? htmlspecialchars($data['last_order_amount']) : '' ?>">
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Salvar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>