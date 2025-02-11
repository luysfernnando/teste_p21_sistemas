<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    private static $instance = null;
    private $mailer;

    private function __construct() {
        $this->mailer = new PHPMailer(true);
        
        try {
            // Configurações do servidor
            $this->mailer->isSMTP();
            $this->mailer->Host = MAIL_HOST;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = MAIL_USER;
            $this->mailer->Password = MAIL_PASS;
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = MAIL_PORT;
            $this->mailer->CharSet = 'UTF-8';

            // Remetente padrão
            $this->mailer->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        } catch (Exception $e) {
            throw new Exception('Erro ao configurar o mailer: ' . $e->getMessage());
        }
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
        $subject = "Bem-vindo à " . APP_NAME;
        
        $body = "
            <h2>Olá {$customer['name']},</h2>
            <p>Seja bem-vindo à {APP_NAME}! Estamos muito felizes em ter você como nosso cliente.</p>
            <p>A partir de agora você poderá aproveitar todos os nossos produtos mágicos e serviços encantados.</p>
            <p>Se precisar de ajuda, não hesite em nos contatar.</p>
            <br>
            <p>Atenciosamente,<br>Equipe " . APP_NAME . "</p>
        ";

        return $this->send($customer['email'], $subject, $body);
    }

    /**
     * Envia um e-mail de confirmação de pedido
     */
    public function sendOrderConfirmation($order, $customer) {
        $subject = "Pedido #{$order['order_number']} Confirmado";
        
        $body = "
            <h2>Olá {$customer['name']},</h2>
            <p>Seu pedido #{$order['order_number']} foi confirmado com sucesso!</p>
            <p><strong>Detalhes do Pedido:</strong></p>
            <ul>
                <li>Número: {$order['order_number']}</li>
                <li>Data: " . date('d/m/Y H:i', strtotime($order['created_at'])) . "</li>
                <li>Valor Total: R$ " . number_format($order['total_amount'], 2, ',', '.') . "</li>
            </ul>
            <p>Você receberá atualizações sobre o status do seu pedido.</p>
            <br>
            <p>Atenciosamente,<br>Equipe " . APP_NAME . "</p>
        ";

        return $this->send($customer['email'], $subject, $body);
    }

    /**
     * Envia um e-mail de atualização de status do pedido
     */
    public function sendOrderStatusUpdate($order, $customer) {
        $status = Helpers::getOrderStatus($order['status']);
        $subject = "Atualização do Pedido #{$order['order_number']}";
        
        $body = "
            <h2>Olá {$customer['name']},</h2>
            <p>O status do seu pedido #{$order['order_number']} foi atualizado para: <strong>{$status}</strong></p>
            <p><strong>Detalhes do Pedido:</strong></p>
            <ul>
                <li>Número: {$order['order_number']}</li>
                <li>Data: " . date('d/m/Y H:i', strtotime($order['created_at'])) . "</li>
                <li>Valor Total: R$ " . number_format($order['total_amount'], 2, ',', '.') . "</li>
            </ul>
            <br>
            <p>Atenciosamente,<br>Equipe " . APP_NAME . "</p>
        ";

        return $this->send($customer['email'], $subject, $body);
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

    private function __clone() {}
    private function __wakeup() {}
} 