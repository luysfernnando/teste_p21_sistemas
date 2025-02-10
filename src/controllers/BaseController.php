<?php

abstract class BaseController {
    protected $db;
    protected $view;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    protected function render($view, $data = []) {
        // Extrair variáveis para o template
        extract($data);
        
        // Incluir o header
        require_once TEMPLATE_DIR . '/layouts/header.php';
        
        // Incluir o template específico
        require_once TEMPLATE_DIR . "/pages/{$view}.php";
        
        // Incluir o footer
        require_once TEMPLATE_DIR . '/layouts/footer.php';
    }

    protected function redirect($url) {
        header("Location: " . APP_URL . "/{$url}");
        exit;
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    protected function getPost($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    protected function getQuery($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    protected function validateRequired($data, $fields) {
        $errors = [];
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                $errors[$field] = "O campo {$field} é obrigatório.";
            }
        }
        return $errors;
    }

    protected function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    protected function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
} 