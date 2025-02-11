<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mailer {
    private static $instance = null;
    private $mailer;

    private function __construct() {
        $this->mailer = new PHPMailer(true);
        
        // Configurações do servidor de e-mail
        $this->mailer->isSMTP();
        $this->mailer->Host = SMTP_HOST;
        
        // Configura autenticação apenas se houver credenciais
        if (!empty(SMTP_USER) && !empty(SMTP_PASS)) {
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = SMTP_USER;
            $this->mailer->Password = SMTP_PASS;
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $this->mailer->SMTPAuth = false;
        }
        
        $this->mailer->Port = SMTP_PORT;
        $this->mailer->CharSet = 'UTF-8';
        
        // Configurações do remetente
        $this->mailer->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Envia um e-mail
     */
    public function send($to, $subject, $body, $isHtml = true) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->isHTML($isHtml);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            
            return $this->mailer->send();
        } catch (Exception $e) {
            throw new Exception('Erro ao enviar e-mail: ' . $e->getMessage());
        }
    }

    /**
     * Envia um e-mail de boas-vindas para um novo cliente
     */
    public function sendWelcomeEmail($customer) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($customer['email'], $customer['name']);
            $this->mailer->Subject = 'Bem-vindo à ' . COMPANY_NAME;
            
            // Corpo do e-mail
            $body = "Olá {$customer['name']},<br><br>";
            $body .= "Seja bem-vindo à " . COMPANY_NAME . "!<br><br>";
            $body .= "Estamos muito felizes em tê-lo como cliente.<br>";
            $body .= "A partir de agora você receberá atualizações sobre seus pedidos neste e-mail.<br><br>";
            $body .= "Atenciosamente,<br>";
            $body .= COMPANY_NAME;

            $this->mailer->isHTML(true);
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags(str_replace('<br>', "\n", $body));

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail de boas-vindas: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Envia um e-mail de confirmação de pedido
     */
    public function sendOrderConfirmation($order, $customer) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($customer['email'], $customer['name']);
            $this->mailer->Subject = 'Confirmação de Pedido #' . $order['order_number'];
            
            // Corpo do e-mail
            $body = "Olá {$customer['name']},<br><br>";
            $body .= "Seu pedido #{$order['order_number']} foi recebido com sucesso!<br><br>";
            $body .= "Valor total: R$ " . number_format($order['total_amount'], 2, ',', '.') . "<br>";
            $body .= "Status: " . $this->getStatusLabel($order['status']) . "<br><br>";
            $body .= "Agradecemos pela preferência!<br><br>";
            $body .= "Atenciosamente,<br>";
            $body .= COMPANY_NAME;

            $this->mailer->isHTML(true);
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags(str_replace('<br>', "\n", $body));

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail de confirmação: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Envia um e-mail de atualização de status do pedido
     */
    public function sendOrderStatusUpdate($order) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($order['email'], $order['name']);
            $this->mailer->Subject = 'Atualização do Pedido #' . $order['order_number'];
            
            // Corpo do e-mail
            $body = "Olá {$order['name']},<br><br>";
            $body .= "O status do seu pedido #{$order['order_number']} foi atualizado!<br><br>";
            $body .= "Novo status: " . $this->getStatusLabel($order['status']) . "<br><br>";
            $body .= "Para mais detalhes, acesse sua área do cliente em nosso site.<br><br>";
            $body .= "Atenciosamente,<br>";
            $body .= COMPANY_NAME;

            $this->mailer->isHTML(true);
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags(str_replace('<br>', "\n", $body));

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail de atualização de status: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Envia um e-mail promocional
     */
    public function sendPromotionalEmail($customer, $subject, $content) {
        $body = "
            <h2>Olá {$customer['name']},</h2>
            {$content}
            <br>
            <p>Atenciosamente,<br>Equipe " . APP_NAME . "</p>
            <hr>
            <small>Se não deseja mais receber nossos e-mails, <a href='" . APP_URL . "/unsubscribe'>clique aqui</a>.</small>
        ";

        return $this->send($customer['email'], $subject, $body);
    }

    private function getStatusLabel($status) {
        $labels = [
            'pending' => 'Pendente',
            'processing' => 'Em Processamento',
            'completed' => 'Concluído',
            'cancelled' => 'Cancelado'
        ];
        return $labels[$status] ?? $status;
    }

    private function __clone() {}
    public function __wakeup() {}
} 