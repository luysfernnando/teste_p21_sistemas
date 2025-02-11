<?php

class CustomerController extends BaseController {
    public function index() {
        $customers = $this->db->query("SELECT * FROM customers ORDER BY name")->fetchAll();
        $this->render('pages/customers/index', ['customers' => $customers]);
    }

    public function import() {
        if ($this->isPost()) {
            if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
                $this->setFlash('error', 'Por favor, selecione um arquivo Excel válido.');
                $this->redirect('clientes/importar');
                return;
            }

            $file = $_FILES['excel_file'];
            $tempFile = $file['tmp_name'];
            
            // Verifica se é um arquivo Excel
            $fileType = mime_content_type($tempFile);
            if (!in_array($fileType, [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/octet-stream' // Alguns sistemas podem retornar este tipo para .xlsx
            ])) {
                $this->setFlash('error', 'O arquivo deve ser uma planilha Excel (.xls ou .xlsx).');
                $this->redirect('clientes/importar');
                return;
            }

            // Processa o arquivo Excel
            require_once ROOT_DIR . '/vendor/autoload.php';
            
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tempFile);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
                
                // Verifica se há dados na planilha
                if (count($rows) <= 1) {
                    $this->setFlash('error', 'A planilha está vazia ou contém apenas o cabeçalho.');
                    $this->redirect('clientes/importar');
                    return;
                }
                
                // Remove o cabeçalho
                array_shift($rows);
                
                $imported = 0;
                $errors = [];
                $warnings = [];

                foreach ($rows as $index => $row) {
                    // Pula linhas completamente vazias
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Extrai apenas o nome do cliente (removendo o ID se existir)
                    $fullName = isset($row[1]) ? trim((string)$row[1]) : '';
                    $name = preg_replace('/^\d+\s*/', '', $fullName); // Remove números no início do nome
                    $name = empty($name) ? '-' : $name;

                    // Processa o valor do último pedido
                    $lastOrderAmount = null;
                    if (isset($row[5]) && !empty(trim((string)$row[5]))) {
                        $amount = trim((string)$row[5]);
                        if (is_numeric($amount)) {
                            $lastOrderAmount = floatval($amount);
                        } else {
                            // Converte valores por extenso
                            $textualValues = [
                                'cinquenta' => 50,
                                'cem' => 100,
                                'duzentos' => 200,
                                // Adicione mais valores conforme necessário
                            ];
                            $amountLower = strtolower($amount);
                            if (isset($textualValues[$amountLower])) {
                                $lastOrderAmount = $textualValues[$amountLower];
                            } else {
                                $warnings[] = "Linha " . ($index + 2) . ": Valor do último pedido não reconhecido: {$amount}";
                            }
                        }
                    }

                    // Processa a data do último pedido
                    $lastOrderDate = null;
                    if (isset($row[4]) && !empty(trim((string)$row[4]))) {
                        try {
                            $dateValue = trim((string)$row[4]);
                            error_log("Data original: " . print_r($dateValue, true));
                            
                            // Tenta converter a data se estiver em formato string (YYYY-MM-DD)
                            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateValue)) {
                                $lastOrderDate = $dateValue;
                            }
                            // Se não for uma string de data válida, assume que é um número do Excel
                            else {
                                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue);
                                $lastOrderDate = $date->format('Y-m-d');
                            }
                        } catch (Exception $e) {
                            error_log("Erro ao processar data: " . $e->getMessage());
                            $warnings[] = "Linha " . ($index + 2) . ": Data do último pedido inválida";
                        }
                    }

                    $data = [
                        'name' => $name,
                        'email' => isset($row[2]) && !empty(trim((string)$row[2])) ? trim((string)$row[2]) : null,
                        'order_history' => isset($row[3]) && !empty(trim((string)$row[3])) ? trim((string)$row[3]) : '-',
                        'last_order_date' => $lastOrderDate,
                        'last_order_amount' => $lastOrderAmount
                    ];

                    // Validação básica de email apenas se não for nulo
                    if ($data['email'] !== null && !$this->validateEmail($data['email'])) {
                        $warnings[] = "Linha " . ($index + 2) . ": Email inválido - gerando email único";
                        $data['email'] = null;
                    }

                    try {
                        // Verifica se o cliente já existe por email
                        if ($data['email'] !== null) {
                            $existingCustomer = $this->db->query(
                                "SELECT id FROM customers WHERE email = ?", 
                                [$data['email']]
                            )->fetch();

                            if ($existingCustomer) {
                                // Atualiza o cliente existente
                                $this->db->update(
                                    'customers',
                                    $data,
                                    'id = ?',
                                    [$existingCustomer['id']]
                                );
                                $warnings[] = "Linha " . ($index + 2) . ": Cliente já existente - registro atualizado";
                                $imported++;
                                continue;
                            }
                        }
                        // Se não encontrou por email, tenta encontrar por nome (apenas se o nome não for placeholder)
                        else if ($data['name'] !== '-') {
                            $existingCustomer = $this->db->query(
                                "SELECT id, email FROM customers WHERE name = ? AND email LIKE '%@magia.com'", 
                                [$data['name']]
                            )->fetch();

                            if ($existingCustomer) {
                                // Usa o email existente ao invés de gerar um novo
                                $data['email'] = $existingCustomer['email'];
                                
                                // Atualiza o cliente existente
                                $this->db->update(
                                    'customers',
                                    $data,
                                    'id = ?',
                                    [$existingCustomer['id']]
                                );
                                $warnings[] = "Linha " . ($index + 2) . ": Cliente encontrado por nome - registro atualizado";
                                $imported++;
                                continue;
                            }
                        }

                        // Se não encontrou cliente existente, gera um email único
                        if ($data['email'] === null) {
                            $timestamp = time();
                            $random = substr(md5(uniqid()), 0, 6);
                            $data['email'] = "{$timestamp}_{$random}@magia.com";
                        }

                        // Insere novo cliente
                        $this->db->insert('customers', $data);
                        $imported++;
                        
                        // Adiciona avisos para campos com placeholder
                        if ($data['name'] === '-') {
                            $warnings[] = "Linha " . ($index + 2) . ": Nome em branco - usando placeholder";
                        }
                        if ($data['order_history'] === '-') {
                            $warnings[] = "Linha " . ($index + 2) . ": Histórico de pedidos em branco - usando placeholder";
                        }
                        
                    } catch (PDOException $e) {
                        $errors[] = "Linha " . ($index + 2) . ": Erro ao importar - " . $e->getMessage();
                        error_log("Erro ao importar cliente: " . $e->getMessage());
                    }
                }

                // Monta a mensagem de retorno
                if ($imported > 0) {
                    $message = "{$imported} cliente(s) importado(s) com sucesso.";
                    
                    if (count($warnings) > 0) {
                        $message .= "\nAvisos: " . implode("; ", $warnings);
                    }
                    
                    if (count($errors) > 0) {
                        $message .= "\nErros: " . implode("; ", $errors);
                    }
                    
                    $this->setFlash('success', $message);
                } else {
                    $message = "Nenhum cliente importado.";
                    
                    if (count($warnings) > 0) {
                        $message .= "\nAvisos: " . implode("; ", $warnings);
                    }
                    
                    if (count($errors) > 0) {
                        $message .= "\nErros: " . implode("; ", $errors);
                    }
                    
                    $this->setFlash('error', $message);
                }
                
            } catch (Exception $e) {
                error_log("Erro ao processar arquivo Excel: " . $e->getMessage());
                $this->setFlash('error', 'Erro ao processar o arquivo: ' . $e->getMessage());
            }

            $this->redirect('clientes');
            return;
        }

        $this->render('pages/customers/import');
    }

    public function create() {
        if ($this->isPost()) {
            $data = [
                'name' => $this->getPost('name'),
                'email' => $this->getPost('email'),
                'order_history' => $this->getPost('order_history'),
                'last_order_date' => $this->getPost('last_order_date'),
                'last_order_amount' => $this->getPost('last_order_amount')
            ];

            $errors = $this->validateRequired($data, ['name', 'email']);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Por favor, preencha todos os campos obrigatórios.');
                $this->render('pages/customers/create', ['data' => $data, 'errors' => $errors]);
                return;
            }

            if (!$this->validateEmail($data['email'])) {
                $errors['email'] = 'Email inválido.';
                $this->setFlash('error', 'Email inválido.');
                $this->render('pages/customers/create', ['data' => $data, 'errors' => $errors]);
                return;
            }

            try {
                $this->db->insert('customers', $data);
                $this->setFlash('success', 'Cliente cadastrado com sucesso!');
                $this->redirect('clientes');
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $this->setFlash('error', 'Este email já está cadastrado.');
                } else {
                    $this->setFlash('error', 'Erro ao cadastrar cliente.');
                }
                $this->render('pages/customers/create', ['data' => $data]);
            }
            return;
        }

        $this->render('pages/customers/create');
    }

    public function edit($id) {
        $customer = $this->db->query("SELECT * FROM customers WHERE id = ?", [$id])->fetch();
        
        if (!$customer) {
            $this->setFlash('error', 'Cliente não encontrado.');
            $this->redirect('clientes');
            return;
        }

        if ($this->isPost()) {
            $data = [
                'name' => $this->getPost('name'),
                'email' => $this->getPost('email'),
                'order_history' => $this->getPost('order_history'),
                'last_order_date' => $this->getPost('last_order_date'),
                'last_order_amount' => $this->getPost('last_order_amount')
            ];

            $errors = $this->validateRequired($data, ['name', 'email']);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Por favor, preencha todos os campos obrigatórios.');
                $this->render('pages/customers/edit', ['data' => $data, 'errors' => $errors]);
                return;
            }

            if (!$this->validateEmail($data['email'])) {
                $errors['email'] = 'Email inválido.';
                $this->setFlash('error', 'Email inválido.');
                $this->render('pages/customers/edit', ['data' => $data, 'errors' => $errors]);
                return;
            }

            // Validação dos novos campos
            if (!empty($data['last_order_date']) && !$this->validateDate($data['last_order_date'])) {
                $errors['last_order_date'] = 'Data inválida.';
                $this->setFlash('error', 'Data do último pedido inválida.');
                $this->render('pages/customers/edit', ['data' => $data, 'errors' => $errors]);
                return;
            }

            if (!empty($data['last_order_amount']) && !is_numeric($data['last_order_amount'])) {
                $errors['last_order_amount'] = 'Valor inválido.';
                $this->setFlash('error', 'Valor do último pedido inválido.');
                $this->render('pages/customers/edit', ['data' => $data, 'errors' => $errors]);
                return;
            }

            try {
                $this->db->update('customers', $data, 'id = ?', [$id]);
                $this->setFlash('success', 'Cliente atualizado com sucesso!');
                $this->redirect('clientes');
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $this->setFlash('error', 'Este email já está cadastrado para outro cliente.');
                } else {
                    $this->setFlash('error', 'Erro ao atualizar cliente.');
                }
                $this->render('pages/customers/edit', ['data' => $data]);
            }
            return;
        }

        $this->render('pages/customers/edit', ['data' => $customer]);
    }

    private function validateDate($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    public function delete($id) {
        if (!$this->isPost()) {
            $this->redirect('clientes');
            return;
        }

        try {
            $this->db->delete('customers', 'id = ?', [$id]);
            $this->setFlash('success', 'Cliente excluído com sucesso!');
        } catch (PDOException $e) {
            $this->setFlash('error', 'Erro ao excluir cliente.');
        }

        $this->redirect('clientes');
    }
} 