<?php
/**
 * ============================================================
 *  CONFIGURAÇÃO DE PRODUÇÃO / SERVIDOR - CMDPCD Jahu
 * ============================================================
 *  1. Copie este arquivo para "config/db.local.php" no servidor.
 *  2. Preencha com os dados do banco MySQL criado na hospedagem.
 *  3. NUNCA envie o arquivo "config/db.local.php" para o GitHub
 *     (ele já está protegido no .gitignore e bloqueado no .htaccess).
 * ============================================================
 */
return [
    // Ambiente: use 'production' no servidor online (desativa usuários/PCDs fictícios)
    'env'       => 'production',
    'seed_demo' => false,

    // ---- MySQL (núcleo do sistema) ----
    'host'   => 'localhost',          // ou host informado pela hospedagem (ex.: mysql.hostinger.com)
    'port'   => 3306,
    'dbname' => 'nome_do_banco',      // ex.: u123456789_cmpcd_jau
    'user'   => 'usuario_do_banco',   // crie um usuário só para o site, sem privilégio de root
    'pass'   => 'TROQUE_POR_UMA_SENHA_FORTE',

    // ---- Armazenamento documental (chat, FAQ, pedidos de ajuda e logs) ----
    // 'json_local' -> recomendado para hospedagem compartilhada sem MongoDB (rápido, sem timeout)
    // 'auto'       -> tenta conectar em mongo_uri e usa json_local automaticamente se indisponível
    'mongo_driver' => 'json_local',
    'mongo_uri'    => '',             // ex.: 'mongodb+srv://usuario:senha@cluster.mongodb.net/?retryWrites=true&w=majority'
    'mongo_db'     => 'cmpcd_jau',
];
