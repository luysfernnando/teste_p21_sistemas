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
        
        // Configuração de debug mais detalhada
        $this->mailer->SMTPDebug = SMTP::DEBUG_CONNECTION; // Alterado para ver detalhes da conexão
        $this->mailer->Debugoutput = function($str, $level) {
            error_log("[PHPMailer Debug][Level $level] $str");
        };
        
        // Configurações do servidor de e-mail
        $this->mailer->isSMTP();
        $this->mailer->Host = SMTP_HOST;
        $this->mailer->Port = SMTP_PORT;
        
        // Configurações específicas para MailHog
        $this->mailer->SMTPAuth = false;
        $this->mailer->SMTPSecure = '';
        $this->mailer->SMTPAutoTLS = false;
        
        // Timeout mais longo para debug
        $this->mailer->Timeout = 20;
        $this->mailer->SMTPKeepAlive = true;
        
        // Configurações de codificação
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->Encoding = 'base64';
        
        // Configurações do remetente
        $this->mailer->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        
        // Verifica se consegue conectar ao servidor SMTP
        try {
            if (!$this->mailer->smtpConnect()) {
                error_log("Erro ao conectar ao servidor SMTP: " . $this->mailer->ErrorInfo);
            }
        } catch (Exception $e) {
            error_log("Exceção ao tentar conectar ao SMTP: " . $e->getMessage());
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
            error_log("Iniciando envio de e-mail para: $to");
            error_log("Usando servidor SMTP: " . SMTP_HOST . ":" . SMTP_PORT);
            
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->isHTML($isHtml);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            
            if (!$isHtml) {
                $this->mailer->AltBody = $body;
            } else {
                $this->mailer->AltBody = strip_tags(str_replace('<br>', "\n", $body));
            }
            
            $result = $this->mailer->send();
            error_log("E-mail enviado com sucesso para: $to");
            return $result;
            
        } catch (Exception $e) {
            error_log("Erro detalhado ao enviar e-mail: " . $this->mailer->ErrorInfo);
            error_log("Stack trace: " . $e->getTraceAsString());
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
            $this->mailer->addAddress($order['customer_email'], $order['customer_name']);
            $this->mailer->Subject = 'Atualização do Pedido #' . $order['order_number'];
            
            // Substitui as variáveis no corpo do e-mail
            $body = str_replace(
                [
                    '{nome_cliente}',
                    '{numero_pedido}',
                    '{status_pedido}'
                ],
                [
                    $order['customer_name'],
                    $order['order_number'],
                    $this->getStatusLabel($order['status'])
                ],
                $order['body']
            );

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