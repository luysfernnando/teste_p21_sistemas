<?php

class AddTotalQuantityToOrders {
    public function up() {
        return "
            -- Adiciona coluna de quantidade total
            ALTER TABLE orders 
            ADD COLUMN total_quantity INT NOT NULL DEFAULT 0 AFTER total_amount;

            -- Atualiza a quantidade total dos pedidos existentes
            UPDATE orders o 
            SET total_quantity = (
                SELECT COALESCE(SUM(quantity), 0)
                FROM order_items oi
                WHERE oi.order_id = o.id
            );
        ";
    }

    public function down() {
        return "
            -- Remove a coluna de quantidade total
            ALTER TABLE orders 
            DROP COLUMN total_quantity;
        ";
    }
} 