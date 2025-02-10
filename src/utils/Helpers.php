<?php

class Helpers {
    /**
     * Formata um número para moeda brasileira
     */
    public static function formatMoney($value) {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }

    /**
     * Formata uma data para o formato brasileiro
     */
    public static function formatDate($date, $withTime = false) {
        if (!$date) return '';
        $format = $withTime ? 'd/m/Y H:i' : 'd/m/Y';
        return date($format, strtotime($date));
    }

    /**
     * Limpa uma string de telefone
     */
    public static function cleanPhone($phone) {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    /**
     * Formata um telefone
     */
    public static function formatPhone($phone) {
        $phone = self::cleanPhone($phone);
        if (strlen($phone) === 11) {
            return sprintf('(%s) %s-%s', 
                substr($phone, 0, 2),
                substr($phone, 2, 5),
                substr($phone, 7)
            );
        }
        if (strlen($phone) === 10) {
            return sprintf('(%s) %s-%s',
                substr($phone, 0, 2),
                substr($phone, 2, 4),
                substr($phone, 6)
            );
        }
        return $phone;
    }

    /**
     * Gera um número de pedido único
     */
    public static function generateOrderNumber() {
        return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    /**
     * Valida um CPF
     */
    public static function validateCPF($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        if (strlen($cpf) != 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    /**
     * Valida um CNPJ
     */
    public static function validateCNPJ($cnpj) {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        
        if (strlen($cnpj) != 14 || preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        for ($i = 0, $j = 5, $sum = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $rest = $sum % 11;
        if ($cnpj[12] != ($rest < 2 ? 0 : 11 - $rest)) {
            return false;
        }

        for ($i = 0, $j = 6, $sum = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $rest = $sum % 11;
        return $cnpj[13] == ($rest < 2 ? 0 : 11 - $rest);
    }

    /**
     * Formata um CPF
     */
    public static function formatCPF($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        if (strlen($cpf) != 11) {
            return $cpf;
        }
        return sprintf('%s.%s.%s-%s', 
            substr($cpf, 0, 3),
            substr($cpf, 3, 3),
            substr($cpf, 6, 3),
            substr($cpf, 9, 2)
        );
    }

    /**
     * Formata um CNPJ
     */
    public static function formatCNPJ($cnpj) {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        if (strlen($cnpj) != 14) {
            return $cnpj;
        }
        return sprintf('%s.%s.%s/%s-%s', 
            substr($cnpj, 0, 2),
            substr($cnpj, 2, 3),
            substr($cnpj, 5, 3),
            substr($cnpj, 8, 4),
            substr($cnpj, 12, 2)
        );
    }

    /**
     * Gera um slug a partir de uma string
     */
    public static function slugify($text) {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return $text ?: 'n-a';
    }

    /**
     * Trunca um texto
     */
    public static function truncate($text, $length = 100, $ending = '...') {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length - strlen($ending)) . $ending;
    }

    /**
     * Retorna o status de um pedido em português
     */
    public static function getOrderStatus($status) {
        $statuses = [
            'pending' => 'Pendente',
            'processing' => 'Em Processamento',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado'
        ];
        return $statuses[$status] ?? 'Desconhecido';
    }

    /**
     * Retorna a cor do status de um pedido
     */
    public static function getOrderStatusColor($status) {
        $colors = [
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger'
        ];
        return $colors[$status] ?? 'secondary';
    }

    /**
     * Retorna o status de uma integração em português
     */
    public static function getIntegrationStatus($status) {
        $statuses = [
            'pending' => 'Pendente',
            'processed' => 'Processado',
            'failed' => 'Falhou'
        ];
        return $statuses[$status] ?? 'Desconhecido';
    }

    /**
     * Retorna a cor do status de uma integração
     */
    public static function getIntegrationStatusColor($status) {
        $colors = [
            'pending' => 'warning',
            'processed' => 'success',
            'failed' => 'danger'
        ];
        return $colors[$status] ?? 'secondary';
    }
} 