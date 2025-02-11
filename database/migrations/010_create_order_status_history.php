<?php

class CreateOrderStatusHistory {
    public function up() {
        return "
            -- Tabela de histórico de status dos pedidos
            CREATE TABLE IF NOT EXISTS order_status_history (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                status VARCHAR(50) NOT NULL,
                notes TEXT NULL,
                created_by VARCHAR(100) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (order_id) REFERENCES orders(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            -- Índices
            CREATE INDEX idx_order_status_history_order ON order_status_history(order_id);
            CREATE INDEX idx_order_status_history_status ON order_status_history(status);
            CREATE INDEX idx_order_status_history_created_at ON order_status_history(created_at);
        ";
    }

    public function down() {
        return "
            -- Remove os índices
            DROP INDEX IF EXISTS idx_order_status_history_created_at ON order_status_history;
            DROP INDEX IF EXISTS idx_order_status_history_status ON order_status_history;
            DROP INDEX IF EXISTS idx_order_status_history_order ON order_status_history;

            -- Remove a tabela
            DROP TABLE IF EXISTS order_status_history;
        ";
    }
} 