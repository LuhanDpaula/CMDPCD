<?php
/**
 * ============================================================
 *  SISTEMA DE LOGS DE AUDITORIA E RASTREABILIDADE
 * ============================================================
 *  Armazenamento Primário: MongoDB (coleção "logs_auditoria")
 *  Armazenamento Fallback: MySQL (tabela "logs_auditoria") / Arquivo
 *
 *  Projetado para garantir conformidade com a LGPD e rastrear
 *  todas as ações dos múltiplos usuários no sistema mesmo em
 *  ambientes sem MongoDB instalado (ex.: laboratórios de faculdade).
 * ============================================================
 */

class AuditLog
{
    const COLL_MONGO = 'logs_auditoria';
    const TABELA_MYSQL = 'logs_auditoria';

    private static $mysqlTableChecked = false;
    private static $mongoSynced = false;

    /**
     * Sincroniza automaticamente logs que tenham sido gravados no MySQL (fallback)
     * para a coleção do MongoDB assim que o MongoDB estiver ativo.
     */
    public static function sincronizarFallbackParaMongo(): void
    {
        if (self::$mongoSynced || !class_exists('Mongo') || !Mongo::available()) {
            return;
        }
        self::$mongoSynced = true;

        try {
            $db = Database::getConnection();
            self::ensureMySqlTable($db);

            $meta = Mongo::findOne('config_sistema', ['_id' => 'audit_sync']);
            $lastId = (int)($meta['ultimo_mysql_id'] ?? 0);

            $stmt = $db->prepare("SELECT * FROM " . self::TABELA_MYSQL . " WHERE id > :lastId ORDER BY id ASC LIMIT 500");
            $stmt->execute([':lastId' => $lastId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$rows) {
                return;
            }

            $docs = [];
            $maxId = $lastId;
            foreach ($rows as $r) {
                $id = (int)$r['id'];
                if ($id > $maxId) {
                    $maxId = $id;
                }

                $user = null;
                if (!empty($r['usuario_id']) || !empty($r['usuario_nome'])) {
                    $user = [
                        'id'     => !empty($r['usuario_id']) ? (int)$r['usuario_id'] : null,
                        'nome'   => $r['usuario_nome'] ?? 'Usuário',
                        'email'  => $r['usuario_email'] ?? '',
                        'perfil' => $r['usuario_perfil'] ?? '',
                    ];
                }

                $detalhes = [];
                if (!empty($r['detalhes'])) {
                    $detalhes = json_decode($r['detalhes'], true) ?: [];
                }

                $ts = strtotime($r['criado_em'] ?? '') ?: time();
                $docs[] = [
                    'data_hora'  => new MongoDB\BSON\UTCDateTime($ts * 1000),
                    'usuario'    => $user,
                    'acao'       => $r['acao'] ?? '',
                    'modulo'     => $r['modulo'] ?? '',
                    'descricao'  => $r['descricao'] ?? '',
                    'detalhes'   => $detalhes,
                    'ip'         => $r['ip'] ?? '127.0.0.1',
                    'user_agent' => $r['user_agent'] ?? '',
                    'mysql_sync_id' => $id,
                ];
            }

            if ($docs) {
                Mongo::insertMany(self::COLL_MONGO, $docs);
                Mongo::updateOne(
                    'config_sistema',
                    ['_id' => 'audit_sync'],
                    ['$set' => ['ultimo_mysql_id' => $maxId, 'atualizado_em' => Mongo::now()]],
                    ['upsert' => true]
                );
            }
        } catch (Throwable $e) {
            error_log('[CMDPCD][AuditLog-Sync] ' . $e->getMessage());
        }
    }

    /**
     * Mapeamento de ações amigáveis para exibição.
     */
    const ACOES_LABELS = [
        'login_sucesso'               => 'Login efetuado com sucesso',
        'login_falha'                 => 'Tentativa de login inválida',
        'logout'                      => 'Logout do sistema',
        'pcd_cadastro_publico'        => 'Novo pré-cadastro PCD (Portal)',
        'pcd_criado_admin'            => 'PCD cadastrado pelo painel',
        'pcd_atualizado'              => 'Cadastro de PCD atualizado',
        'pcd_deferido'                => 'Cadastro de PCD deferido (aprovado)',
        'pcd_indeferido'              => 'Cadastro de PCD indeferido',
        'pcd_excluido'                => 'Cadastro de PCD excluído',
        'pcd_documento_visualizado'   => 'Visualização de documento/laudo PCD (LGPD)',
        'alerta_seguranca_exfiltracao'=> 'Alerta LGPD: Excesso de requisições a laudos',
        'alerta_seguranca_exclusao_falha' => 'Alerta: Senha incorreta ao tentar excluir usuário',
        'noticia_criada'              => 'Notícia publicada',
        'noticia_atualizada'          => 'Notícia atualizada',
        'noticia_excluida'            => 'Notícia excluída',
        'membro_criado'               => 'Membro do conselho cadastrado',
        'membro_atualizado'           => 'Membro do conselho atualizado',
        'membro_excluido'             => 'Membro do conselho excluído',
        'documento_publicado'         => 'Documento oficial publicado',
        'documento_atualizado'        => 'Documento oficial atualizado',
        'documento_excluido'          => 'Documento oficial excluído',
        'usuario_criado'              => 'Novo usuário de sistema cadastrado',
        'usuario_atualizado'          => 'Usuário de sistema atualizado',
        'usuario_excluido'            => 'Usuário de sistema excluído',
        'pedido_atendimento_registrado' => 'Atendimento em pedido registrado',
        'pedido_prioridade_alterada'  => 'Prioridade de pedido alterada',
        'pedido_excluido'             => 'Pedido de ajuda excluído (LGPD)',
        'mensagem_lida'               => 'Mensagem de contato marcada como lida',
        'mensagem_excluida'           => 'Mensagem de contato excluída',
        'faq_criada'                  => 'Pergunta frequente criada',
        'faq_atualizada'              => 'Pergunta frequente atualizada',
        'faq_excluida'                => 'Pergunta frequente excluída',
    ];

    const MODULOS_LABELS = [
        'auth'          => 'Autenticação',
        'pcds'          => 'PCDs e Documentos',
        'conteudos'     => 'Conteúdos e Transparência',
        'conselho'      => 'Membros do Conselho',
        'usuarios'      => 'Usuários do Sistema',
        'pedidos'       => 'Pedidos de Ajuda',
        'mensagens'     => 'Mensagens de Contato',
        'faq'           => 'Base de FAQ',
    ];

    /**
     * Registra uma ação de auditoria no sistema.
     * Tenta gravar primeiro no MongoDB; se indisponível, grava no MySQL como fallback.
     */
    public static function registrar(string $acao, string $modulo, array $detalhes = [], ?string $descricao = null): void
    {
        try {
            // Obtém dados do usuário atual na sessão (se houver)
            $user = null;
            if (!empty($_SESSION['user_id'])) {
                $user = [
                    'id'     => (int)$_SESSION['user_id'],
                    'nome'   => $_SESSION['user_name'] ?? 'Usuário',
                    'email'  => $_SESSION['user_email'] ?? '',
                    'perfil' => function_exists('getPerfilUsuario') ? getPerfilUsuario() : '',
                ];
            }

            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
            $desc = $descricao ?: (self::ACOES_LABELS[$acao] ?? ucfirst(str_replace('_', ' ', $acao)));

            // 1. TENTA GRAVAR NO MONGODB (DESTINO PRINCIPAL)
            if (class_exists('Mongo') && Mongo::available()) {
                self::sincronizarFallbackParaMongo();
                Mongo::insertOne(self::COLL_MONGO, [
                    'data_hora'  => Mongo::now(),
                    'usuario'    => $user,
                    'acao'       => $acao,
                    'modulo'     => $modulo,
                    'descricao'  => $desc,
                    'detalhes'   => $detalhes,
                    'ip'         => $ip,
                    'user_agent' => $userAgent,
                ]);
                return;
            }

            // 2. FALLBACK TRANSPARENTE: MYSQL
            self::registrarNoMySql($user, $acao, $modulo, $desc, $detalhes, $ip, $userAgent);

        } catch (Throwable $e) {
            // Em auditoria, falha no log NUNCA deve travar a requisição principal do usuário
            error_log('[CMDPCD][AuditLog] Falha ao registrar log: ' . $e->getMessage());
        }
    }

    /**
     * Gravação segura no MySQL para ambientes sem MongoDB.
     */
    private static function registrarNoMySql(?array $user, string $acao, string $modulo, string $desc, array $detalhes, string $ip, string $userAgent): void
    {
        try {
            $db = Database::getConnection();
            self::ensureMySqlTable($db);

            $stmt = $db->prepare("
                INSERT INTO " . self::TABELA_MYSQL . " (
                    usuario_id, usuario_nome, usuario_email, usuario_perfil,
                    acao, modulo, descricao, detalhes, ip, user_agent, criado_em
                ) VALUES (
                    :uid, :unome, :uemail, :uperfil,
                    :acao, :modulo, :descricao, :detalhes, :ip, :user_agent, NOW()
                )
            ");

            $stmt->execute([
                ':uid'        => $user['id'] ?? null,
                ':unome'      => $user['nome'] ?? null,
                ':uemail'     => $user['email'] ?? null,
                ':uperfil'    => $user['perfil'] ?? null,
                ':acao'       => $acao,
                ':modulo'     => $modulo,
                ':descricao'  => $desc,
                ':detalhes'   => json_encode($detalhes, JSON_UNESCAPED_UNICODE),
                ':ip'         => $ip,
                ':user_agent' => $userAgent,
            ]);
        } catch (Throwable $e) {
            error_log('[CMDPCD][AuditLog-MySQL] ' . $e->getMessage());
        }
    }

    /**
     * Garante a criação da tabela de fallback no MySQL automaticamente.
     */
    public static function ensureMySqlTable($db): void
    {
        if (self::$mysqlTableChecked) return;
        self::$mysqlTableChecked = true;

        try {
            $db->exec("
                CREATE TABLE IF NOT EXISTS `" . self::TABELA_MYSQL . "` (
                    `id` bigint(20) NOT NULL AUTO_INCREMENT,
                    `usuario_id` bigint(20) DEFAULT NULL,
                    `usuario_nome` varchar(255) DEFAULT NULL,
                    `usuario_email` varchar(255) DEFAULT NULL,
                    `usuario_perfil` varchar(50) DEFAULT NULL,
                    `acao` varchar(100) NOT NULL,
                    `modulo` varchar(50) NOT NULL,
                    `descricao` varchar(255) DEFAULT NULL,
                    `detalhes` text DEFAULT NULL,
                    `ip` varchar(45) NOT NULL,
                    `user_agent` varchar(255) DEFAULT NULL,
                    `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_logs_criado_em` (`criado_em`),
                    KEY `idx_logs_usuario` (`usuario_id`),
                    KEY `idx_logs_modulo` (`modulo`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
        } catch (Throwable $e) {
            error_log('[CMDPCD][AuditLog-Table] ' . $e->getMessage());
        }
    }

    /**
     * Retorna a lista de logs formatada com base no motor ativo (Mongo ou MySQL).
     */
    public static function listar(array $filtros = []): array
    {
        $limite = (int)($filtros['limit'] ?? 150);

        if (class_exists('Mongo') && Mongo::available()) {
            self::sincronizarFallbackParaMongo();
            return self::listarMongo($filtros, $limite);
        }

        return self::listarMySql($filtros, $limite);
    }

    private static function listarMongo(array $f, int $limit): array
    {
        $query = [];

        if (!empty($f['modulo'])) {
            $query['modulo'] = $f['modulo'];
        }

        if (!empty($f['acao'])) {
            $query['acao'] = $f['acao'];
        }

        if (!empty($f['usuario_id'])) {
            $query['usuario.id'] = (int)$f['usuario_id'];
        }

        if (!empty($f['busca'])) {
            $regex = Mongo::regex($f['busca']);
            $query['$or'] = [
                ['descricao' => $regex],
                ['acao' => $regex],
                ['usuario.nome' => $regex],
                ['usuario.email' => $regex],
                ['ip' => $regex],
            ];
        }

        $docs = Mongo::find(self::COLL_MONGO, $query, [
            'sort'  => ['data_hora' => -1],
            'limit' => $limit,
        ]);

        $normalizados = [];
        foreach ($docs as $doc) {
            $dtStr = Mongo::date($doc['data_hora'] ?? null, 'd/m/Y H:i:s');
            $normalizados[] = [
                'id'             => (string)($doc['_id'] ?? ''),
                'data_hora_str'  => $dtStr,
                'timestamp'      => ($doc['data_hora'] instanceof MongoDB\BSON\UTCDateTime) ? $doc['data_hora']->toDateTime()->getTimestamp() : time(),
                'usuario_id'     => $doc['usuario']['id'] ?? null,
                'usuario_nome'   => $doc['usuario']['nome'] ?? 'Visitante/Público',
                'usuario_email'  => $doc['usuario']['email'] ?? '',
                'usuario_perfil' => $doc['usuario']['perfil'] ?? '',
                'acao'           => $doc['acao'] ?? '',
                'acao_label'     => self::ACOES_LABELS[$doc['acao'] ?? ''] ?? ($doc['acao'] ?? ''),
                'modulo'         => $doc['modulo'] ?? '',
                'modulo_label'   => self::MODULOS_LABELS[$doc['modulo'] ?? ''] ?? ($doc['modulo'] ?? ''),
                'descricao'      => $doc['descricao'] ?? '',
                'detalhes'       => is_array($doc['detalhes'] ?? null) ? $doc['detalhes'] : [],
                'ip'             => $doc['ip'] ?? '-',
                'user_agent'     => $doc['user_agent'] ?? '',
                'origem'         => 'MongoDB',
            ];
        }

        return $normalizados;
    }

    private static function listarMySql(array $f, int $limit): array
    {
        try {
            $db = Database::getConnection();
            self::ensureMySqlTable($db);

            $sql = "SELECT * FROM " . self::TABELA_MYSQL . " WHERE 1=1";
            $params = [];

            if (!empty($f['modulo'])) {
                $sql .= " AND modulo = :modulo";
                $params[':modulo'] = $f['modulo'];
            }

            if (!empty($f['acao'])) {
                $sql .= " AND acao = :acao";
                $params[':acao'] = $f['acao'];
            }

            if (!empty($f['usuario_id'])) {
                $sql .= " AND usuario_id = :uid";
                $params[':uid'] = (int)$f['usuario_id'];
            }

            if (!empty($f['busca'])) {
                $sql .= " AND (descricao LIKE :busca OR usuario_nome LIKE :busca OR ip LIKE :busca OR acao LIKE :busca)";
                $params[':busca'] = '%' . $f['busca'] . '%';
            }

            $sql .= " ORDER BY criado_em DESC LIMIT " . (int)$limit;

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $normalizados = [];
            foreach ($rows as $r) {
                $detalhes = [];
                if (!empty($r['detalhes'])) {
                    $detalhes = json_decode($r['detalhes'], true) ?: [];
                }

                $normalizados[] = [
                    'id'             => (string)$r['id'],
                    'data_hora_str'  => date('d/m/Y H:i:s', strtotime($r['criado_em'])),
                    'timestamp'      => strtotime($r['criado_em']),
                    'usuario_id'     => $r['usuario_id'],
                    'usuario_nome'   => $r['usuario_nome'] ?: 'Visitante/Público',
                    'usuario_email'  => $r['usuario_email'] ?: '',
                    'usuario_perfil' => $r['usuario_perfil'] ?: '',
                    'acao'           => $r['acao'],
                    'acao_label'     => self::ACOES_LABELS[$r['acao']] ?? $r['acao'],
                    'modulo'         => $r['modulo'],
                    'modulo_label'   => self::MODULOS_LABELS[$r['modulo']] ?? $r['modulo'],
                    'descricao'      => $r['descricao'],
                    'detalhes'       => $detalhes,
                    'ip'             => $r['ip'],
                    'user_agent'     => $r['user_agent'],
                    'origem'         => 'MySQL (Fallback)',
                ];
            }

            return $normalizados;
        } catch (Throwable $e) {
            error_log('[CMDPCD][AuditLog-ListMySql] ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Retorna indicadores estatísticos resumidos para o topo da tela de logs.
     */
    public static function estatisticas(): array
    {
        $engine = self::getMotorAtivo();
        $totalLogs = 0;
        $totalHoje = 0;
        $usuariosAtivos = 0;

        if (class_exists('Mongo') && Mongo::available()) {
            try {
                self::sincronizarFallbackParaMongo();
                $totalLogs = Mongo::count(self::COLL_MONGO);
                $inicioHoje = new MongoDB\BSON\UTCDateTime(strtotime('today midnight') * 1000);
                $totalHoje = Mongo::count(self::COLL_MONGO, ['data_hora' => ['$gte' => $inicioHoje]]);
            } catch (Throwable $e) {}
        } else {
            try {
                $db = Database::getConnection();
                self::ensureMySqlTable($db);
                $totalLogs = (int)$db->query("SELECT COUNT(*) FROM " . self::TABELA_MYSQL)->fetchColumn();
                $totalHoje = (int)$db->query("SELECT COUNT(*) FROM " . self::TABELA_MYSQL . " WHERE DATE(criado_em) = CURDATE()")->fetchColumn();
            } catch (Throwable $e) {}
        }

        return [
            'motor_ativo' => $engine,
            'total_logs'  => $totalLogs,
            'total_hoje'  => $totalHoje,
        ];
    }

    public static function getMotorAtivo(): string
    {
        if (class_exists('Mongo') && Mongo::available()) {
            return 'MongoDB';
        }
        return 'MySQL (Fallback)';
    }
}
