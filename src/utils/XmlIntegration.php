<?php

class XmlIntegration {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Processa um arquivo XML de pedidos
     */
    public function processOrderXml($xmlString, $partnerName) {
        try {
            // Salva o XML no banco
            $integrationId = $this->db->insert('partner_integrations', [
                'partner_name' => $partnerName,
                'xml_data' => $xmlString,
                'status' => 'pending'
            ]);

            // Carrega o XML
            $xml = new SimpleXMLElement($xmlString);
            
            // Processa cada pedido
            foreach ($xml->order as $orderXml) {
                $this->processOrder($orderXml);
            }

            // Atualiza o status da integração
            $this->db->update('partner_integrations', 
                ['status' => 'processed', 'processed_at' => date('Y-m-d H:i:s')],
                'id = ?',
                [$integrationId]
            );

            return true;
        } catch (Exception $e) {
            // Em caso de erro, marca a integração como falha
            if (isset($integrationId)) {
                $this->db->update('partner_integrations',
                    ['status' => 'failed', 'processed_at' => date('Y-m-d H:i:s')],
                    'id = ?',
                    [$integrationId]
                );
            }
            throw $e;
        }
    }

    /**
     * Processa um pedido do XML
     */
    private function processOrder($orderXml) {
        // Verifica se o cliente existe
        $customer = $this->getOrCreateCustomer($orderXml->customer);

        // Cria o pedido
        $orderData = [
            'customer_id' => $customer['id'],
            'order_number' => Helpers::generateOrderNumber(),
            'total_amount' => (float)$orderXml->total,
            'status' => 'pending'
        ];

        $orderId = $this->db->insert('orders', $orderData);

        // Processa os itens do pedido
        foreach ($orderXml->items->item as $item) {
            $product = $this->getOrCreateProduct($item);

            $this->db->insert('order_items', [
                'order_id' => $orderId,
                'product_id' => $product['id'],
                'quantity' => (int)$item->quantity,
                'unit_price' => (float)$item->price
            ]);
        }

        // Envia e-mail de confirmação
        try {
            $order = $orderData;
            $order['id'] = $orderId;
            Mailer::getInstance()->sendOrderConfirmation($order, $customer);
        } catch (Exception $e) {
            // Log do erro de envio de e-mail
            error_log("Erro ao enviar e-mail de confirmação: " . $e->getMessage());
        }
    }

    /**
     * Busca ou cria um cliente
     */
    private function getOrCreateCustomer($customerXml) {
        $email = (string)$customerXml->email;
        
        // Tenta encontrar o cliente
        $customer = $this->db->query(
            "SELECT * FROM customers WHERE email = ?",
            [$email]
        )->fetch();

        if ($customer) {
            return $customer;
        }

        // Cria um novo cliente
        $customerData = [
            'name' => (string)$customerXml->name,
            'email' => $email,
            'phone' => (string)$customerXml->phone,
            'address' => (string)$customerXml->address
        ];

        $customerId = $this->db->insert('customers', $customerData);
        $customerData['id'] = $customerId;

        // Envia e-mail de boas-vindas
        try {
            Mailer::getInstance()->sendWelcomeEmail($customerData);
        } catch (Exception $e) {
            // Log do erro de envio de e-mail
            error_log("Erro ao enviar e-mail de boas-vindas: " . $e->getMessage());
        }

        return $customerData;
    }

    /**
     * Busca ou cria um produto
     */
    private function getOrCreateProduct($itemXml) {
        $name = (string)$itemXml->name;
        
        // Tenta encontrar o produto
        $product = $this->db->query(
            "SELECT * FROM products WHERE name = ?",
            [$name]
        )->fetch();

        if ($product) {
            return $product;
        }

        // Cria um novo produto
        $productData = [
            'name' => $name,
            'description' => (string)$itemXml->description,
            'price' => (float)$itemXml->price,
            'stock' => 0
        ];

        $productId = $this->db->insert('products', $productData);
        $productData['id'] = $productId;

        return $productData;
    }

    /**
     * Gera um XML de resposta
     */
    public function generateResponseXml($success, $message, $data = []) {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><response></response>');
        
        $xml->addChild('success', $success ? 'true' : 'false');
        $xml->addChild('message', $message);
        
        if (!empty($data)) {
            $dataNode = $xml->addChild('data');
            foreach ($data as $key => $value) {
                $dataNode->addChild($key, $value);
            }
        }

        return $xml->asXML();
    }

    /**
     * Valida um XML contra um schema XSD
     */
    public function validateXml($xmlString, $xsdPath) {
        libxml_use_internal_errors(true);
        
        $xml = new DOMDocument();
        $xml->loadXML($xmlString);
        
        if (!$xml->schemaValidate($xsdPath)) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->message;
            }
            
            throw new Exception("Erro de validação XML: " . implode("; ", $errorMessages));
        }
        
        return true;
    }
} 