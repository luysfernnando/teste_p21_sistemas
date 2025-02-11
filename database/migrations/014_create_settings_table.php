<?php

class CreateSettingsTable {
    public function up() {
        return "
            CREATE TABLE IF NOT EXISTS settings (
                name VARCHAR(100) PRIMARY KEY,
                value TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
    }

    public function down() {
        return "DROP TABLE IF EXISTS settings;";
    }
} 