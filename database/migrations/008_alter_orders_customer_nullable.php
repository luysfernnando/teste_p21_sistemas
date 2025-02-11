<?php

class AlterOrdersCustomerNullable {
    public function up() {
        return "
            -- Torna o campo customer_id opcional
            ALTER TABLE orders 
            MODIFY COLUMN customer_id INT NULL;
        ";
    }

    public function down() {
        return "
            -- Primeiro, remove os itens dos pedidos que não têm customer_id
            DELETE oi FROM order_items oi
            INNER JOIN orders o ON o.id = oi.order_id
            WHERE o.customer_id IS NULL;
            
            -- Agora remove os pedidos sem customer_id
            DELETE FROM orders WHERE customer_id IS NULL;
            
            -- Por fim, volta o campo customer_id para NOT NULL
            ALTER TABLE orders 
            MODIFY COLUMN customer_id INT NOT NULL;
        ";
    }
} 