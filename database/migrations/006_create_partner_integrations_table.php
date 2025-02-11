<?php

class CreatePartnerIntegrationsTable {
    public function up() {
        return "
            -- Tabela de integrações com parceiros
            CREATE TABLE IF NOT EXISTS partner_integrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                partner_name VARCHAR(100) NOT NULL,
                xml_data TEXT NOT NULL,
                status ENUM('pending', 'processed', 'failed') NOT NULL DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                processed_at TIMESTAMP NULL,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            -- Índices
            CREATE INDEX idx_partner_integrations_status ON partner_integrations(status);
            CREATE INDEX idx_partner_integrations_partner ON partner_integrations(partner_name);
        ";
    }

    public function down() {
        return "
            -- Remove os índices
            DROP INDEX IF EXISTS idx_partner_integrations_partner ON partner_integrations;
            DROP INDEX IF EXISTS idx_partner_integrations_status ON partner_integrations;

            -- Remove a tabela
            DROP TABLE IF EXISTS partner_integrations;
        ";
    }
} 