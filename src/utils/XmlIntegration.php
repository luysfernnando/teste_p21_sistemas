<?php

class XmlIntegration {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Valida um XML contra um schema XSD
     */
    public function validateXml($xmlString, $schemaPath) {
        $xml = new DOMDocument();
        $xml->loadXML($xmlString);

        if (!$xml->schemaValidate($schemaPath)) {
            throw new Exception('XML inválido: não está de acordo com o schema XSD');
        }

        return true;
    }

    /**
     * Processa um arquivo XML de pedidos
     */
    public function processOrderXml($xmlString, $partnerName, $integrationId = null) {
        try {
            // Se não foi fornecido um ID de integração, cria um novo registro
            if ($integrationId === null) {
                $integrationId = $this->db->insert('partner_integrations', [
                    'partner_name' => $partnerName,
                    'xml_data' => $xmlString,
                    'status' => 'pending'
                ]);
            }

            // Carrega o XML
            $xml = new SimpleXMLElement($xmlString);
            
            // Processa cada pedido
            foreach ($xml->pedido as $orderXml) {
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
            if ($integrationId) {
                $this->db->update('partner_integrations',
                    [
                        'status' => 'failed',
                        'processed_at' => date('Y-m-d H:i:s'),
                        'error_message' => $e->getMessage()
                    ],
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
        try {
            // Busca ou cria o produto
            $product = $this->getOrCreateProduct([
                'name' => (string)$orderXml->produto,
                'price' => 0, // Preço será definido posteriormente
                'stock' => 0  // Estoque será atualizado posteriormente
            ]);

            // Cria o pedido
            $orderData = [
                'store_id' => (string)$orderXml->id_loja,
                'store_name' => (string)$orderXml->nome_loja,
                'store_location' => (string)$orderXml->localizacao,
                'order_number' => $this->generateOrderNumber(),
                'total_amount' => 0, // Será calculado após definir o preço do produto
                'total_quantity' => (int)$orderXml->quantidade,
                'status' => 'pending'
            ];

            $orderId = $this->db->insert('orders', $orderData);

            // Cria o item do pedido
            $this->db->insert('order_items', [
                'order_id' => $orderId,
                'product_id' => $product['id'],
                'quantity' => (int)$orderXml->quantidade,
                'unit_price' => 0 // Preço será definido posteriormente
            ]);

        } catch (Exception $e) {
            error_log("Erro ao processar pedido: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Busca ou cria um produto
     */
    private function getOrCreateProduct($data) {
        // Tenta encontrar o produto
        $product = $this->db->query(
            "SELECT * FROM products WHERE name = ?",
            [$data['name']]
        )->fetch();

        if ($product) {
            return $product;
        }

        // Cria um novo produto
        $productId = $this->db->insert('products', [
            'name' => $data['name'],
            'description' => 'Importado via XML',
            'price' => $data['price'],
            'stock' => $data['stock']
        ]);

        $data['id'] = $productId;
        return $data;
    }

    /**
     * Gera um número único para o pedido
     */
    private function generateOrderNumber() {
        $prefix = date('Ymd');
        $sequence = mt_rand(1000, 9999);
        return $prefix . $sequence;
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
} 