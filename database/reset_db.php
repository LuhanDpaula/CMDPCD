<?php
// Segurança: este script APAGA/RECRIA o banco. Só pode rodar pelo terminal, nunca pelo navegador.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Acesso negado.');
}
// PHP CLI Script to drop, recreate, migrate and seed database

$host = 'localhost';
$dbname = 'cmpcd_jau';
$user = 'root';
$passwords = ['root', ''];
$connected = false;
$pdo = null;

echo "=== DATABASE RESET INITIATED ===\n";

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
    echo "Dropping database '$dbname' if exists...\n";
    $pdo->exec("DROP DATABASE IF EXISTS `$dbname`");
    
    echo "Creating database '$dbname'...\n";
    $pdo->exec("CREATE DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // Execute migrate
    echo "\n";
    require __DIR__ . '/migrate.php';
    
    // Execute seeder
    echo "\n";
    require __DIR__ . '/seeder.php';
    
    echo "\n=== DATABASE RESET AND SEEDING COMPLETED SUCCESSFULLY! ===\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
