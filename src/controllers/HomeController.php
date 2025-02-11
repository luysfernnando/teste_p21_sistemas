<?php

class HomeController extends BaseController {
    public function index() {
        // Busca estatísticas gerais
        $stats = [
            'customers' => $this->db->query("SELECT COUNT(*) as total FROM customers")->fetch()['total'],
            'orders' => $this->db->query("SELECT COUNT(*) as total FROM orders")->fetch()['total'],
            'products' => $this->db->query("SELECT COUNT(*) as total FROM products")->fetch()['total'],
            'pending_orders' => $this->db->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'")->fetch()['total']
        ];

        // Busca os últimos pedidos
        $latest_orders = $this->db->query("
            SELECT o.*, c.name as customer_name 
            FROM orders o 
            LEFT JOIN customers c ON o.customer_id = c.id 
            ORDER BY o.created_at DESC 
            LIMIT 5
        ")->fetchAll();

        // Busca os últimos clientes cadastrados
        $latest_customers = $this->db->query("
            SELECT * FROM customers 
            ORDER BY created_at DESC 
            LIMIT 5
        ")->fetchAll();

        // Busca as últimas integrações
        $latest_integrations = $this->db->query("
            SELECT * FROM partner_integrations 
            ORDER BY created_at DESC 
            LIMIT 5
        ")->fetchAll();

        $this->render('pages/home/index', [
            'stats' => $stats,
            'latest_orders' => $latest_orders,
            'latest_customers' => $latest_customers,
            'latest_integrations' => $latest_integrations
        ]);
    }
} 