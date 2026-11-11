<?php
// Segurança: este script APAGA/RECRIA o banco. Só pode rodar pelo terminal, nunca pelo navegador.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Acesso negado.');
}
// PHP CLI Script to execute migrations (schema structure only)

$host = 'localhost';
$dbname = 'cmpcd_jau';
$user = 'root';
$passwords = ['root', ''];
$connected = false;
$pdo = null;

echo "--- RUNNING MIGRATIONS ---\n";

foreach ($passwords as $password) {
    try {
        $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $connected = true;
        break;
    } catch (PDOException $e) {
        // Tenta a próxima senha
    }
}

if (!$connected) {
    echo "ERROR: Could not connect to MySQL server with user 'root'.\n";
    exit(1);
}

try {
    $sqlPath = __DIR__ . '/CMDPCD_PI_BD.sql';
    if (!file_exists($sqlPath)) {
        echo "ERROR: Migration file not found at $sqlPath\n";
        exit(1);
    }

    echo "Reading schema from database/CMDPCD_PI_BD.sql...\n";
    $sql = file_get_contents($sqlPath);

    echo "Executing schema migration...\n";
    $pdo->exec($sql);

    echo "SUCCESS: Database schema created successfully!\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
