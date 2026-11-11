<?php
/**
 * O que as pessoas digitaram no chat e não encontraram resposta.
 * Coleção MongoDB "perguntas_sem_resposta" (apagadas automaticamente após 90 dias - índice TTL).
 * Não guarda nenhum dado pessoal: só o texto digitado.
 *
 * { _id, texto: "carteirinha de autista", texto_normalizado: "carteirinha de autista", criado_em: ISODate }
 */
class PerguntaSemResposta
{
    const COLL = 'perguntas_sem_resposta';

    public function registrar(string $texto): void
    {
        $texto = limit_str($texto, 200);
        if (mb_strlen($texto) < 3) return;
        try {
            Mongo::insertOne(self::COLL, [
                'texto' => $texto,
                'texto_normalizado' => $this->normalizar($texto),
                'criado_em' => Mongo::now(),
            ]);
        } catch (Throwable $e) {
            error_log('[CMDPCD][SemResposta] ' . $e->getMessage());
        }
    }

    /** Agrupa textos iguais e mostra os mais repetidos primeiro (aggregation $group + $sort) */
    public function agrupadas(int $limite = 100): array
    {
        try {
            return Mongo::aggregate(self::COLL, [
                ['$group' => [
                    '_id' => '$texto_normalizado',
                    'exemplo' => ['$first' => '$texto'],
                    'vezes' => ['$sum' => 1],
                    'ultima' => ['$max' => '$criado_em'],
                ]],
                ['$sort' => ['vezes' => -1, 'ultima' => -1]],
                ['$limit' => $limite],
            ]);
        } catch (Throwable $e) {
            // Alternativa em PHP (servidores compatíveis sem o acumulador $first)
            $grupos = [];
            foreach (Mongo::find(self::COLL, [], ['sort' => ['criado_em' => 1]]) as $d) {
                $k = $d['texto_normalizado'];
                if (!isset($grupos[$k])) $grupos[$k] = ['_id' => $k, 'exemplo' => $d['texto'], 'vezes' => 0, 'ultima' => $d['criado_em']];
                $grupos[$k]['vezes']++;
                $grupos[$k]['ultima'] = $d['criado_em'];
            }
            usort($grupos, fn($a, $b) => [$b['vezes'], (string)$b['ultima']] <=> [$a['vezes'], (string)$a['ultima']]);
            return array_slice($grupos, 0, $limite);
        }
    }

    public function total(): int
    {
        return Mongo::count(self::COLL);
    }

    /** Remove todas as ocorrências de um texto (depois que virou pergunta frequente ou foi descartado) */
    public function descartar(string $textoNormalizado): int
    {
        return Mongo::deleteMany(self::COLL, ['texto_normalizado' => (string)$textoNormalizado]);
    }

    private function normalizar(string $t): string
    {
        $t = mb_strtolower(trim(preg_replace('/\s+/u', ' ', $t)), 'UTF-8');
        $t = strtr($t, ['á'=>'a','à'=>'a','â'=>'a','ã'=>'a','é'=>'e','ê'=>'e','í'=>'i','ó'=>'o','ô'=>'o','õ'=>'o','ú'=>'u','ü'=>'u','ç'=>'c']);
        return rtrim($t, ' ?!.');
    }
}
