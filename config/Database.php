<?php

class Database {
    private static $instance = null;

    /**
     * Detecta se o sistema está rodando em produção.
     * Considera produção quando configurado explicitamente ('production') ou
     * quando executado em um domínio web que não seja localhost/ambiente local.
     */
    public static function isProduction(array $local = []): bool {
        $env = strtolower((string)($local['env'] ?? (getenv('APP_ENV') ?: ($_SERVER['APP_ENV'] ?? ''))));
        if ($env === 'production' || $env === 'prod') {
            return true;
        }
        if ($env === 'local' || $env === 'development' || $env === 'dev') {
            return false;
        }
        if (PHP_SAPI === 'cli') {
            return false;
        }
        $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? 'localhost'));
        $host = preg_replace('/:\d+$/', '', $host);
        if ($host === '' || $host === 'localhost' || $host === '127.0.0.1' || $host === '[::1]') {
            return false;
        }
        if (preg_match('/\.(local|test|localhost)$/', $host)) {
            return false;
        }
        return true;
    }

    public static function getConnection() {
        if (self::$instance === null) {
            // Credenciais: em produção defina as variáveis de ambiente (ou config/db.local.php)
            // com um usuário PRÓPRIO do MySQL, que só tenha acesso ao banco do site.
            // Os valores root/'' abaixo servem apenas para rodar localmente no XAMPP.
            $local = is_file(__DIR__ . '/db.local.php') ? (require __DIR__ . '/db.local.php') : [];
            $host = $local['host'] ?? (getenv('DB_HOST') ?: 'localhost');
            $port = (int)($local['port'] ?? (getenv('DB_PORT') ?: 3306));
            $dbname = $local['dbname'] ?? (getenv('DB_NAME') ?: 'cmpcd_jau');
            $user = $local['user'] ?? (getenv('DB_USER') ?: 'root');
            $isProduction = self::isProduction($local);
            $seedDemo = isset($local['seed_demo']) ? (bool)$local['seed_demo'] : !$isProduction;

            if (isset($local['pass'])) {
                $passwords = [$local['pass']];
            } elseif (getenv('DB_PASS') !== false && getenv('DB_PASS') !== null) {
                $passwords = [getenv('DB_PASS')];
            } elseif ($isProduction) {
                // Em produção, a senha DEVE ser definida explicitamente para evitar acessos com senhas padrão
                throw new Exception('Configuração insegura: defina DB_PASS ou crie config/db.local.php com uma senha forte.');
            } else {
                // Tenta 'root' e depois '' como senhas padrão do XAMPP (somente desenvolvimento local)
                $passwords = ['root', ''];
            }

            if (!preg_match('/^[A-Za-z0-9_-]+$/', $dbname)) {
                throw new Exception('Nome de banco inválido.');
            }
            if ($port < 1 || $port > 65535) {
                $port = 3306;
            }

            $connected = false;
            $pdo = null;
            $pdoOpts = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            foreach ($passwords as $password) {
                try {
                    $pdo = new PDO(
                        "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
                        $user,
                        $password,
                        $pdoOpts
                    );
                    $connected = true;
                    break;
                } catch (PDOException $e) {
                    // Em ambiente local, se o erro for de banco não existente (1049), tenta criar o banco
                    if (!$isProduction && ((int)($e->errorInfo[1] ?? 0) === 1049 || (string)$e->getCode() === '1049')) {
                        try {
                            $tempPdo = new PDO(
                                "mysql:host={$host};port={$port};charset=utf8mb4",
                                $user,
                                $password,
                                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                            );
                            $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

                            $pdo = new PDO(
                                "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
                                $user,
                                $password,
                                $pdoOpts
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
            self::checkAndSeedDatabase(self::$instance, $isProduction, $seedDemo);
        }

        return self::$instance;
    }

    private static function checkAndSeedDatabase(PDO $pdo, bool $isProduction, bool $seedDemo): void {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE 'perfis_acesso'");
            $tableExists = $stmt->rowCount() > 0;

            if (!$tableExists) {
                // Em produção usa o schema limpo (sem DROP DATABASE/USE e sem usuários/PCDs fictícios)
                $sqlPath = $isProduction
                    ? __DIR__ . '/../database/producao_schema.sql'
                    : __DIR__ . '/../database/CMDPCD_PI_BD.sql';

                if (!file_exists($sqlPath)) {
                    $sqlPath = __DIR__ . '/../database/CMDPCD_PI_BD.sql';
                }

                if (file_exists($sqlPath)) {
                    $sql = file_get_contents($sqlPath);
                    $sql = preg_replace('/DROP DATABASE IF EXISTS.*/i', '', $sql);
                    $sql = preg_replace('/CREATE DATABASE.*/i', '', $sql);
                    $sql = preg_replace('/USE\s+`?[^`;\r\n]+`?;?/i', '', $sql);
                    $pdo->exec($sql);
                }
            }

            // Garante dados estruturais obrigatórios (perfis e tipos de deficiência)
            $stmt = $pdo->query("SELECT COUNT(*) FROM perfis_acesso");
            if ((int)$stmt->fetchColumn() === 0) {
                if ($isProduction || !$seedDemo) {
                    self::seedStructuralData($pdo);
                } else {
                    $seederPath = __DIR__ . '/../database/seeder.sql';
                    if (file_exists($seederPath)) {
                        $sqlSeed = file_get_contents($seederPath);
                        $sqlSeed = preg_replace('/USE\s+`?[^`;\r\n]+`?;?/i', '', $sqlSeed);
                        $pdo->exec($sqlSeed);
                    }
                }
            } else {
                // Em produção, garante também que tipos_deficiencia esteja populada
                $stmtTipos = $pdo->query("SELECT COUNT(*) FROM tipos_deficiencia");
                if ((int)$stmtTipos->fetchColumn() === 0) {
                    self::seedStructuralData($pdo);
                }
            }
        } catch (Exception $e) {
            error_log("Database Setup Error: " . $e->getMessage());
        }

        // Dados fictícios e admin local só são criados em ambiente de desenvolvimento local.
        if (!$isProduction && $seedDemo) {
            try {
                self::ensureLocalDemoData($pdo);
            } catch (Throwable $e) {
                error_log('[CMDPCD] Não foi possível preparar o ambiente local: ' . $e->getMessage());
            }
        }
    }

    /**
     * Popula apenas tabelas estruturais indispensáveis (sem usuários padrão nem registros fictícios).
     */
    private static function seedStructuralData(PDO $pdo): void {
        $pdo->exec(
            "INSERT IGNORE INTO perfis_acesso (id, nome, descricao) VALUES
             (1, 'admin_total', 'Administrador com acesso total ao sistema'),
             (2, 'admin_parcial', 'Administrador com acesso parcial'),
             (3, 'editor', 'Editor de conteúdos')"
        );
        $pdo->exec(
            "INSERT IGNORE INTO tipos_deficiencia (id, nome, descricao) VALUES
             (1, 'Física', 'Alteração completa ou parcial de um ou mais segmentos do corpo humano'),
             (2, 'Auditiva', 'Perda bilateral, parcial ou total de quarenta decibéis (dB) ou mais'),
             (3, 'Visual', 'Cegueira, baixa visão ou visão monocular'),
             (4, 'Intelectual', 'Funcionamento intelectual significativamente inferior à média'),
             (5, 'Múltipla', 'Associação de duas ou mais deficiências'),
             (6, 'TEA', 'Transtorno do Espectro Autista'),
             (7, 'Outros', 'Outras condições não listadas')"
        );
    }

    private static function ensureLocalDemoData(PDO $pdo): void {
        $pdo->exec("CREATE TABLE IF NOT EXISTS cmdpcd_local_setup (chave varchar(80) NOT NULL PRIMARY KEY, aplicado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $profile = $pdo->prepare("SELECT id FROM perfis_acesso WHERE nome = 'admin_total' LIMIT 1");
        $profile->execute();
        $profileId = (int)$profile->fetchColumn();
        if (!$profileId) {
            $pdo->exec("INSERT INTO perfis_acesso (nome, descricao) VALUES ('admin_total', 'Administrador com acesso total ao sistema')");
            $profileId = (int)$pdo->lastInsertId();
        }

        $email = 'luhan@email.com';
        $marker = $pdo->prepare('SELECT chave FROM cmdpcd_local_setup WHERE chave = ?');
        $marker->execute(['admin_bootstrap_v1']);
        if (!$marker->fetchColumn()) {
            $findAdmin = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
            $findAdmin->execute([$email]);
            $adminId = $findAdmin->fetchColumn();
            $hash = password_hash('123456', PASSWORD_DEFAULT);
            if ($adminId) {
                $update = $pdo->prepare("UPDATE usuarios SET perfil_id = ?, nome_completo = 'Luhan - Administrador', senha = ?, status = 'ativo', deletado_em = NULL WHERE id = ?");
                $update->execute([$profileId, $hash, $adminId]);
            } else {
                $cpf = '99999990000';
                $checkCpf = $pdo->prepare('SELECT id FROM usuarios WHERE cpf = ? LIMIT 1');
                for ($i = 1; $i < 100; $i++) {
                    $candidate = '9999999' . str_pad((string)$i, 4, '0', STR_PAD_LEFT);
                    $checkCpf->execute([$candidate]);
                    if (!$checkCpf->fetchColumn()) { $cpf = $candidate; break; }
                }
                $insert = $pdo->prepare("INSERT INTO usuarios (perfil_id, nome_completo, cpf, celular, email, senha, status) VALUES (?, 'Luhan - Administrador', ?, '(14) 90000-0000', ?, ?, 'ativo')");
                $insert->execute([$profileId, $cpf, $email, $hash]);
            }
            $done = $pdo->prepare('INSERT INTO cmdpcd_local_setup (chave) VALUES (?)');
            $done->execute(['admin_bootstrap_v1']);
        }

        $demoMarker = $pdo->prepare('SELECT chave FROM cmdpcd_local_setup WHERE chave = ?');
        $demoMarker->execute(['demo_pcds_v1']);
        if (!$demoMarker->fetchColumn()) {
            require_once __DIR__ . '/../database/demo_data.php';
            cmdpcdSeedDemoPcds($pdo);
            $doneDemo = $pdo->prepare('INSERT INTO cmdpcd_local_setup (chave) VALUES (?)');
            $doneDemo->execute(['demo_pcds_v1']);
        }
    }
}
