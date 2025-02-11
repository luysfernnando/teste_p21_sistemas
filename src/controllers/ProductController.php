<?php

class ProductController extends BaseController {
    public function index() {
        $products = $this->db->query("SELECT * FROM products ORDER BY name")->fetchAll();
        $this->render('pages/products/index', ['products' => $products]);
    }

    public function create() {
        if ($this->isPost()) {
            $data = [
                'name' => $this->getPost('name'),
                'description' => $this->getPost('description'),
                'price' => $this->getPost('price'),
                'stock' => $this->getPost('stock')
            ];

            // Upload da imagem
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = ROOT_DIR . '/public/uploads/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (!in_array($fileExtension, $allowedExtensions)) {
                    $this->setFlash('error', 'Formato de imagem inválido. Use JPG, PNG ou GIF.');
                    $this->render('pages/products/create', ['data' => $data]);
                    return;
                }

                // Verifica o tamanho do arquivo (máximo 2MB)
                if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                    $this->setFlash('error', 'A imagem deve ter no máximo 2MB.');
                    $this->render('pages/products/create', ['data' => $data]);
                    return;
                }

                $fileName = uniqid() . '.' . $fileExtension;
                $uploadFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                    $data['image'] = 'uploads/products/' . $fileName;
                } else {
                    $this->setFlash('error', 'Erro ao fazer upload da imagem.');
                    $this->render('pages/products/create', ['data' => $data]);
                    return;
                }
            }

            $errors = $this->validateRequired($data, ['name', 'price', 'stock']);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Por favor, preencha todos os campos obrigatórios.');
                $this->render('pages/products/create', ['data' => $data, 'errors' => $errors]);
                return;
            }

            if (!is_numeric($data['price']) || $data['price'] <= 0) {
                $errors['price'] = 'Preço inválido.';
                $this->setFlash('error', 'Preço inválido.');
                $this->render('pages/products/create', ['data' => $data, 'errors' => $errors]);
                return;
            }

            if (!is_numeric($data['stock']) || $data['stock'] < 0) {
                $errors['stock'] = 'Quantidade inválida.';
                $this->setFlash('error', 'Quantidade inválida.');
                $this->render('pages/products/create', ['data' => $data, 'errors' => $errors]);
                return;
            }

            try {
                $this->db->insert('products', $data);
                $this->setFlash('success', 'Produto cadastrado com sucesso!');
                $this->redirect('produtos');
            } catch (PDOException $e) {
                $this->setFlash('error', 'Erro ao cadastrar produto.');
                $this->render('pages/products/create', ['data' => $data]);
            }
            return;
        }

        $this->render('pages/products/create');
    }

    public function edit($id) {
        $product = $this->db->query("SELECT * FROM products WHERE id = ?", [$id])->fetch();
        
        if (!$product) {
            $this->setFlash('error', 'Produto não encontrado.');
            $this->redirect('produtos');
            return;
        }

        if ($this->isPost()) {
            $data = [
                'name' => $this->getPost('name'),
                'description' => $this->getPost('description'),
                'price' => $this->getPost('price'),
                'stock' => $this->getPost('stock')
            ];

            // Upload da nova imagem, se fornecida
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = ROOT_DIR . '/public/uploads/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (!in_array($fileExtension, $allowedExtensions)) {
                    $this->setFlash('error', 'Formato de imagem inválido. Use JPG, PNG ou GIF.');
                    $this->render('pages/products/edit', ['data' => $data]);
                    return;
                }

                $fileName = uniqid() . '.' . $fileExtension;
                $uploadFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                    // Remove a imagem antiga se existir
                    if (!empty($product['image'])) {
                        $oldFile = ROOT_DIR . '/public/' . $product['image'];
                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                        }
                    }
                    $data['image'] = 'uploads/products/' . $fileName;
                } else {
                    $this->setFlash('error', 'Erro ao fazer upload da imagem.');
                    $this->render('pages/products/edit', ['data' => $data]);
                    return;
                }
            } else {
                // Mantém a imagem atual
                $data['image'] = $product['image'];
            }

            $errors = $this->validateRequired($data, ['name', 'price', 'stock']);
            
            if (!empty($errors)) {
                $this->setFlash('error', 'Por favor, preencha todos os campos obrigatórios.');
                $this->render('pages/products/edit', ['data' => $data, 'errors' => $errors]);
                return;
            }

            if (!is_numeric($data['price']) || $data['price'] <= 0) {
                $errors['price'] = 'Preço inválido.';
                $this->setFlash('error', 'Preço inválido.');
                $this->render('pages/products/edit', ['data' => $data, 'errors' => $errors]);
                return;
            }

            if (!is_numeric($data['stock']) || $data['stock'] < 0) {
                $errors['stock'] = 'Quantidade inválida.';
                $this->setFlash('error', 'Quantidade inválida.');
                $this->render('pages/products/edit', ['data' => $data, 'errors' => $errors]);
                return;
            }

            try {
                $this->db->update('products', $data, 'id = ?', [$id]);
                $this->setFlash('success', 'Produto atualizado com sucesso!');
                $this->redirect('produtos');
            } catch (PDOException $e) {
                $this->setFlash('error', 'Erro ao atualizar produto.');
                $this->render('pages/products/edit', ['data' => $data]);
            }
            return;
        }

        $this->render('pages/products/edit', ['data' => $product]);
    }

    public function delete($id) {
        if (!$this->isPost()) {
            $this->redirect('produtos');
            return;
        }

        try {
            // Busca o produto para obter a imagem
            $product = $this->db->query("SELECT image FROM products WHERE id = ?", [$id])->fetch();
            
            if ($product && !empty($product['image'])) {
                $imagePath = ROOT_DIR . '/public/' . $product['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $this->db->delete('products', 'id = ?', [$id]);
            $this->setFlash('success', 'Produto excluído com sucesso!');
        } catch (PDOException $e) {
            $this->setFlash('error', 'Erro ao excluir produto.');
        }

        $this->redirect('produtos');
    }
} 