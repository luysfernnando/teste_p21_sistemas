<?php

class AddErrorMessageToIntegrations {
    public function up() {
        return "
            ALTER TABLE partner_integrations
            ADD COLUMN error_message TEXT NULL AFTER status;
        ";
    }

    public function down() {
        return "
            ALTER TABLE partner_integrations
            DROP COLUMN error_message;
        ";
    }
} 