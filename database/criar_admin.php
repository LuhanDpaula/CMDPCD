<?php
/**
 * ============================================================
 *  CRIAR ADMINISTRADOR DE PRODUÇÃO (USO EXCLUSIVO VIA TERMINAL)
 * ============================================================
 *  Uso:
 *    php database/criar_admin.php "Nome Completo" "admin@dominio.org.br" "00000000000" "SenhaForte@2026" "(14) 99999-9999"
 * ============================================================
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Acesso negado.');
}

require_once __DIR__ . '/../app/helpers/security.php';
require_once __DIR__ . '/../config/Database.php';

$nome    = trim($argv[1] ?? '');
$email   = trim($argv[2] ?? '');
$cpf     = only_digits($argv[3] ?? '');
$senha   = (string)($argv[4] ?? '');
$celular = trim($argv[5] ?? '(14) 3624-5077');

if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($cpf) !== 11 || strlen($senha) < 8) {
    fwrite(STDERR, "Uso:\n");
    fwrite(STDERR, "  php database/criar_admin.php \"Nome Completo\" \"email@dominio.org.br\" \"CPF_11_DIGITOS\" \"SenhaForte8+Chars\" \"(14) 99999-9999\"\n\n");
    fwrite(STDERR, "Requisitos:\n");
    fwrite(STDERR, "  - E-mail válido\n");
    fwrite(STDERR, "  - CPF com 11 dígitos\n");
    fwrite(STDERR, "  - Senha com no mínimo 8 caracteres\n");
    exit(1);
}

try {
    $pdo = Database::getConnection();
    $perfilId = (int)$pdo->query("SELECT id FROM perfis_acesso WHERE nome = 'admin_total' LIMIT 1")->fetchColumn();
    if (!$perfilId) {
        $pdo->exec("INSERT INTO perfis_acesso (nome, descricao) VALUES ('admin_total', 'Administrador com acesso total ao sistema')");
        $perfilId = (int)$pdo->lastInsertId();
    }

    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? OR cpf = ? LIMIT 1");
    $stmt->execute([$email, $cpf]);
    $existingId = $stmt->fetchColumn();

    if ($existingId) {
        $upd = $pdo->prepare("UPDATE usuarios SET perfil_id = ?, nome_completo = ?, cpf = ?, celular = ?, email = ?, senha = ?, status = 'ativo', deletado_em = NULL WHERE id = ?");
        $upd->execute([$perfilId, $nome, $cpf, $celular, $email, $hash, $existingId]);
        echo "Administrador atualizado com sucesso (ID {$existingId}): {$email}\n";
    } else {
        $ins = $pdo->prepare("INSERT INTO usuarios (perfil_id, nome_completo, cpf, celular, email, senha, status) VALUES (?, ?, ?, ?, ?, ?, 'ativo')");
        $ins->execute([$perfilId, $nome, $cpf, $celular, $email, $hash]);
        echo "Administrador criado com sucesso (ID " . $pdo->lastInsertId() . "): {$email}\n";
    }
} catch (Throwable $e) {
    fwrite(STDERR, "Erro ao criar administrador: " . $e->getMessage() . "\n");
    exit(1);
}
