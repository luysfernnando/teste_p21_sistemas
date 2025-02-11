<?php

class CreateEmailTemplates {
    public function up() {
        return "CREATE TABLE email_templates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            body TEXT NOT NULL,
            type ENUM('promotion', 'news', 'order_status', 'custom') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
    }

    public function down() {
        return "DROP TABLE IF EXISTS email_templates";
    }
} 