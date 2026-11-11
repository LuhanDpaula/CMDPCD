<?php
// Segurança: este script APAGA/RECRIA o banco. Só pode rodar pelo terminal, nunca pelo navegador.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Acesso negado.');
}
// PHP CLI Script to execute seeds (initial/example data)

$host = 'localhost';
$dbname = 'cmpcd_jau';
$user = 'root';
$passwords = ['root', ''];
$connected = false;
$pdo = null;

echo "--- RUNNING SEEDERS ---\n";

foreach ($passwords as $password) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $connected = true;
        break;
    } catch (PDOException $e) {
        // Tenta a próxima senha
    }
}

if (!$connected) {
    echo "ERROR: Could not connect to database '$dbname' with user 'root'. Make sure migrations have been executed.\n";
    exit(1);
}

try {
    $sqlPath = __DIR__ . '/seeder.sql';
    if (!file_exists($sqlPath)) {
        echo "ERROR: Seeder SQL file not found at $sqlPath\n";
        exit(1);
    }

    echo "Reading seed data from database/seeder.sql...\n";
    $sql = file_get_contents($sqlPath);

    echo "Executing seeders...\n";
    $pdo->exec($sql);

    echo "SUCCESS: Database seeded successfully!\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
