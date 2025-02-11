<?php

class CommunicationController extends BaseController {
    public function index() {
        $templates = $this->db->query("SELECT * FROM email_templates ORDER BY created_at DESC")->fetchAll();
        
        // Busca o template padrão de status
        $statusTemplateId = $this->db->query("SELECT value FROM settings WHERE name = 'status_template_id'")->fetchColumn();
        
        $this->render('pages/communication/index', [
            'templates' => $templates,
            'statusTemplateId' => $statusTemplateId
        ]);
    }

    public function createTemplate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $subject = $_POST['subject'];
            $body = $_POST['body'];
            $type = $_POST['type'];

            $this->db->query(
                "INSERT INTO email_templates (name, subject, body, type) VALUES (?, ?, ?, ?)",
                [$name, $subject, $body, $type]
            );

            $this->redirect('/comunicados');
        }
        $this->render('pages/communication/create_template');
    }

    public function editTemplate($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $subject = $_POST['subject'];
            $body = $_POST['body'];
            $type = $_POST['type'];

            $this->db->query(
                "UPDATE email_templates SET name = ?, subject = ?, body = ?, type = ? WHERE id = ?",
                [$name, $subject, $body, $type, $id]
            );

            $this->redirect('/comunicados');
        }

        $template = $this->db->query("SELECT * FROM email_templates WHERE id = ?", [$id])->fetch();
        $this->render('pages/communication/edit_template', ['template' => $template]);
    }

    public function history() {
        $history = $this->db->query("
            SELECT h.*, t.name as template_name, c.name as customer_name, o.order_number 
            FROM email_history h 
            LEFT JOIN email_templates t ON h.template_id = t.id
            LEFT JOIN customers c ON h.customer_id = c.id
            LEFT JOIN orders o ON h.order_id = o.id
            ORDER BY h.created_at DESC
        ")->fetchAll();

        $this->render('pages/communication/history', ['history' => $history]);
    }

    public function sendEmail() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $templateId = $_POST['template_id'];
            $customerIds = $_POST['customer_ids'];
            $template = $this->db->query("SELECT * FROM email_templates WHERE id = ?", [$templateId])->fetch();

            foreach ($customerIds as $customerId) {
                try {
                    // Busca informações do cliente
                    $customer = $this->db->query("SELECT * FROM customers WHERE id = ?", [$customerId])->fetch();
                    
                    // Substitui as variáveis no template
                    $body = str_replace(
                        [
                            '{nome_cliente}',
                            '{numero_pedido}',
                            '{status_pedido}'
                        ],
                        [
                            $customer['name'],
                            'N/A', // Pode ser atualizado se necessário
                            'N/A'  // Pode ser atualizado se necessário
                        ],
                        $template['body']
                    );

                    // Envia o e-mail
                    $mailer = Mailer::getInstance();
                    $mailer->send($customer['email'], $template['subject'], $body);

                    // Registra sucesso no histórico
                    $this->db->query(
                        "INSERT INTO email_history (template_id, customer_id, subject, body, status) VALUES (?, ?, ?, ?, ?)",
                        [$templateId, $customerId, $template['subject'], $body, 'sent']
                    );

                } catch (Exception $e) {
                    // Registra falha no histórico
                    $this->db->query(
                        "INSERT INTO email_history (template_id, customer_id, subject, body, status, error_message) VALUES (?, ?, ?, ?, ?, ?)",
                        [$templateId, $customerId, $template['subject'], $body, 'failed', $e->getMessage()]
                    );
                    error_log("Erro ao enviar e-mail: " . $e->getMessage());
                }
            }

            $this->redirect('/comunicados/historico');
        }

        $templates = $this->db->query("SELECT * FROM email_templates")->fetchAll();
        $customers = $this->db->query("SELECT * FROM customers")->fetchAll();
        $this->render('pages/communication/send_email', [
            'templates' => $templates,
            'customers' => $customers
        ]);
    }

    public function previewTemplate() {
        header('Content-Type: application/json');
        
        if (!$this->isAjax()) {
            echo json_encode(['error' => 'Requisição inválida']);
            exit;
        }

        $templateId = $_GET['template_id'] ?? null;
        if (!$templateId) {
            echo json_encode(['error' => 'Template não especificado']);
            exit;
        }

        try {
            $template = $this->db->query(
                "SELECT * FROM email_templates WHERE id = ?", 
                [$templateId]
            )->fetch();

            if (!$template) {
                echo json_encode(['error' => 'Template não encontrado']);
                exit;
            }

            // Simula as variáveis do template
            $previewContent = str_replace(
                ['{nome_cliente}', '{numero_pedido}', '{status_pedido}'],
                ['[Nome do Cliente]', '[Número do Pedido]', '[Status do Pedido]'],
                $template['body']
            );

            echo json_encode([
                'success' => true,
                'subject' => $template['subject'],
                'content' => $previewContent
            ]);
            exit;
            
        } catch (Exception $e) {
            error_log("Erro ao gerar prévia do template: " . $e->getMessage());
            echo json_encode(['error' => 'Erro interno ao processar o template']);
            exit;
        }
    }

    public function saveSettings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $statusTemplateId = $_POST['status_template_id'];
            
            // Atualiza ou insere a configuração
            $this->db->query("
                INSERT INTO settings (name, value) 
                VALUES ('status_template_id', ?) 
                ON DUPLICATE KEY UPDATE value = ?
            ", [$statusTemplateId, $statusTemplateId]);

            $this->setFlash('success', 'Configurações salvas com sucesso!');
            $this->redirect('/comunicados');
        }
    }
} 