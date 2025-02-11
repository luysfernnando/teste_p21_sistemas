<?php

class Migration {
    private $db;
    private $migrationsPath;
    private $currentBatch;

    public function __construct() {
        $this->checkDatabase();
        $this->db = Database::getInstance();
        $this->migrationsPath = ROOT_DIR . '/database/migrations';
        $this->ensureMigrationsTable();
    }

    /**
     * Verifica se o banco de dados existe e oferece criá-lo se não existir
     */
    private function checkDatabase() {
        try {
            // Tenta conectar sem selecionar o banco
            $pdo = new PDO(
                sprintf("mysql:host=%s", DB_HOST),
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Verifica se o banco existe
            $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . DB_NAME . "'");
            $exists = $stmt->fetch();

            if (!$exists) {
                echo "Database '" . DB_NAME . "' não existe." . PHP_EOL;
                echo "Deseja criar o banco de dados? (y/n): ";
                $handle = fopen("php://stdin", "r");
                $line = fgets($handle);
                if (trim(strtolower($line)) === 'y') {
                    $pdo->exec("CREATE DATABASE " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    echo "Database '" . DB_NAME . "' criada com sucesso!" . PHP_EOL;
                } else {
                    echo "Operação cancelada." . PHP_EOL;
                    exit(1);
                }
            }
        } catch (PDOException $e) {
            echo "Erro ao verificar/criar banco de dados: " . $e->getMessage() . PHP_EOL;
            exit(1);
        }
    }

    /**
     * Garante que a tabela de migrations existe
     */
    private function ensureMigrationsTable() {
        try {
            $sql = "
                CREATE TABLE IF NOT EXISTS migrations (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    migration VARCHAR(255) NOT NULL,
                    batch INT NOT NULL,
                    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ";
            $this->db->query($sql);
        } catch (Exception $e) {
            echo "Erro ao criar tabela de migrations: " . $e->getMessage() . PHP_EOL;
            exit(1);
        }
    }

    /**
     * Executa todas as migrations pendentes
     */
    public function migrate() {
        try {
            // Pega o último batch
            $lastBatch = $this->db->query("SELECT MAX(batch) as last_batch FROM migrations")->fetch();
            $this->currentBatch = ($lastBatch['last_batch'] ?? 0) + 1;

            // Lista todas as migrations do diretório
            $files = glob($this->migrationsPath . '/*.php');
            natsort($files);

            // Pega as migrations já executadas
            $executed = $this->db->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);

            $count = 0;
            foreach ($files as $file) {
                $migrationName = basename($file);
                
                // Pula as migrations já executadas
                if (in_array($migrationName, $executed)) {
                    continue;
                }

                try {
                    // Carrega a classe da migration
                    require_once $file;
                    $className = $this->getClassNameFromFile($file);
                    $migration = new $className();

                    // Executa o método up
                    $this->db->beginTransaction();
                    $sql = $migration->up();
                    if (!empty($sql)) {
                        $this->db->query($sql);
                    }
                    
                    // Registra a migration
                    $this->db->insert('migrations', [
                        'migration' => $migrationName,
                        'batch' => $this->currentBatch
                    ]);
                    
                    $this->db->commit();
                    $count++;
                    echo "Migrated: {$migrationName}" . PHP_EOL;
                } catch (Exception $e) {
                    if ($this->db->inTransaction()) {
                        $this->db->rollBack();
                    }
                    echo "Error migrating {$migrationName}: " . $e->getMessage() . PHP_EOL;
                    throw $e;
                }
            }

            echo "Migration completed. {$count} migrations executed." . PHP_EOL;
        } catch (Exception $e) {
            echo "Migration failed: " . $e->getMessage() . PHP_EOL;
            exit(1);
        }
    }

    /**
     * Reverte o último batch de migrations
     */
    public function rollback() {
        try {
            // Pega o último batch
            $lastBatch = $this->db->query("SELECT MAX(batch) as last_batch FROM migrations")->fetch();
            if (!$lastBatch['last_batch']) {
                echo "Nothing to rollback." . PHP_EOL;
                return;
            }

            // Pega as migrations do último batch
            $migrations = $this->db->query(
                "SELECT * FROM migrations WHERE batch = ? ORDER BY id DESC",
                [$lastBatch['last_batch']]
            )->fetchAll();

            $count = 0;
            foreach ($migrations as $migration) {
                $file = $this->migrationsPath . '/' . $migration['migration'];

                if (!file_exists($file)) {
                    echo "Warning: Migration file not found: {$migration['migration']}" . PHP_EOL;
                    continue;
                }

                try {
                    // Carrega a classe da migration
                    require_once $file;
                    $className = $this->getClassNameFromFile($file);
                    $migrationInstance = new $className();

                    // Executa o método down
                    $this->db->beginTransaction();
                    $sql = $migrationInstance->down();
                    if (!empty($sql)) {
                        $this->db->query($sql);
                    }
                    
                    // Remove o registro da migration
                    $this->db->delete('migrations', 'id = ?', [$migration['id']]);
                    
                    $this->db->commit();
                    $count++;
                    echo "Rolled back: {$migration['migration']}" . PHP_EOL;
                } catch (Exception $e) {
                    if ($this->db->inTransaction()) {
                        $this->db->rollBack();
                    }
                    echo "Error rolling back {$migration['migration']}: " . $e->getMessage() . PHP_EOL;
                    throw $e;
                }
            }

            echo "Rollback completed. {$count} migrations rolled back." . PHP_EOL;
        } catch (Exception $e) {
            echo "Rollback failed: " . $e->getMessage() . PHP_EOL;
            exit(1);
        }
    }

    /**
     * Lista o status de todas as migrations
     */
    public function status() {
        try {
            // Lista todas as migrations do diretório
            $files = glob($this->migrationsPath . '/*.php');
            natsort($files);

            // Pega as migrations já executadas
            $executed = $this->db->query(
                "SELECT migration, batch, executed_at FROM migrations"
            )->fetchAll(PDO::FETCH_ASSOC);
            $executedMap = array_column($executed, null, 'migration');

            echo "Migration Status:" . PHP_EOL;
            echo str_repeat('-', 80) . PHP_EOL;
            echo sprintf("%-40s %-10s %-30s\n", 'Migration', 'Batch', 'Executed At');
            echo str_repeat('-', 80) . PHP_EOL;

            foreach ($files as $file) {
                $migrationName = basename($file);
                $status = isset($executedMap[$migrationName])
                    ? sprintf("%-10s %-30s",
                        $executedMap[$migrationName]['batch'],
                        $executedMap[$migrationName]['executed_at'])
                    : sprintf("%-10s %-30s", 'Pending', 'Not executed');

                echo sprintf("%-40s %s\n", $migrationName, $status);
            }

            echo str_repeat('-', 80) . PHP_EOL;
        } catch (Exception $e) {
            echo "Error getting migration status: " . $e->getMessage() . PHP_EOL;
            exit(1);
        }
    }

    /**
     * Obtém o nome da classe a partir do arquivo de migration
     */
    private function getClassNameFromFile($file) {
        $content = file_get_contents($file);
        if (preg_match('/class\s+(\w+)/', $content, $matches)) {
            return $matches[1];
        }
        throw new Exception("Could not find class name in file: " . basename($file));
    }
} 