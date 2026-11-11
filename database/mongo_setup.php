<?php
// Configura o MongoDB: cria os índices e carrega as 30 perguntas frequentes iniciais.
// Uso (no terminal, na pasta do projeto):   php database/mongo_setup.php
// Para recarregar as perguntas do zero:      php database/mongo_setup.php --resetar-faq
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Acesso negado.');
}

require_once __DIR__ . '/../app/helpers/security.php';
require_once __DIR__ . '/../config/Mongo.php';

echo "=== CONFIGURAÇÃO DO MONGODB ===\n";
$driverMode = Mongo::hasNativeExtension() ? 'Extensão C (ext-mongodb)' : 'Cliente Nativo Wire Protocol (PHP Socket/BSON)';
echo "Driver em uso: $driverMode\n";

try {
    Mongo::manager()->executeCommand('admin', new MongoDB\Driver\Command(['ping' => 1]));
} catch (Throwable $e) {
    echo "ERRO: não foi possível conectar ao MongoDB: " . $e->getMessage() . "\n";
    exit(1);
}
echo "Conectado ao banco '" . Mongo::db() . "'.\n\n";

if (in_array('--resetar-faq', $argv, true)) {
    $n = Mongo::deleteMany('faq', []);
    echo "Perguntas frequentes apagadas: $n\n";
    Mongo::deleteOne('config_sistema', ['_id' => 'setup']);
}

echo "Índices:\n";
foreach (Mongo::createIndexes() as $linha) {
    echo "  $linha\n";
}

Mongo::deleteOne('config_sistema', ['_id' => 'setup']); // força o setup completo
Mongo::ensureSetup();

echo "\nColeções:\n";
foreach (['faq', 'pedidos_ajuda', 'perguntas_sem_resposta', 'resumo_mensal_pedidos', 'contadores', 'logs_auditoria'] as $c) {
    echo sprintf("  %-25s %d documento(s)\n", $c, Mongo::count($c));
}
echo "\nPronto!\n";
