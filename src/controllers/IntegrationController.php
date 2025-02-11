<?php

class IntegrationController extends BaseController {
    private $xmlIntegration;

    public function __construct() {
        parent::__construct();
        $this->xmlIntegration = new XmlIntegration();
    }

    public function index() {
        // Lista as últimas integrações
        $integrations = $this->db->query("
            SELECT * FROM partner_integrations 
            ORDER BY created_at DESC 
            LIMIT 50
        ")->fetchAll();

        $this->render('pages/integration/index', ['integrations' => $integrations]);
    }

    public function receive() {
        // Verifica se é uma requisição POST
        if (!$this->isPost()) {
            $this->json([
                'success' => false,
                'message' => 'Método não permitido'
            ]);
            return;
        }

        try {
            // Obtém o XML enviado
            $xmlString = file_get_contents('php://input');
            if (empty($xmlString)) {
                throw new Exception('Nenhum XML recebido');
            }

            // Obtém o nome do parceiro do header
            $partnerName = $_SERVER['HTTP_X_PARTNER_NAME'] ?? 'Unknown';

            // Registra a tentativa de integração
            $integrationId = $this->db->insert('partner_integrations', [
                'partner_name' => $partnerName,
                'xml_data' => $xmlString,
                'status' => 'pending'
            ]);

            try {
                // Valida o XML contra o schema
                $schemaPath = ROOT_DIR . '/src/utils/schemas/orders.xsd';
                $this->xmlIntegration->validateXml($xmlString, $schemaPath);

                // Processa o XML
                $this->xmlIntegration->processOrderXml($xmlString, $partnerName, $integrationId);

                // Retorna sucesso
                header('Content-Type: application/xml');
                echo $this->xmlIntegration->generateResponseXml(
                    true,
                    'XML processado com sucesso'
                );

            } catch (Exception $e) {
                // Atualiza o status da integração para falha
                $this->db->update('partner_integrations',
                    [
                        'status' => 'failed',
                        'processed_at' => date('Y-m-d H:i:s'),
                        'error_message' => $e->getMessage()
                    ],
                    'id = ?',
                    [$integrationId]
                );
                throw $e;
            }

        } catch (Exception $e) {
            // Log do erro
            error_log("Erro na integração: " . $e->getMessage());

            // Retorna erro
            header('Content-Type: application/xml');
            echo $this->xmlIntegration->generateResponseXml(
                false,
                'Erro ao processar XML: ' . $e->getMessage()
            );
        }
    }

    public function view($id) {
        // Busca a integração
        $integration = $this->db->query("
            SELECT * FROM partner_integrations 
            WHERE id = ?
        ", [$id])->fetch();

        if (!$integration) {
            $this->setFlash('error', 'Integração não encontrada');
            $this->redirect('integracao');
            return;
        }

        $this->render('pages/integration/view', ['integration' => $integration]);
    }

    public function reprocess($id) {
        if (!$this->isPost()) {
            $this->redirect('integracao');
            return;
        }

        try {
            // Busca a integração
            $integration = $this->db->query("
                SELECT * FROM partner_integrations 
                WHERE id = ?
            ", [$id])->fetch();

            if (!$integration) {
                throw new Exception('Integração não encontrada');
            }

            // Reprocessa o XML
            $this->xmlIntegration->processOrderXml(
                $integration['xml_data'],
                $integration['partner_name']
            );

            $this->setFlash('success', 'Integração reprocessada com sucesso');

        } catch (Exception $e) {
            $this->setFlash('error', 'Erro ao reprocessar: ' . $e->getMessage());
        }

        $this->redirect('integracao');
    }

    public function delete($id) {
        if (!$this->isPost()) {
            $this->redirect('integracao');
            return;
        }

        try {
            $this->db->delete('partner_integrations', 'id = ?', [$id]);
            $this->setFlash('success', 'Integração excluída com sucesso');
        } catch (Exception $e) {
            $this->setFlash('error', 'Erro ao excluir integração');
        }

        $this->redirect('integracao');
    }
} 