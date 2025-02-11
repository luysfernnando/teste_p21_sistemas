<?php

class AlterOrdersAddStoreInfo {
    public function up() {
        return "
            -- Adiciona campos para informações da loja
            ALTER TABLE orders 
            ADD COLUMN store_id VARCHAR(50) NULL AFTER customer_id,
            ADD COLUMN store_name VARCHAR(100) NULL AFTER store_id,
            ADD COLUMN store_location VARCHAR(255) NULL AFTER store_name;

            -- Adiciona índice para store_id
            CREATE INDEX idx_orders_store ON orders(store_id);
        ";
    }

    public function down() {
        return "
            -- Remove o índice
            DROP INDEX idx_orders_store ON orders;

            -- Remove as colunas
            ALTER TABLE orders 
            DROP COLUMN store_location,
            DROP COLUMN store_name,
            DROP COLUMN store_id;
        ";
    }
} 