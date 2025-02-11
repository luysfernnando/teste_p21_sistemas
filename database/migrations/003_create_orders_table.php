<?php

class CreateOrdersTable {
    public function up() {
        return "
            -- Tabela de pedidos
            CREATE TABLE IF NOT EXISTS orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                customer_id INT NOT NULL,
                order_number VARCHAR(50) NOT NULL UNIQUE,
                total_amount DECIMAL(10,2) NOT NULL,
                status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (customer_id) REFERENCES customers(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            -- Índices
            CREATE INDEX idx_orders_customer ON orders(customer_id);
            CREATE INDEX idx_orders_number ON orders(order_number);
            CREATE INDEX idx_orders_status ON orders(status);
        ";
    }

    public function down() {
        return "
            -- Remove os índices
            DROP INDEX IF EXISTS idx_orders_status ON orders;
            DROP INDEX IF EXISTS idx_orders_number ON orders;
            DROP INDEX IF EXISTS idx_orders_customer ON orders;

            -- Remove a tabela
            DROP TABLE IF EXISTS orders;
        ";
    }
} 