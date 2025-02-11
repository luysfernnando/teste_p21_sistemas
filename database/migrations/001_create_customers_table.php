<?php

class CreateCustomersTable {
    public function up() {
        return "
            -- Tabela de clientes
            CREATE TABLE IF NOT EXISTS customers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                order_history TEXT,
                last_order_date DATE,
                last_order_amount DECIMAL(10,2),
                phone VARCHAR(20),
                address TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            -- Índices
            CREATE INDEX idx_customers_email ON customers(email);
            CREATE INDEX idx_customers_name ON customers(name);
        ";
    }

    public function down() {
        return "
            -- Remove os índices
            DROP INDEX IF EXISTS idx_customers_name ON customers;
            DROP INDEX IF EXISTS idx_customers_email ON customers;

            -- Remove a tabela
            DROP TABLE IF EXISTS customers;
        ";
    }
} 