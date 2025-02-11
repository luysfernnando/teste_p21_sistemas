<?php

class OrderController extends BaseController {
    private $xmlIntegration;

    public function __construct() {
        parent::__construct();
        $this->xmlIntegration = new XmlIntegration();
    }

    public function index() {
        // Busca todos os pedidos com informações do cliente e/ou loja
        $orders = $this->db->query("
            SELECT o.*, 
                   COALESCE(c.name, o.store_name) as customer_name,
                   COALESCE(o.total_quantity, (
                       SELECT SUM(oi.quantity)
                       FROM order_items oi
                       WHERE oi.order_id = o.id
                   )) as total_quantity
            FROM orders o 
            LEFT JOIN customers c ON o.customer_id = c.id 
            ORDER BY o.created_at DESC
        ")->fetchAll();

        $this->render('pages/orders/index', ['orders' => $orders]);
    }

    public function create() {
        if ($this->isPost()) {
            try {
                $this->db->beginTransaction();

                $customerId = $this->getPost('customer_id');
                $items = json_decode($this->getPost('items'), true);
                $totalAmount = 0;
                $totalQuantity = 0;

                // Calcula o total do pedido
                foreach ($items as $item) {
                    $totalAmount += $item['quantity'] * $item['price'];
                    $totalQuantity += $item['quantity'];
                }

                // Insere o pedido
                $orderId = $this->db->insert('orders', [
                    'customer_id' => $customerId,
                    'order_number' => Helpers::generateOrderNumber(),
                    'total_amount' => $totalAmount,
                    'total_quantity' => $totalQuantity,
                    'status' => 'pending'
                ]);

                // Insere os itens do pedido
                foreach ($items as $item) {
                    $this->db->insert('order_items', [
                        'order_id' => $orderId,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price']
                    ]);
                }

                $this->db->commit();

                // Busca informações do cliente para o e-mail
                $customer = $this->db->query(
                    "SELECT * FROM customers WHERE id = ?",
                    [$customerId]
                )->fetch();

                // Envia e-mail de confirmação
                try {
                    $order = [
                        'id' => $orderId,
                        'order_number' => $orderNumber,
                        'total_amount' => $totalAmount,
                        'status' => 'pending'
                    ];
                    Mailer::getInstance()->sendOrderConfirmation($order, $customer);
                } catch (Exception $e) {
                    error_log("Erro ao enviar e-mail de confirmação: " . $e->getMessage());
                }

                $this->setFlash('success', 'Pedido criado com sucesso!');
                $this->redirect('pedidos');
                return;

            } catch (Exception $e) {
                $this->db->rollBack();
                $this->setFlash('error', 'Erro ao criar pedido: ' . $e->getMessage());
            }
        }

        // Busca clientes e produtos para o formulário
        $customers = $this->db->query("SELECT * FROM customers ORDER BY name")->fetchAll();
        $products = $this->db->query("SELECT * FROM products ORDER BY name")->fetchAll();

        $this->render('pages/orders/create', [
            'customers' => $customers,
            'products' => $products
        ]);
    }

    public function import() {
        if ($this->isPost()) {
            if (!isset($_FILES['xml_file']) || $_FILES['xml_file']['error'] !== UPLOAD_ERR_OK) {
                $this->setFlash('error', 'Por favor, selecione um arquivo XML válido.');
                $this->redirect('pedidos/importar');
                return;
            }

            try {
                $xmlString = file_get_contents($_FILES['xml_file']['tmp_name']);
                
                // Valida o XML contra o schema
                $schemaPath = ROOT_DIR . '/src/utils/schemas/orders.xsd';
                $this->xmlIntegration->validateXml($xmlString, $schemaPath);

                // Processa o XML
                $this->xmlIntegration->processOrderXml($xmlString, 'Importação Manual');

                $this->setFlash('success', 'Pedidos importados com sucesso!');
                $this->redirect('pedidos');
                return;

            } catch (Exception $e) {
                $this->setFlash('error', 'Erro ao importar pedidos: ' . $e->getMessage());
                $this->redirect('pedidos/importar');
                return;
            }
        }

        $this->render('pages/orders/import');
    }

    public function view($id) {
        $order = $this->db->query("
            SELECT o.*, 
                   COALESCE(c.name, o.store_name) as customer_name,
                   c.email as customer_email 
            FROM orders o 
            LEFT JOIN customers c ON o.customer_id = c.id 
            WHERE o.id = ?
        ", [$id])->fetch();

        if (!$order) {
            $this->setFlash('error', 'Pedido não encontrado');
            $this->redirect('pedidos');
            return;
        }

        // Busca os itens do pedido
        $items = $this->db->query("
            SELECT oi.*, p.name as product_name 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = ?
        ", [$id])->fetchAll();

        // Busca o histórico de status
        $statusHistory = $this->db->query("
            SELECT * FROM order_status_history 
            WHERE order_id = ? 
            ORDER BY created_at ASC
        ", [$id])->fetchAll();

        $this->render('pages/orders/view', [
            'order' => $order,
            'items' => $items,
            'statusHistory' => $statusHistory
        ]);
    }

    public function updateStatus($id) {
        if (!$this->isPost()) {
            $this->redirect('pedidos');
            return;
        }

        $status = $this->getPost('status');
        $customerEmail = $this->getPost('customer_email');
        $updateCustomer = $this->getPost('update_customer');
        
        $validStatus = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($status, $validStatus)) {
            $this->setFlash('error', 'Status inválido');
            $this->redirect('pedidos/visualizar/' . $id);
            return;
        }

        try {
            $this->db->beginTransaction();

            // Atualiza o status do pedido
            $this->db->update('orders', 
                ['status' => $status],
                'id = ?',
                [$id]
            );

            // Registra no histórico
            $this->db->insert('order_status_history', [
                'order_id' => $id,
                'status' => $status,
                'notes' => 'Status atualizado via sistema'
            ]);

            // Busca informações do pedido e cliente
            $order = $this->db->query("
                SELECT o.*, 
                       COALESCE(c.name, o.store_name) as customer_name,
                       c.email as customer_email,
                       c.id as customer_id
                FROM orders o 
                LEFT JOIN customers c ON o.customer_id = c.id 
                WHERE o.id = ?
            ", [$id])->fetch();

            // Se foi fornecido um novo e-mail no modal
            if ($customerEmail) {
                // Se solicitado, atualiza o cadastro do cliente
                if ($updateCustomer && $order['customer_id']) {
                    $this->db->update(
                        'customers',
                        ['email' => $customerEmail],
                        'id = ?',
                        [$order['customer_id']]
                    );
                }
                
                // Usa o e-mail fornecido para enviar a notificação
                $order['customer_email'] = $customerEmail;
            }

            // Envia e-mail de atualização de status se houver um e-mail
            if ($order['customer_email']) {
                try {
                    // Busca o template padrão de status
                    $templateId = $this->db->query("SELECT value FROM settings WHERE name = 'status_template_id'")->fetchColumn();
                    if ($templateId) {
                        $template = $this->db->query("SELECT * FROM email_templates WHERE id = ?", [$templateId])->fetch();
                        if ($template) {
                            $order['body'] = $template['body']; // Adiciona o corpo do template ao pedido
                            Mailer::getInstance()->sendOrderStatusUpdate($order);
                        }
                    }
                } catch (Exception $e) {
                    error_log("Erro ao enviar e-mail de atualização de status: " . $e->getMessage());
                }
            }

            $this->db->commit();
            $this->setFlash('success', 'Status atualizado com sucesso!');

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->setFlash('error', 'Erro ao atualizar status: ' . $e->getMessage());
        }

        // Redireciona de volta para a página apropriada
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, '/pedidos/visualizar/') !== false) {
            $this->redirect('pedidos/visualizar/' . $id);
        } else {
            $this->redirect('pedidos');
        }
    }
} 