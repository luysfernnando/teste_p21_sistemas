<?php

class CreateCommunicationsTable {
    public function up() {
        return "
            -- Tabela de comunicações/emails
            CREATE TABLE IF NOT EXISTS communications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                customer_id INT NOT NULL,
                type ENUM('order_status', 'promotion', 'newsletter') NOT NULL,
                subject VARCHAR(200) NOT NULL,
                content TEXT NOT NULL,
                sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                status ENUM('pending', 'sent', 'failed') NOT NULL DEFAULT 'pending',
                FOREIGN KEY (customer_id) REFERENCES customers(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            -- Índices
            CREATE INDEX idx_communications_customer ON communications(customer_id);
            CREATE INDEX idx_communications_type ON communications(type);
            CREATE INDEX idx_communications_status ON communications(status);
        ";
    }

    public function down() {
        return "
            -- Remove os índices
            DROP INDEX IF EXISTS idx_communications_status ON communications;
            DROP INDEX IF EXISTS idx_communications_type ON communications;
            DROP INDEX IF EXISTS idx_communications_customer ON communications;

            -- Remove a tabela
            DROP TABLE IF EXISTS communications;
        ";
    }
} 