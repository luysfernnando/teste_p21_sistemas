<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-edit text-primary"></i>
        Editar Produto
    </h1>
    <a href="<?= APP_URL ?>/produtos" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>
        Voltar
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="<?= APP_URL ?>/produtos/editar/<?= $data['id'] ?>" method="post" enctype="multipart/form-data">
                    <!-- Preview da imagem atual -->
                    <?php if (!empty($data['image'])): ?>
                        <div class="text-center mb-3">
                            <img src="<?= APP_URL ?>/<?= htmlspecialchars($data['image']) ?>" 
                                 alt="Imagem atual do produto"
                                 class="img-thumbnail"
                                 style="max-height: 200px;">
                            <p class="text-muted small mt-1">Imagem atual do produto</p>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nome do Produto</label>
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
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                  id="description" 
                                  name="description" 
                                  rows="3"><?= htmlspecialchars($data['description']) ?></textarea>
                        <?php if (isset($errors['description'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['description'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="price" class="form-label">Preço</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" 
                                       class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>" 
                                       id="price" 
                                       name="price" 
                                       step="0.01" 
                                       min="0"
                                       value="<?= htmlspecialchars($data['price']) ?>"
                                       required>
                                <?php if (isset($errors['price'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $errors['price'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="stock" class="form-label">Quantidade em Estoque</label>
                            <input type="number" 
                                   class="form-control <?= isset($errors['stock']) ? 'is-invalid' : '' ?>" 
                                   id="stock" 
                                   name="stock" 
                                   min="0"
                                   value="<?= htmlspecialchars($data['stock']) ?>"
                                   required>
                            <?php if (isset($errors['stock'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['stock'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Nova Imagem do Produto</label>
                        <input type="file" 
                               class="form-control <?= isset($errors['image']) ? 'is-invalid' : '' ?>" 
                               id="image" 
                               name="image"
                               accept="image/*">
                        <div class="form-text">Deixe em branco para manter a imagem atual. Formatos aceitos: JPG, PNG e GIF. Tamanho máximo: 2MB</div>
                        <?php if (isset($errors['image'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['image'] ?>
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