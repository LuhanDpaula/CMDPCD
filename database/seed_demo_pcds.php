<?php
// Uso: php database/seed_demo_pcds.php (com MySQL do XAMPP iniciado).
if (PHP_SAPI !== 'cli') { http_response_code(403); exit('Acesso negado.'); }
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/demo_data.php';
try {
    $pdo = Database::getConnection();
    $added = cmdpcdSeedDemoPcds($pdo);
    cmdpcdWriteDemoCsv(__DIR__ . '/dados_demo_pcds.csv');
    echo "Registros fictícios adicionados: {$added}\nArquivo CSV: database/dados_demo_pcds.csv\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Falha: " . $e->getMessage() . "\n");
    exit(1);
}
