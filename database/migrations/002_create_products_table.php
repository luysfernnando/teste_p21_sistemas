<?php

class CreateProductsTable {
    public function up() {
        return "
            -- Tabela de produtos
            CREATE TABLE IF NOT EXISTS products (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                price DECIMAL(10,2) NOT NULL,
                stock INT NOT NULL DEFAULT 0,
                image VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            -- Índices
            CREATE INDEX idx_products_name ON products(name);
            CREATE INDEX idx_products_price ON products(price);
        ";
    }

    public function down() {
        return "
            -- Remove os índices
            DROP INDEX IF EXISTS idx_products_price ON products;
            DROP INDEX IF EXISTS idx_products_name ON products;

            -- Remove a tabela
            DROP TABLE IF EXISTS products;
        ";
    }
} 