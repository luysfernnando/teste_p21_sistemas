<?php

class CreateOrderItemsTable {
    public function up() {
        return "
            -- Tabela de itens do pedido
            CREATE TABLE IF NOT EXISTS order_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                product_id INT NOT NULL,
                quantity INT NOT NULL,
                unit_price DECIMAL(10,2) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (order_id) REFERENCES orders(id),
                FOREIGN KEY (product_id) REFERENCES products(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            -- Índices
            CREATE INDEX idx_order_items_order ON order_items(order_id);
            CREATE INDEX idx_order_items_product ON order_items(product_id);
        ";
    }

    public function down() {
        return "
            -- Remove os índices
            DROP INDEX IF EXISTS idx_order_items_product ON order_items;
            DROP INDEX IF EXISTS idx_order_items_order ON order_items;

            -- Remove a tabela
            DROP TABLE IF EXISTS order_items;
        ";
    }
} 