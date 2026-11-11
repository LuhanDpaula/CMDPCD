<?php
/**
 * Perguntas frequentes do chat "Posso ajudar?" - coleção MongoDB "faq".
 *
 * Exemplo de documento:
 * {
 *   _id: ObjectId,
 *   categoria: "direitos",
 *   pergunta: "O que é o BPC/LOAS?",
 *   resposta: "...",
 *   palavras_chave: ["bpc", "loas", "inss"],
 *   ordem: 17,
 *   ativo: true,
 *   util: { sim: 12, nao: 1 },
 *   visualizacoes: 87,
 *   criado_em: ISODate, atualizado_em: ISODate
 * }
 */
class Faq
{
    const COLL = 'faq';

    /** Categorias fixas do chat (chave => nome exibido) */
    const CATEGORIAS = [
        'conselho'  => 'Sobre o Conselho',
        'cadastro'  => 'Cadastro de PCD',
        'direitos'  => 'Direitos e benefícios',
        'denuncias' => 'Denúncias e ajuda',
        'site'      => 'Uso do site',
    ];

    /** Palavras comuns que não ajudam na busca */
    const STOPWORDS = ['a','o','as','os','de','da','do','das','dos','e','é','em','no','na','nos','nas','um','uma','para','pra','por',
        'com','como','que','qual','quais','eu','meu','minha','me','se','sobre','tem','ter','tenho','posso','quero','onde','quando','ao','à',
        'consigo','conseguir','consegue','faço','fazer','faz','pode','podem','preciso','precisa','gostaria','saber','queria','isso','esse','essa',
        'ola','olá','bom','boa','dia','tarde','noite','obrigado','obrigada','por favor','favor','ajuda','duvida','dúvida','sim','não','nao','mais','muito'];

    public function __construct()
    {
        if (!Mongo::available()) {
            throw new AppException('O chat está temporariamente indisponível.');
        }
    }

    // ------------------------------------------------------------
    // Site público
    // ------------------------------------------------------------

    /** Categorias com a quantidade de perguntas ativas (aggregation: $match + $group) */
    public function categoriasComTotal(): array
    {
        $rows = Mongo::aggregate(self::COLL, [
            ['$match' => ['ativo' => true]],
            ['$group' => ['_id' => '$categoria', 'total' => ['$sum' => 1]]],
        ]);
        $totais = array_column($rows, 'total', '_id');

        $out = [];
        foreach (self::CATEGORIAS as $id => $label) {
            if (!empty($totais[$id])) {
                $out[] = ['id' => $id, 'label' => $label, 'total' => (int)$totais[$id]];
            }
        }
        return $out;
    }

    public function porCategoria(string $categoria): array
    {
        if (!isset(self::CATEGORIAS[$categoria])) return [];
        $rows = Mongo::find(self::COLL,
            ['ativo' => true, 'categoria' => $categoria],
            ['sort' => ['ordem' => 1, 'pergunta' => 1], 'projection' => ['pergunta' => 1]]);
        return array_map(fn($r) => ['id' => (string)$r['_id'], 'pergunta' => $r['pergunta']], $rows);
    }

    /** Busca uma resposta e soma 1 visualização ($inc) */
    public function resposta(string $id): ?array
    {
        $oid = Mongo::id($id);
        if (!$oid) return null;
        $r = Mongo::findOne(self::COLL, ['_id' => $oid, 'ativo' => true]);
        if (!$r) return null;
        Mongo::updateOne(self::COLL, ['_id' => $oid], ['$inc' => ['visualizacoes' => 1]]);
        return [
            'id' => (string)$r['_id'],
            'pergunta' => $r['pergunta'],
            'resposta' => $r['resposta'],
            'categoria' => self::CATEGORIAS[$r['categoria']] ?? '',
        ];
    }

    /**
     * Busca o que a pessoa digitou.
     * 1º tenta o índice de TEXTO do MongoDB ($text, ordenado por relevância).
     * 2º se não achar nada (ex.: palavra pela metade), tenta por partes da palavra com $regex.
     */
    public function buscar(string $texto, int $limite = 5): array
    {
        $texto = trim(mb_substr($texto, 0, 200));
        if (mb_strlen($texto) < 2) return [];

        $resultados = [];
        try {
            $resultados = Mongo::find(self::COLL,
                ['$text' => ['$search' => $texto, '$language' => 'portuguese'], 'ativo' => true],
                [
                    'projection' => ['pergunta' => 1, 'score' => ['$meta' => 'textScore']],
                    'sort' => ['score' => ['$meta' => 'textScore']],
                    'limit' => $limite,
                ]);
        } catch (Throwable $e) {
            // Servidor sem suporte a $text (ou índice ausente): usa a busca alternativa
            error_log('[CMDPCD][Faq] $text indisponível: ' . $e->getMessage());
        }

        if (!$resultados) {
            $resultados = $this->buscarPorPalavras($texto, $limite);
        }

        return array_map(fn($r) => ['id' => (string)$r['_id'], 'pergunta' => $r['pergunta']], $resultados);
    }

    /** Busca alternativa: cada palavra relevante vira uma $regex em pergunta/palavras-chave */
    private function buscarPorPalavras(string $texto, int $limite): array
    {
        $palavras = $this->palavrasRelevantes($texto);
        if (!$palavras) return [];

        $ors = [];
        foreach ($palavras as $p) {
            // usa o radical (sem as 2 últimas letras) para achar "cadastrar" ao digitar "cadastro"
            $raiz = mb_strlen($p) > 5 ? mb_substr($p, 0, -2) : $p;
            $re = $this->regexSemAcento($raiz);
            $ors[] = ['pergunta' => $re];
            $ors[] = ['palavras_chave' => $re];
        }
        $docs = Mongo::find(self::COLL, ['ativo' => true, '$or' => $ors],
            ['projection' => ['pergunta' => 1, 'palavras_chave' => 1], 'limit' => 50]);

        // Pontua cada pergunta. Palavras raras valem mais que palavras comuns
        // (ex.: "reunião" vale mais que "conselho", que aparece em quase tudo).
        // Achar na própria pergunta vale o dobro de achar só nas palavras-chave.
        $alvos = [];
        $df = array_fill_keys($palavras, 0);
        foreach ($docs as $k => $d) {
            $alvos[$k] = [
                'p' => $this->normalizar($d['pergunta']),
                'k' => $this->normalizar(implode(' ', $d['palavras_chave'] ?? [])),
            ];
            foreach ($palavras as $p) {
                $raiz = mb_strlen($p) > 5 ? mb_substr($p, 0, -2) : $p;
                if (mb_strpos($alvos[$k]['p'] . ' ' . $alvos[$k]['k'], $raiz) !== false) $df[$p]++;
            }
        }
        foreach ($docs as $k => &$d) {
            $d['_score'] = 0;
            foreach ($palavras as $p) {
                $raiz = mb_strlen($p) > 5 ? mb_substr($p, 0, -2) : $p;
                $peso = 1 / max(1, $df[$p]);
                if (mb_strpos($alvos[$k]['p'], $raiz) !== false) $d['_score'] += 2 * $peso;
                elseif (mb_strpos($alvos[$k]['k'], $raiz) !== false) $d['_score'] += $peso;
            }
        }
        unset($d);
        usort($docs, fn($a, $b) => $b['_score'] <=> $a['_score']);
        // Só mostra resultados com pelo menos metade da relevância do melhor (evita sugestões sem relação)
        $melhor = $docs[0]['_score'] ?? 0;
        $docs = array_filter($docs, fn($d) => $d['_score'] > 0 && $d['_score'] >= $melhor * 0.5);
        return array_slice(array_values($docs), 0, $limite);
    }

    public function palavrasRelevantes(string $texto): array
    {
        $partes = preg_split('/[^\p{L}\p{N}]+/u', $this->normalizar($texto), -1, PREG_SPLIT_NO_EMPTY);
        $stop = array_map([$this, 'normalizar'], self::STOPWORDS);
        return array_values(array_unique(array_filter($partes, fn($p) => mb_strlen($p) >= 3 && !in_array($p, $stop, true))));
    }

    /** Regex que encontra a palavra com ou sem acento: "beneficio" acha "benefício" */
    private function regexSemAcento(string $t): \MongoDB\BSON\Regex
    {
        $map = ['a' => '[aáàâãä]', 'e' => '[eéèêë]', 'i' => '[iíìîï]', 'o' => '[oóòôõö]', 'u' => '[uúùûü]', 'c' => '[cç]'];
        $out = '';
        foreach (mb_str_split($t) as $ch) {
            $out .= $map[$ch] ?? preg_quote($ch, '/');
        }
        return new \MongoDB\BSON\Regex($out, 'i');
    }

    /** minúsculas e sem acentos */
    public function normalizar(string $t): string
    {
        $t = mb_strtolower($t, 'UTF-8');
        $map = ['á'=>'a','à'=>'a','â'=>'a','ã'=>'a','ä'=>'a','é'=>'e','ê'=>'e','è'=>'e','í'=>'i','î'=>'i','ó'=>'o','ô'=>'o','õ'=>'o','ö'=>'o','ú'=>'u','ü'=>'u','ç'=>'c'];
        return strtr($t, $map);
    }

    /** "Isso ajudou?" - soma 1 em util.sim ou util.nao ($inc em campo de subdocumento) */
    public function avaliar(string $id, bool $util): bool
    {
        $oid = Mongo::id($id);
        if (!$oid) return false;
        return Mongo::updateOne(self::COLL, ['_id' => $oid, 'ativo' => true],
            ['$inc' => [$util ? 'util.sim' : 'util.nao' => 1]]) > 0;
    }

    // ------------------------------------------------------------
    // Painel administrativo
    // ------------------------------------------------------------
    public function todas(string $categoria = '', string $busca = ''): array
    {
        $filtro = [];
        if (isset(self::CATEGORIAS[$categoria])) {
            $filtro['categoria'] = $categoria;
        }
        if ($busca !== '') {
            $filtro['$or'] = [
                ['pergunta' => Mongo::regex($busca)],
                ['resposta' => Mongo::regex($busca)],
                ['palavras_chave' => Mongo::regex($busca)],
            ];
        }
        return Mongo::find(self::COLL, $filtro, ['sort' => ['categoria' => 1, 'ordem' => 1]]);
    }

    public function find(string $id): ?array
    {
        $oid = Mongo::id($id);
        return $oid ? Mongo::findOne(self::COLL, ['_id' => $oid]) : null;
    }

    /** Valida e monta o documento a partir do formulário */
    public function dadosDoFormulario(array $post): array
    {
        $pergunta = limit_str($post['pergunta'] ?? '', 300);
        $resposta = limit_str($post['resposta'] ?? '', 3000);
        $categoria = (string)($post['categoria'] ?? '');

        if ($pergunta === '' || $resposta === '' || !isset(self::CATEGORIAS[$categoria])) {
            throw new AppException('Preencha a pergunta, a resposta e a categoria.');
        }

        // "bpc, loas, inss" -> ["bpc", "loas", "inss"]
        $palavras = array_filter(array_map(
            fn($p) => mb_strtolower(limit_str($p, 50)),
            explode(',', (string)($post['palavras_chave'] ?? ''))
        ));

        return [
            'categoria' => $categoria,
            'pergunta' => $pergunta,
            'resposta' => str_replace("\r\n", "\n", $resposta),
            'palavras_chave' => array_values(array_unique(array_slice($palavras, 0, 20))),
            'ordem' => max(0, min(9999, (int)($post['ordem'] ?? 0))),
            'ativo' => !empty($post['ativo']),
        ];
    }

    public function create(array $dados): string
    {
        $now = Mongo::now();
        $dados += ['util' => ['sim' => 0, 'nao' => 0], 'visualizacoes' => 0, 'criado_em' => $now, 'atualizado_em' => $now];
        return Mongo::insertOne(self::COLL, $dados);
    }

    /** $set altera só os campos editados, mantendo contadores (util, visualizacoes) */
    public function update(string $id, array $dados): bool
    {
        $oid = Mongo::id($id);
        if (!$oid) return false;
        $dados['atualizado_em'] = Mongo::now();
        Mongo::updateOne(self::COLL, ['_id' => $oid], ['$set' => $dados]);
        return true;
    }

    public function alternarAtivo(string $id): ?bool
    {
        $faq = $this->find($id);
        if (!$faq) return null;
        $novo = empty($faq['ativo']);
        Mongo::updateOne(self::COLL, ['_id' => $faq['_id']], ['$set' => ['ativo' => $novo, 'atualizado_em' => Mongo::now()]]);
        return $novo;
    }

    public function delete(string $id): bool
    {
        $oid = Mongo::id($id);
        return $oid ? Mongo::deleteOne(self::COLL, ['_id' => $oid]) > 0 : false;
    }

    /** Números gerais para o topo da tela (aggregation com $group e $sum) */
    public function estatisticas(): array
    {
        $vazio = ['total' => 0, 'ativas' => 0, 'visualizacoes' => 0, 'util_sim' => 0, 'util_nao' => 0];
        try {
            $r = Mongo::aggregate(self::COLL, [
                ['$group' => [
                    '_id' => null,
                    'total' => ['$sum' => 1],
                    'ativas' => ['$sum' => ['$cond' => ['$ativo', 1, 0]]],
                    'visualizacoes' => ['$sum' => '$visualizacoes'],
                    'util_sim' => ['$sum' => '$util.sim'],
                    'util_nao' => ['$sum' => '$util.nao'],
                ]],
            ]);
            return $r[0] ?? $vazio;
        } catch (Throwable $e) {
            // Alternativa para servidores compatíveis com MongoDB que não têm todos os operadores
            $out = $vazio;
            foreach (Mongo::find(self::COLL, [], ['projection' => ['ativo' => 1, 'visualizacoes' => 1, 'util' => 1]]) as $d) {
                $out['total']++;
                $out['ativas'] += !empty($d['ativo']) ? 1 : 0;
                $out['visualizacoes'] += (int)($d['visualizacoes'] ?? 0);
                $out['util_sim'] += (int)($d['util']['sim'] ?? 0);
                $out['util_nao'] += (int)($d['util']['nao'] ?? 0);
            }
            return $out;
        }
    }
}
