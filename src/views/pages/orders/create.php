<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-cart-plus text-primary"></i>
            Novo Pedido
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

    <div class="card">
        <div class="card-body">
            <form action="/pedidos/novo" method="post" id="orderForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Cliente</label>
                            <select class="form-select" id="customer_id" name="customer_id" required>
                                <option value="">Selecione um cliente</option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?= $customer['id'] ?>">
                                        <?= htmlspecialchars($customer['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h5>Itens do Pedido</h5>
                    <div id="orderItems">
                        <!-- Os itens serão adicionados aqui dinamicamente -->
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addOrderItem()">
                        <i class="bi bi-plus-lg"></i> Adicionar Item
                    </button>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Total: R$ <span id="orderTotal">0,00</span></strong>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Criar Pedido
                    </button>
                </div>

                <input type="hidden" name="items" id="itemsJson">
            </form>
        </div>
    </div>
</div>

<!-- Template para novos itens -->
<template id="orderItemTemplate">
    <div class="card mb-3 order-item">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Produto</label>
                        <select class="form-select product-select" onchange="updateItemPrice(this)" required>
                            <option value="">Selecione um produto</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?= $product['id'] ?>" 
                                        data-price="<?= $product['price'] ?>">
                                    <?= htmlspecialchars($product['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Quantidade</label>
                        <input type="number" 
                               class="form-control quantity-input" 
                               value="1" 
                               min="1" 
                               onchange="updateItemTotal(this)"
                               required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Preço Unitário</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" 
                                   class="form-control price-input" 
                                   step="0.01" 
                                   onchange="updateItemTotal(this)"
                                   required>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">Total</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" 
                                   class="form-control item-total" 
                                   readonly>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" 
                    class="btn btn-outline-danger btn-sm position-absolute top-0 end-0 m-3"
                    onclick="removeOrderItem(this)">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
</template>

<script>
function addOrderItem() {
    const template = document.getElementById('orderItemTemplate');
    const orderItems = document.getElementById('orderItems');
    const clone = template.content.cloneNode(true);
    orderItems.appendChild(clone);
    updateOrderTotal();
}

function removeOrderItem(button) {
    button.closest('.order-item').remove();
    updateOrderTotal();
}

function updateItemPrice(select) {
    const item = select.closest('.order-item');
    const price = select.options[select.selectedIndex].dataset.price;
    const priceInput = item.querySelector('.price-input');
    priceInput.value = price;
    updateItemTotal(priceInput);
}

function updateItemTotal(input) {
    const item = input.closest('.order-item');
    const quantity = parseFloat(item.querySelector('.quantity-input').value) || 0;
    const price = parseFloat(item.querySelector('.price-input').value) || 0;
    const total = quantity * price;
    item.querySelector('.item-total').value = total.toFixed(2);
    updateOrderTotal();
}

function updateOrderTotal() {
    const items = document.querySelectorAll('.order-item');
    let total = 0;

    items.forEach(item => {
        total += parseFloat(item.querySelector('.item-total').value) || 0;
    });

    document.getElementById('orderTotal').textContent = total.toFixed(2).replace('.', ',');
    updateItemsJson();
}

function updateItemsJson() {
    const items = [];
    document.querySelectorAll('.order-item').forEach(item => {
        const productSelect = item.querySelector('.product-select');
        if (productSelect.value) {
            items.push({
                product_id: productSelect.value,
                quantity: item.querySelector('.quantity-input').value,
                price: item.querySelector('.price-input').value
            });
        }
    });
    document.getElementById('itemsJson').value = JSON.stringify(items);
}

// Adiciona o primeiro item automaticamente
document.addEventListener('DOMContentLoaded', function() {
    addOrderItem();
});

// Valida o formulário antes de enviar
document.getElementById('orderForm').addEventListener('submit', function(e) {
    const items = JSON.parse(document.getElementById('itemsJson').value);
    if (items.length === 0) {
        e.preventDefault();
        alert('Adicione pelo menos um item ao pedido.');
    }
});
</script>

<?php require_once ROOT_DIR . '/src/views/layouts/footer.php'; ?> 