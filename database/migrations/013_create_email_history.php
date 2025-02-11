<?php

class CreateEmailHistory {
    public function up() {
        return "CREATE TABLE email_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            template_id INT,
            customer_id INT,
            order_id INT NULL,
            subject VARCHAR(255) NOT NULL,
            body TEXT NOT NULL,
            status ENUM('sent', 'failed') NOT NULL,
            error_message TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (template_id) REFERENCES email_templates(id),
            FOREIGN KEY (customer_id) REFERENCES customers(id),
            FOREIGN KEY (order_id) REFERENCES orders(id)
        )";
    }

    public function down() {
        return "DROP TABLE IF EXISTS email_history";
    }
} 