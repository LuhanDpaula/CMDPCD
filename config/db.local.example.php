<?php
// Copie este arquivo para "db.local.php" e preencha com os dados do servidor.
// NUNCA envie o db.local.php para o GitHub (já está no .gitignore).
return [
    // ---- MySQL (núcleo do sistema) ----
    'host'   => 'localhost',
    'dbname' => 'cmpcd_jau',
    'user'   => 'cmpcd_app',      // crie um usuário só para o site, sem privilégio de root
    'pass'   => 'troque-por-uma-senha-forte',

    // ---- MongoDB (chat, perguntas frequentes e pedidos de ajuda) ----
    // Local:  'mongodb://127.0.0.1:27017'
    // Atlas:  'mongodb+srv://usuario:senha@cluster0.xxxxx.mongodb.net/?retryWrites=true&w=majority'
    'mongo_uri' => 'mongodb://127.0.0.1:27017',
    'mongo_db'  => 'cmpcd_jau',
];
