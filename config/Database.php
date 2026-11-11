<?php

class Database {
    private static $instance = null;

    public static function getConnection() {
        if (self::$instance === null) {
            // Credenciais: em produção defina as variáveis de ambiente (ou config/db.local.php)
            // com um usuário PRÓPRIO do MySQL, que só tenha acesso ao banco cmpcd_jau.
            // Os valores root/'' abaixo servem apenas para rodar localmente no XAMPP.
            $local = is_file(__DIR__ . '/db.local.php') ? (require __DIR__ . '/db.local.php') : [];
            $host = $local['host'] ?? (getenv('DB_HOST') ?: 'localhost');
            $dbname = $local['dbname'] ?? (getenv('DB_NAME') ?: 'cmpcd_jau');
            $user = $local['user'] ?? (getenv('DB_USER') ?: 'root');
            $isProduction = (getenv('APP_ENV') === 'production') || (!empty($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] === 'production');

            if (isset($local['pass'])) {
                $passwords = [$local['pass']];
            } elseif (getenv('DB_PASS') !== false && getenv('DB_PASS') !== null) {
                $passwords = [getenv('DB_PASS')];
            } elseif ($isProduction) {
                // Em produção, a senha DEVE ser definida explicitamente para evitar acessos com senhas padrão
                throw new Exception('Configuração insegura: defina DB_PASS ou crie config/db.local.php com uma senha forte.');
            } else {
                // Tenta 'root' e depois '' como senhas padrão do XAMPP (somente desenvolvimento acadêmico local)
                $passwords = ['root', ''];
            }
            if (!preg_match('/^[A-Za-z0-9_]+$/', $dbname)) {
                throw new Exception('Nome de banco inválido.');
            }
            $connected = false;
            $pdo = null;

            foreach ($passwords as $password) {
                try {
                    // Tenta conectar diretamente ao banco de dados
                    $pdo = new PDO(
                        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                        $user,
                        $password,
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::ATTR_EMULATE_PREPARES => false,
                        ]
                    );
                    $connected = true;
                    break;
                } catch (PDOException $e) {
                    // Se o erro for de banco não existente, tenta criar o banco
                    if ($e->getCode() == 1049) {
                        try {
                            $tempPdo = new PDO(
                                "mysql:host=$host;charset=utf8mb4",
                                $user,
                                $password,
                                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                            );
                            $tempPdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                            
                            // Conecta novamente após criar
                            $pdo = new PDO(
                                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                                $user,
                                $password,
                                [
                                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                                    PDO::ATTR_EMULATE_PREPARES => false,
                                ]
                            );
                            $connected = true;
                            break;
                        } catch (PDOException $ex) {
                            // Continua para a próxima senha
                        }
                    }
                }
            }

            if (!$connected) {
                throw new Exception("Não foi possível conectar ao banco de dados.");
            }

            self::$instance = $pdo;
            self::checkAndSeedDatabase(self::$instance);
        }

        return self::$instance;
    }

    private static function checkAndSeedDatabase($pdo) {
        try {
            // Verifica se a tabela perfis_acesso existe
            $stmt = $pdo->query("SHOW TABLES LIKE 'perfis_acesso'");
            $tableExists = $stmt->rowCount() > 0;

            if (!$tableExists) {
                // 1. Importa a estrutura (schema) do banco de dados
                $sqlPath = __DIR__ . '/../database/CMDPCD_PI_BD.sql';
                if (file_exists($sqlPath)) {
                    $sql = file_get_contents($sqlPath);
                    // O arquivo SQL cria a base e dá USE. Como já estamos conectados, podemos apenas executar.
                    // Para garantir compatibilidade com execuções múltiplas, removemos comandos de banco que não podem rodar no contexto de conexão ativa
                    $sql = preg_replace('/DROP DATABASE IF EXISTS.*/i', '', $sql);
                    $sql = preg_replace('/CREATE DATABASE.*/i', '', $sql);
                    $sql = preg_replace('/USE `?cmpcd_jau`?.*/i', '', $sql);
                    
                    $pdo->exec($sql);
                }
            }

            // 2. Garante a inserção dos dados iniciais (seeder) se a tabela de perfis de acesso estiver vazia
            $stmt = $pdo->query("SELECT COUNT(*) FROM perfis_acesso");
            if ($stmt->fetchColumn() == 0) {
                $seederPath = __DIR__ . '/../database/seeder.sql';
                if (file_exists($seederPath)) {
                    $sqlSeed = file_get_contents($seederPath);
                    $sqlSeed = preg_replace('/USE cmpcd_jau.*/i', '', $sqlSeed);
                    $pdo->exec($sqlSeed);
                }
            }

        } catch (Exception $e) {
            // Registra erros de inicialização
            error_log("Database Setup Error: " . $e->getMessage());
        }
    }
}
