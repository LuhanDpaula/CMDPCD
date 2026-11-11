<?php
/**
 * Pedidos de ajuda feitos pelo chat - coleção MongoDB "pedidos_ajuda".
 *
 * Exemplo de documento:
 * {
 *   _id: ObjectId,
 *   protocolo: "2026-0007",
 *   nome: "Maria Souza", telefone: "14999990000", bairro: "Centro", endereco: null,
 *   assunto: "transporte", mensagem: "Preciso de ajuda com o passe livre",
 *   para_quem: "propria", preferencia_contato: "whatsapp", melhor_horario: "manha",
 *   consentimento: { aceito: true, data: ISODate, texto: "..." },
 *   status: "em_atendimento",
 *   prioritario: false,                 // marcação INTERNA, definida só pelo Conselho
 *   historico: [
 *     { data: ISODate, acao: "Pedido registrado pelo site", por: "Sistema" },
 *     { data: ISODate, acao: "Ligação realizada", obs: "Retornar sexta", por: "Carlos", status: "em_atendimento" }
 *   ],
 *   criado_em: ISODate,
 *   primeiro_contato_em: ISODate,       // usado no cálculo de tempo médio de atendimento
 *   encerrado_em: ISODate               // só existe quando concluído/sem retorno -> índice TTL apaga após 1 ano
 * }
 */
class PedidoAjuda
{
    const COLL = 'pedidos_ajuda';
    const COLL_RESUMO = 'resumo_mensal_pedidos';

    const ASSUNTOS = [
        'cadastro'      => 'Cadastro de PCD',
        'beneficios'    => 'Benefícios (BPC/LOAS e outros)',
        'saude'         => 'Saúde e reabilitação',
        'educacao'      => 'Educação',
        'trabalho'      => 'Trabalho e emprego',
        'transporte'    => 'Transporte e mobilidade',
        'acessibilidade'=> 'Acessibilidade (calçadas, prédios)',
        'direitos'      => 'Discriminação ou violação de direitos',
        'outro'         => 'Outro assunto',
    ];

    const STATUS = [
        'aguardando'     => 'Aguardando contato',
        'em_atendimento' => 'Em atendimento',
        'concluido'      => 'Concluído',
        'sem_retorno'    => 'Sem retorno',
    ];

    /** Status que encerram o pedido (passam a contar o prazo de exclusão de 1 ano) */
    const STATUS_ENCERRADOS = ['concluido', 'sem_retorno'];

    const PREFERENCIAS = ['ligacao' => 'Ligação', 'whatsapp' => 'WhatsApp'];
    const HORARIOS = ['manha' => 'Manhã', 'tarde' => 'Tarde', 'qualquer' => 'Qualquer horário'];
    const PARA_QUEM = ['propria' => 'A própria pessoa', 'outra' => 'Familiar / responsável por outra pessoa'];

    const TEXTO_CONSENTIMENTO = 'Autorizo o Conselho Municipal da Pessoa com Deficiência de Jahu a usar meus dados apenas para entrar em contato comigo sobre este pedido. Os dados serão excluídos 1 ano após o encerramento do atendimento.';

    public function __construct()
    {
        if (!Mongo::available()) {
            throw new AppException('O serviço de pedidos está temporariamente indisponível.');
        }
    }

    // ------------------------------------------------------------
    // Criação (site público)
    // ------------------------------------------------------------
    /** Valida o formulário do chat. Todo valor é convertido para texto (evita injeção de operadores NoSQL). */
    public function validar(array $p): array
    {
        $nome = limit_str($p['nome'] ?? '', 120);
        $telefone = only_digits($p['telefone'] ?? '');
        $bairro = limit_str($p['bairro'] ?? '', 80);
        $endereco = limit_str($p['endereco'] ?? '', 200);
        $mensagem = limit_str($p['mensagem'] ?? '', 1000);
        $assunto = (string)($p['assunto'] ?? '');

        if (mb_strlen($nome) < 3) throw new AppException('Informe seu nome.');
        if (strlen($telefone) < 10 || strlen($telefone) > 11) throw new AppException('Informe um telefone válido com DDD.');
        if ($bairro === '') throw new AppException('Informe seu bairro.');
        if (!isset(self::ASSUNTOS[$assunto])) throw new AppException('Escolha o assunto.');
        if (mb_strlen($mensagem) < 5) throw new AppException('Conte rapidamente como o Conselho pode ajudar.');
        if (empty($p['consentimento'])) throw new AppException('É preciso autorizar o uso dos dados para o Conselho entrar em contato.');

        return [
            'nome' => $nome,
            'telefone' => $telefone,
            'bairro' => $bairro,
            'endereco' => $endereco !== '' ? $endereco : null,
            'assunto' => $assunto,
            'mensagem' => $mensagem,
            'para_quem' => in_list((string)($p['para_quem'] ?? ''), array_keys(self::PARA_QUEM), 'propria'),
            'preferencia_contato' => in_list((string)($p['preferencia_contato'] ?? ''), array_keys(self::PREFERENCIAS), 'ligacao'),
            'melhor_horario' => in_list((string)($p['melhor_horario'] ?? ''), array_keys(self::HORARIOS), 'qualquer'),
        ];
    }

    public function create(array $dados): string
    {
        $now = Mongo::now();
        $ano = date('Y');
        // Protocolo sequencial por ano, gerado de forma atômica: 2026-0001, 2026-0002...
        $protocolo = $ano . '-' . str_pad((string)Mongo::nextSequence('protocolo_' . $ano), 4, '0', STR_PAD_LEFT);

        Mongo::insertOne(self::COLL, $dados + [
            'protocolo' => $protocolo,
            'status' => 'aguardando',
            'prioritario' => false,
            'consentimento' => ['aceito' => true, 'data' => $now, 'texto' => self::TEXTO_CONSENTIMENTO],
            'historico' => [
                ['data' => $now, 'acao' => 'Pedido registrado pelo site', 'por' => 'Sistema'],
            ],
            'criado_em' => $now,
        ]);
        return $protocolo;
    }

    // ------------------------------------------------------------
    // Painel administrativo
    // ------------------------------------------------------------
    /**
     * Fila de atendimento. Por padrão mostra os pedidos em aberto, do mais antigo para o mais novo
     * (ordem de chegada). Pedidos marcados como prioritários pelo Conselho aparecem primeiro.
     */
    public function lista(array $f): array
    {
        $filtro = [];
        $status = (string)($f['status'] ?? 'abertos');
        if ($status === 'abertos') {
            $filtro['status'] = ['$in' => ['aguardando', 'em_atendimento']];
        } elseif (isset(self::STATUS[$status])) {
            $filtro['status'] = $status;
        } // "todos" = sem filtro

        if (isset(self::ASSUNTOS[$f['assunto'] ?? ''])) {
            $filtro['assunto'] = $f['assunto'];
        }
        $bairro = limit_str($f['bairro'] ?? '', 80);
        if ($bairro !== '') {
            $filtro['bairro'] = Mongo::regex($bairro);
        }
        $busca = limit_str($f['q'] ?? '', 80);
        if ($busca !== '') {
            $filtro['$or'] = [
                ['nome' => Mongo::regex($busca)],
                ['protocolo' => Mongo::regex($busca)],
                ['telefone' => Mongo::regex(only_digits($busca) ?: $busca)],
            ];
        }

        $encerrados = in_array($status, self::STATUS_ENCERRADOS, true) || $status === 'todos';
        $sort = $encerrados ? ['criado_em' => -1] : ['prioritario' => -1, 'criado_em' => 1];

        return Mongo::find(self::COLL, $filtro, [
            'sort' => $sort,
            'limit' => 300,
            'projection' => ['historico' => 0, 'consentimento' => 0],
        ]);
    }

    public function find(string $id): ?array
    {
        $oid = Mongo::id($id);
        return $oid ? Mongo::findOne(self::COLL, ['_id' => $oid]) : null;
    }

    public function contarPendentes(): int
    {
        return Mongo::count(self::COLL, ['status' => 'aguardando']);
    }

    /**
     * Registra um atendimento no histórico.
     *  - $push adiciona o item ao array "historico"
     *  - $set muda o status (e grava encerrado_em quando o pedido é encerrado)
     *  - $unset remove encerrado_em se o pedido for reaberto (o TTL deixa de valer)
     */
    public function registrar(string $id, string $acao, string $obs, string $novoStatus, string $usuario): void
    {
        $pedido = $this->find($id);
        if (!$pedido) throw new AppException('Pedido não encontrado.');

        $acao = limit_str($acao, 120);
        $obs = limit_str($obs, 1000);
        if ($acao === '') throw new AppException('Informe o que foi feito.');
        if (!isset(self::STATUS[$novoStatus])) $novoStatus = $pedido['status'];

        $now = Mongo::now();
        $item = ['data' => $now, 'acao' => $acao, 'por' => $usuario];
        if ($obs !== '') $item['obs'] = $obs;
        if ($novoStatus !== $pedido['status']) $item['status'] = $novoStatus;

        $set = ['status' => $novoStatus, 'atualizado_em' => $now];
        $update = ['$push' => ['historico' => $item]];

        // Primeira ação do Conselho = primeiro contato (para medir o tempo de espera)
        if (empty($pedido['primeiro_contato_em'])) {
            $set['primeiro_contato_em'] = $now;
        }

        if (in_array($novoStatus, self::STATUS_ENCERRADOS, true)) {
            if (empty($pedido['encerrado_em'])) {
                $set['encerrado_em'] = $now; // começa a contar 1 ano para a exclusão automática
            }
        } elseif (!empty($pedido['encerrado_em'])) {
            $update['$unset'] = ['encerrado_em' => ''];
        }

        $update['$set'] = $set;
        Mongo::updateOne(self::COLL, ['_id' => $pedido['_id']], $update);
    }

    /** Marca/desmarca como prioritário (decisão exclusiva do Conselho) */
    public function alternarPrioridade(string $id, string $usuario): ?bool
    {
        $pedido = $this->find($id);
        if (!$pedido) return null;
        $novo = empty($pedido['prioritario']);
        Mongo::updateOne(self::COLL, ['_id' => $pedido['_id']], [
            '$set' => ['prioritario' => $novo],
            '$push' => ['historico' => [
                'data' => Mongo::now(),
                'acao' => $novo ? 'Marcado como prioritário' : 'Prioridade removida',
                'por' => $usuario,
            ]],
        ]);
        return $novo;
    }

    /** Exclusão manual (ex.: a pessoa pediu para apagar seus dados - direito previsto na LGPD) */
    public function delete(string $id): bool
    {
        $oid = Mongo::id($id);
        return $oid ? Mongo::deleteOne(self::COLL, ['_id' => $oid]) > 0 : false;
    }

    // ------------------------------------------------------------
    // Estatísticas (Aggregation Pipeline)
    // ------------------------------------------------------------
    public function estatisticas(): array
    {
        // Totais por status
        $porStatus = array_column(Mongo::aggregate(self::COLL, [
            ['$group' => ['_id' => '$status', 'total' => ['$sum' => 1]]],
        ]), 'total', '_id');

        // Pedidos por assunto (do maior para o menor)
        $porAssunto = Mongo::aggregate(self::COLL, [
            ['$group' => ['_id' => '$assunto', 'total' => ['$sum' => 1]]],
            ['$sort' => ['total' => -1]],
        ]);

        // Bairros com mais pedidos ($toUpper junta "Centro" e "centro")
        try {
            $porBairro = Mongo::aggregate(self::COLL, [
                ['$group' => ['_id' => ['$toUpper' => '$bairro'], 'total' => ['$sum' => 1]]],
                ['$sort' => ['total' => -1]],
                ['$limit' => 5],
            ]);
        } catch (Throwable $e) {
            $porBairro = $this->contarEmPhp('bairro', fn($v) => mb_strtoupper((string)$v), 5);
        }

        // Tempo médio (em dias) entre o pedido e o primeiro contato do Conselho
        try {
            $tempo = Mongo::aggregate(self::COLL, [
                ['$match' => ['primeiro_contato_em' => ['$exists' => true]]],
                ['$project' => ['espera' => ['$subtract' => ['$primeiro_contato_em', '$criado_em']]]],
                ['$group' => ['_id' => null, 'media_ms' => ['$avg' => '$espera']]],
            ]);
            $mediaMs = $tempo[0]['media_ms'] ?? null;
        } catch (Throwable $e) {
            $esperas = [];
            foreach (Mongo::find(self::COLL, ['primeiro_contato_em' => ['$exists' => true]], ['projection' => ['criado_em' => 1, 'primeiro_contato_em' => 1]]) as $d) {
                $esperas[] = (int)(string)$d['primeiro_contato_em'] - (int)(string)$d['criado_em'];
            }
            $mediaMs = $esperas ? array_sum($esperas) / count($esperas) : null;
        }
        $mediaDias = $mediaMs !== null ? round($mediaMs / 86400000, 1) : null;

        // Pedido mais antigo ainda aguardando
        $maisAntigo = Mongo::findOne(self::COLL, ['status' => 'aguardando'], ['sort' => ['criado_em' => 1], 'projection' => ['criado_em' => 1]]);
        $diasMaisAntigo = $maisAntigo ? (int)floor((time() - $maisAntigo['criado_em']->toDateTime()->getTimestamp()) / 86400) : null;

        return [
            'por_status' => $porStatus,
            'por_assunto' => $porAssunto,
            'por_bairro' => $porBairro,
            'media_dias_primeiro_contato' => $mediaDias,
            'dias_pedido_mais_antigo' => $diasMaisAntigo,
        ];
    }

    /** Contagem alternativa em PHP (para servidores sem algum operador de aggregation) */
    private function contarEmPhp(string $campo, callable $normaliza, int $limite): array
    {
        $cont = [];
        foreach (Mongo::find(self::COLL, [], ['projection' => [$campo => 1]]) as $d) {
            $k = $normaliza($d[$campo] ?? '');
            $cont[$k] = ($cont[$k] ?? 0) + 1;
        }
        arsort($cont);
        $out = [];
        foreach (array_slice($cont, 0, $limite, true) as $k => $v) {
            $out[] = ['_id' => $k, 'total' => $v];
        }
        return $out;
    }

    /**
     * Resumo mensal SEM dados pessoais, guardado em outra coleção com $merge.
     * Como os pedidos são apagados 1 ano após o encerramento (LGPD), este resumo
     * preserva o histórico de números para o Conselho (ex.: "out/2026: 23 pedidos").
     * Só recalcula o mês atual e o anterior; meses antigos ficam congelados.
     */
    public function atualizarResumoMensal(): void
    {
        $tz = new DateTimeZone('America/Sao_Paulo');
        $inicio = new DateTime('first day of last month 00:00:00', $tz);
        $desde = new MongoDB\BSON\UTCDateTime($inicio->getTimestamp() * 1000);
        try {
            Mongo::aggregate(self::COLL, [
                ['$match' => ['criado_em' => ['$gte' => $desde]]],
                ['$group' => [
                    '_id' => ['$dateToString' => ['format' => '%Y-%m', 'date' => '$criado_em', 'timezone' => 'America/Sao_Paulo']],
                    'total' => ['$sum' => 1],
                    'concluidos' => ['$sum' => ['$cond' => [['$eq' => ['$status', 'concluido']], 1, 0]]],
                    'sem_retorno' => ['$sum' => ['$cond' => [['$eq' => ['$status', 'sem_retorno']], 1, 0]]],
                    'assuntos' => ['$push' => '$assunto'],
                ]],
                ['$set' => ['atualizado_em' => '$$NOW']],
                ['$merge' => ['into' => self::COLL_RESUMO, 'on' => '_id', 'whenMatched' => 'replace', 'whenNotMatched' => 'insert']],
            ]);
        } catch (Throwable $e) {
            // Alternativa: calcula em PHP e grava com upsert (mesmo resultado do $merge)
            try {
                $meses = [];
                foreach (Mongo::find(self::COLL, ['criado_em' => ['$gte' => $desde]], ['projection' => ['criado_em' => 1, 'status' => 1, 'assunto' => 1]]) as $d) {
                    $dt = $d['criado_em']->toDateTime()->setTimezone($tz);
                    $m = $dt->format('Y-m');
                    $meses[$m] = $meses[$m] ?? ['total' => 0, 'concluidos' => 0, 'sem_retorno' => 0, 'assuntos' => []];
                    $meses[$m]['total']++;
                    if ($d['status'] === 'concluido') $meses[$m]['concluidos']++;
                    if ($d['status'] === 'sem_retorno') $meses[$m]['sem_retorno']++;
                    $meses[$m]['assuntos'][] = $d['assunto'];
                }
                foreach ($meses as $mes => $dados) {
                    Mongo::updateOne(self::COLL_RESUMO, ['_id' => $mes],
                        ['$set' => $dados + ['atualizado_em' => Mongo::now()]], ['upsert' => true]);
                }
            } catch (Throwable $e2) {
                error_log('[CMDPCD][Pedidos] resumo mensal: ' . $e2->getMessage());
            }
        }
    }

    public function resumoMensal(int $meses = 12): array
    {
        try {
            $rows = Mongo::find(self::COLL_RESUMO, [], ['sort' => ['_id' => -1], 'limit' => $meses]);
        } catch (Throwable $e) {
            return [];
        }
        foreach ($rows as &$r) {
            $r['por_assunto'] = array_count_values($r['assuntos'] ?? []);
            arsort($r['por_assunto']);
        }
        return array_reverse($rows);
    }
}
