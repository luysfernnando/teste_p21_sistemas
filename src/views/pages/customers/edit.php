<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-user-edit text-primary"></i>
        Editar Cliente
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
                <form action="<?= APP_URL ?>/clientes/editar/<?= $data['id'] ?>" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" 
                               class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                               id="name" 
                               name="name" 
                               value="<?= htmlspecialchars($data['name']) ?>"
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
                               value="<?= htmlspecialchars($data['email']) ?>"
                               required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['email'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="order_history" class="form-label">Histórico de Pedidos</label>
                        <textarea class="form-control <?= isset($errors['order_history']) ? 'is-invalid' : '' ?>" 
                                  id="order_history" 
                                  name="order_history" 
                                  rows="3"><?= htmlspecialchars($data['order_history']) ?></textarea>
                        <?php if (isset($errors['order_history'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['order_history'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="last_order_date" class="form-label">Data do Último Pedido</label>
                        <input type="date" 
                               class="form-control <?= isset($errors['last_order_date']) ? 'is-invalid' : '' ?>" 
                               id="last_order_date" 
                               name="last_order_date" 
                               value="<?= htmlspecialchars($data['last_order_date']) ?>">
                        <?php if (isset($errors['last_order_date'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['last_order_date'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="last_order_amount" class="form-label">Valor do Último Pedido</label>
                        <input type="number" 
                               class="form-control <?= isset($errors['last_order_amount']) ? 'is-invalid' : '' ?>" 
                               id="last_order_amount" 
                               name="last_order_amount" 
                               step="0.01"
                               value="<?= htmlspecialchars($data['last_order_amount']) ?>">
                        <?php if (isset($errors['last_order_amount'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['last_order_amount'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 