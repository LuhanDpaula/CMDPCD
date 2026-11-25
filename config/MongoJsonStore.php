<?php
/**
 * Armazenamento documental local (JSON) usado quando não existe um mongod local.
 * Mantém coleções e documentos sem esquema fixo, com operações necessárias ao chatbot.
 * O MongoDB real continua sendo priorizado sempre que estiver disponível.
 */
class MongoJsonStore
{
    private string $directory;

    public function __construct(string $directory)
    {
        $this->directory = rtrim($directory, DIRECTORY_SEPARATOR);
        if (!is_dir($this->directory) && !@mkdir($this->directory, 0775, true) && !is_dir($this->directory)) {
            throw new RuntimeException('Não foi possível criar o diretório do banco documental local.');
        }
    }

    private function file(string $collection): string
    {
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $collection)) {
            throw new InvalidArgumentException('Nome de coleção inválido.');
        }
        return $this->directory . DIRECTORY_SEPARATOR . $collection . '.json';
    }

    private function decodeValue($value)
    {
        if (!is_array($value)) return $value;
        if (($value['__cmdpcd_type'] ?? null) === 'ObjectId') return new MongoDB\BSON\ObjectId((string)$value['value']);
        if (($value['__cmdpcd_type'] ?? null) === 'UTCDateTime') return new MongoDB\BSON\UTCDateTime((int)$value['value']);
        foreach ($value as $k => $v) $value[$k] = $this->decodeValue($v);
        return $value;
    }

    private function encodeValue($value)
    {
        if ($value instanceof MongoDB\BSON\ObjectId) return ['__cmdpcd_type' => 'ObjectId', 'value' => (string)$value];
        if ($value instanceof MongoDB\BSON\UTCDateTime) return ['__cmdpcd_type' => 'UTCDateTime', 'value' => (string)$value];
        if ($value instanceof DateTimeInterface) return ['__cmdpcd_type' => 'UTCDateTime', 'value' => (string)($value->getTimestamp() * 1000)];
        if (is_object($value)) $value = (array)$value;
        if (is_array($value)) foreach ($value as $k => $v) $value[$k] = $this->encodeValue($v);
        return $value;
    }

    private function read(string $collection): array
    {
        $file = $this->file($collection);
        if (!is_file($file)) return [];
        $raw = @file_get_contents($file);
        if ($raw === false || trim($raw) === '') return [];
        $data = json_decode($raw, true);
        if (!is_array($data)) throw new RuntimeException('Arquivo documental inválido: ' . basename($file));
        return array_map(fn($d) => $this->decodeValue($d), $data);
    }

    private function mutate(string $collection, callable $callback)
    {
        $file = $this->file($collection);
        $lock = fopen($file . '.lock', 'c+');
        if (!$lock) throw new RuntimeException('Não foi possível bloquear a coleção documental.');
        try {
            if (!flock($lock, LOCK_EX)) throw new RuntimeException('Falha ao bloquear coleção documental.');
            $docs = $this->read($collection);
            $result = $callback($docs);
            $encoded = json_encode($this->encodeValue($docs), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            if ($encoded === false) throw new RuntimeException('Falha ao serializar coleção documental.');
            $tmp = $file . '.tmp';
            if (file_put_contents($tmp, $encoded, LOCK_EX) === false || !@rename($tmp, $file)) {
                @unlink($tmp);
                throw new RuntimeException('Falha ao persistir coleção documental.');
            }
            flock($lock, LOCK_UN);
            return $result;
        } finally {
            fclose($lock);
        }
    }

    private function getPath(array $doc, string $path)
    {
        $current = $doc;
        foreach (explode('.', $path) as $part) {
            if (is_array($current) && array_key_exists($part, $current)) $current = $current[$part];
            else return null;
        }
        return $current;
    }

    private function same($a, $b): bool
    {
        if ($a instanceof MongoDB\BSON\ObjectId) $a = (string)$a;
        if ($b instanceof MongoDB\BSON\ObjectId) $b = (string)$b;
        if ($a instanceof MongoDB\BSON\UTCDateTime) $a = (string)$a;
        if ($b instanceof MongoDB\BSON\UTCDateTime) $b = (string)$b;
        if (is_array($a) || is_array($b)) return $a == $b;
        return $a === $b || (is_numeric($a) && is_numeric($b) && (string)$a === (string)$b);
    }

    private function compare($a, $b): int
    {
        if ($a instanceof MongoDB\BSON\ObjectId) $a = (string)$a;
        if ($b instanceof MongoDB\BSON\ObjectId) $b = (string)$b;
        if ($a instanceof MongoDB\BSON\UTCDateTime) $a = (string)$a;
        if ($b instanceof MongoDB\BSON\UTCDateTime) $b = (string)$b;
        if ($a instanceof DateTimeInterface) $a = $a->getTimestamp();
        if ($b instanceof DateTimeInterface) $b = $b->getTimestamp();
        return is_numeric($a) && is_numeric($b) ? ((float)$a <=> (float)$b) : strcasecmp((string)$a, (string)$b);
    }

    private function fieldMatches($actual, $expected): bool
    {
        if ($expected instanceof MongoDB\BSON\Regex) {
            $pattern = '/' . str_replace('/', '\\/', $expected->getPattern()) . '/' . (strpos($expected->getFlags(), 'i') !== false ? 'i' : '');
            return @preg_match($pattern, (string)$actual) === 1;
        }
        if (is_array($expected) && $expected && str_starts_with((string)array_key_first($expected), '$')) {
            foreach ($expected as $op => $value) {
                switch ($op) {
                    case '$exists': if (((null !== $actual) !== (bool)$value)) return false; break;
                    case '$eq': if (!$this->same($actual, $value)) return false; break;
                    case '$ne': if ($this->same($actual, $value)) return false; break;
                    case '$gt': if ($this->compare($actual, $value) <= 0) return false; break;
                    case '$gte': if ($this->compare($actual, $value) < 0) return false; break;
                    case '$lt': if ($this->compare($actual, $value) >= 0) return false; break;
                    case '$lte': if ($this->compare($actual, $value) > 0) return false; break;
                    case '$in': $ok = false; foreach ((array)$value as $v) if ($this->same($actual, $v)) {$ok = true; break;} if (!$ok) return false; break;
                    case '$nin': foreach ((array)$value as $v) if ($this->same($actual, $v)) return false; break;
                    case '$regex':
                        $flags = (($expected['$options'] ?? '') === 'i') ? 'i' : '';
                        if (@preg_match('/' . str_replace('/', '\\/', (string)$value) . '/' . $flags, (string)$actual) !== 1) return false;
                        break;
                    case '$options': break;
                    case '$not': if ($this->fieldMatches($actual, $value)) return false; break;
                }
            }
            return true;
        }
        if (is_array($actual) && !is_array($expected)) {
            foreach ($actual as $item) if ($this->same($item, $expected)) return true;
            return false;
        }
        return $this->same($actual, $expected);
    }

    private function matches(array $doc, array $filter): bool
    {
        foreach ($filter as $field => $expected) {
            if ($field === '$or') { $ok = false; foreach ((array)$expected as $sub) if ($this->matches($doc, (array)$sub)) {$ok = true; break;} if (!$ok) return false; continue; }
            if ($field === '$and') { foreach ((array)$expected as $sub) if (!$this->matches($doc, (array)$sub)) return false; continue; }
            if ($field === '$nor') { foreach ((array)$expected as $sub) if ($this->matches($doc, (array)$sub)) return false; continue; }
            if ($field === '$text') {
                $search = (string)($expected['$search'] ?? '');
                if ($search !== '' && stripos(json_encode($doc, JSON_UNESCAPED_UNICODE), $search) === false) return false;
                continue;
            }
            $actual = $this->getPath($doc, (string)$field);
            if (!$this->fieldMatches($actual, $expected)) return false;
        }
        return true;
    }

    private function setPath(array &$doc, string $path, $value): void
    {
        $parts = explode('.', $path); $ref =& $doc;
        foreach ($parts as $part) { if (!isset($ref[$part]) || !is_array($ref[$part])) $ref[$part] = []; $ref =& $ref[$part]; }
        $ref = $value;
    }

    private function unsetPath(array &$doc, string $path): void
    {
        $parts = explode('.', $path); $last = array_pop($parts); $ref =& $doc;
        foreach ($parts as $part) { if (!isset($ref[$part]) || !is_array($ref[$part])) return; $ref =& $ref[$part]; }
        unset($ref[$last]);
    }

    private function applyUpdate(array &$doc, array $update): void
    {
        $operators = array_filter(array_keys($update), fn($k) => str_starts_with((string)$k, '$'));
        if (!$operators) { $id = $doc['_id'] ?? null; $doc = $update; if ($id !== null) $doc['_id'] = $id; return; }
        foreach ($update as $op => $values) {
            foreach ((array)$values as $path => $value) {
                $current = $this->getPath($doc, (string)$path);
                switch ($op) {
                    case '$set': $this->setPath($doc, (string)$path, $value); break;
                    case '$inc': $this->setPath($doc, (string)$path, ($current ?? 0) + $value); break;
                    case '$unset': $this->unsetPath($doc, (string)$path); break;
                    case '$push': $arr = is_array($current) ? $current : []; $arr[] = $value; $this->setPath($doc, (string)$path, $arr); break;
                    case '$addToSet': $arr = is_array($current) ? $current : []; $found = false; foreach ($arr as $v) if ($this->same($v, $value)) {$found=true; break;} if (!$found) $arr[] = $value; $this->setPath($doc, (string)$path, $arr); break;
                    case '$pull': $arr = is_array($current) ? $current : []; $arr = array_values(array_filter($arr, fn($v) => !$this->same($v, $value))); $this->setPath($doc, (string)$path, $arr); break;
                }
            }
        }
    }

    public function find(string $collection, array $filter = [], array $options = []): array
    {
        $rows = array_values(array_filter($this->read($collection), fn($d) => $this->matches($d, $filter)));
        if (!empty($options['sort'])) {
            $sort = $options['sort'];
            usort($rows, function($a, $b) use ($sort) { foreach ($sort as $field => $direction) { $cmp = $this->compare($this->getPath($a, $field), $this->getPath($b, $field)); if ($cmp !== 0) return $cmp * ((int)$direction < 0 ? -1 : 1); } return 0; });
        }
        if (!empty($options['skip'])) $rows = array_slice($rows, (int)$options['skip']);
        if (!empty($options['limit'])) $rows = array_slice($rows, 0, (int)$options['limit']);
        if (!empty($options['projection'])) $rows = array_map(fn($d) => $this->project($d, $options['projection']), $rows);
        return $rows;
    }

    private function project(array $doc, array $projection): array
    {
        $include = array_filter($projection, fn($v) => $v === 1 || $v === true);
        if ($include) { $out = []; foreach ($include as $field => $_) { $v = $this->getPath($doc, (string)$field); if ($v !== null) $this->setPath($out, (string)$field, $v); } if (($projection['_id'] ?? 1) !== 0 && isset($doc['_id'])) $out['_id'] = $doc['_id']; return $out; }
        $out = $doc; foreach ($projection as $field => $flag) if ($flag === 0 || $flag === false) $this->unsetPath($out, (string)$field); return $out;
    }

    public function insertOne(string $collection, array $doc): string
    {
        if (!isset($doc['_id'])) $doc['_id'] = new MongoDB\BSON\ObjectId();
        $id = (string)$doc['_id'];
        $this->mutate($collection, function (&$docs) use ($doc) { foreach ($docs as $d) if ((string)($d['_id'] ?? '') === (string)$doc['_id']) throw new RuntimeException('Documento duplicado.'); $docs[] = $doc; });
        return $id;
    }

    public function insertMany(string $collection, array $documents): int
    {
        if (!$documents) return 0;
        return $this->mutate($collection, function (&$docs) use ($documents) { $count = 0; foreach ($documents as $d) { if (!isset($d['_id'])) $d['_id'] = new MongoDB\BSON\ObjectId(); $exists = false; foreach ($docs as $old) if ((string)($old['_id'] ?? '') === (string)$d['_id']) {$exists = true; break;} if (!$exists) {$docs[] = $d; $count++;} } return $count; });
    }

    public function updateOne(string $collection, array $filter, array $update, array $options = []): int
    {
        return $this->mutate($collection, function (&$docs) use ($filter, $update, $options) {
            $changed = 0;
            foreach ($docs as &$doc) if ($this->matches($doc, $filter)) { $this->applyUpdate($doc, $update); $changed++; if (empty($options['multi'])) break; }
            unset($doc);
            if (!$changed && !empty($options['upsert'])) { $doc = []; foreach ($filter as $k => $v) if (!str_starts_with((string)$k, '$') && !(is_array($v) && str_starts_with((string)array_key_first($v), '$'))) $this->setPath($doc, (string)$k, $v); $this->applyUpdate($doc, $update); if (!isset($doc['_id'])) $doc['_id'] = new MongoDB\BSON\ObjectId(); $docs[] = $doc; $changed = 1; }
            return $changed;
        });
    }

    public function delete(string $collection, array $filter, bool $many = false): int
    {
        return $this->mutate($collection, function (&$docs) use ($filter, $many) { $kept = []; $deleted = 0; foreach ($docs as $d) { if ($this->matches($d, $filter) && ($many || !$deleted)) $deleted++; else $kept[] = $d; } $docs = $kept; return $deleted; });
    }

    /** Replica a expiração TTL dos índices MongoDB usados pelo chatbot. */
    public function purgeExpired(?int $now = null): int
    {
        $now ??= time();
        $rules = [
            'pedidos_ajuda' => ['encerrado_em', 31536000],
            'perguntas_sem_resposta' => ['criado_em', 7776000],
            'logs_auditoria' => ['data_hora', 63072000],
        ];
        $removed = 0;
        foreach ($rules as $collection => [$field, $ttl]) {
            if (!is_file($this->file($collection))) continue;
            $removed += $this->mutate($collection, function (&$docs) use ($field, $ttl, $now) {
                $kept = []; $count = 0;
                foreach ($docs as $doc) {
                    $date = $this->getPath($doc, $field);
                    if ($date instanceof MongoDB\BSON\UTCDateTime) $timestamp = $date->toDateTime()->getTimestamp();
                    elseif ($date instanceof DateTimeInterface) $timestamp = $date->getTimestamp();
                    elseif (is_numeric($date)) $timestamp = (int)$date;
                    elseif (is_string($date) && $date !== '') $timestamp = strtotime($date) ?: 0;
                    else $timestamp = 0;
                    if ($timestamp > 0 && ($timestamp + $ttl) <= $now) $count++;
                    else $kept[] = $doc;
                }
                $docs = $kept;
                return $count;
            });
        }
        return $removed;
    }

    private function value($expr, array $doc)
    {
        if (is_string($expr) && str_starts_with($expr, '$')) return $this->getPath($doc, substr($expr, 1));
        if (!is_array($expr)) return $expr;
        if (array_is_list($expr)) return array_map(fn($v) => $this->value($v, $doc), $expr);
        if (count($expr) === 1) {
            $op = array_key_first($expr); $arg = $expr[$op];
            switch ($op) {
                case '$toUpper': return mb_strtoupper((string)$this->value($arg, $doc), 'UTF-8');
                case '$toLower': return mb_strtolower((string)$this->value($arg, $doc), 'UTF-8');
                case '$eq': $v = array_map(fn($x) => $this->value($x, $doc), (array)$arg); return $this->same($v[0] ?? null, $v[1] ?? null);
                case '$ne': $v = array_map(fn($x) => $this->value($x, $doc), (array)$arg); return !$this->same($v[0] ?? null, $v[1] ?? null);
                case '$gt': case '$gte': case '$lt': case '$lte': $v = array_map(fn($x) => $this->value($x, $doc), (array)$arg); $c=$this->compare($v[0]??null,$v[1]??null); return $op==='$gt'?$c>0:($op==='$gte'?$c>=0:($op==='$lt'?$c<0:$c<=0));
                case '$cond': $v=(array)$arg; $test=$this->value($v[0]??null,$doc); return $this->value($test?$v[1]:($v[2]??null),$doc);
                case '$ifNull': $v=(array)$arg; $x=$this->value($v[0]??null,$doc); return $x===null? $this->value($v[1]??null,$doc):$x;
                case '$subtract': $v=array_map(fn($x)=>$this->value($x,$doc),(array)$arg); return $this->numeric($v[0]??0)-$this->numeric($v[1]??0);
                case '$divide': $v=array_map(fn($x)=>$this->value($x,$doc),(array)$arg); return ($v[1]??0) ? $this->numeric($v[0]??0)/$this->numeric($v[1]) : null;
                case '$multiply': $v=array_map(fn($x)=>$this->value($x,$doc),(array)$arg); return array_product(array_map([$this,'numeric'],$v));
                case '$add': $v=array_map(fn($x)=>$this->value($x,$doc),(array)$arg); return array_sum(array_map([$this,'numeric'],$v));
                case '$size': return count((array)$this->value($arg,$doc));
                case '$dateToString': $date=$this->value($arg['date']??null,$doc); if ($date instanceof MongoDB\BSON\UTCDateTime) $dt=$date->toDateTime(); elseif ($date instanceof DateTimeInterface) $dt=DateTime::createFromInterface($date); else $dt=new DateTime('@'.(int)($date??0)); if (!empty($arg['timezone'])) $dt->setTimezone(new DateTimeZone($arg['timezone'])); return $dt->format(str_replace(['%Y','%m','%d','%H','%M','%S'],['Y','m','d','H','i','s'],$arg['format']??'%Y-%m-%d'));
                case '$literal': return $arg;
            }
        }
        $out=[]; foreach ($expr as $k=>$v) $out[$k]=$this->value($v,$doc); return $out;
    }

    private function numeric($value): float
    {
        if ($value instanceof MongoDB\BSON\UTCDateTime) return (float)(string)$value;
        if ($value instanceof DateTimeInterface) return (float)($value->getTimestamp()*1000);
        return is_numeric($value) ? (float)$value : 0.0;
    }

    public function aggregate(string $collection, array $pipeline): array
    {
        $rows = $this->read($collection);
        foreach ($pipeline as $stage) {
            if (isset($stage['$match'])) $rows = array_values(array_filter($rows, fn($d)=>$this->matches($d,(array)$stage['$match'])));
            elseif (isset($stage['$sort'])) { $sort=$stage['$sort']; usort($rows,function($a,$b)use($sort){foreach($sort as $f=>$d){$c=$this->compare($this->getPath($a,$f),$this->getPath($b,$f));if($c)return $c*((int)$d<0?-1:1);}return 0;}); }
            elseif (isset($stage['$limit'])) $rows=array_slice($rows,0,(int)$stage['$limit']);
            elseif (isset($stage['$skip'])) $rows=array_slice($rows,(int)$stage['$skip']);
            elseif (isset($stage['$count'])) $rows = $rows ? [[$stage['$count']=>count($rows)]] : [];
            elseif (isset($stage['$project'])) $rows=array_map(function($d)use($stage){$spec=$stage['$project'];$out=$this->project($d,$spec);foreach($spec as $k=>$v)if(!in_array($v,[0,1,false,true],true))$this->setPath($out,(string)$k,$this->value($v,$d));return $out;},$rows);
            elseif (isset($stage['$set']) || isset($stage['$addFields'])) { $spec=$stage['$set']??$stage['$addFields']; foreach($rows as &$d)foreach($spec as $k=>$v)$this->setPath($d,(string)$k,$v==='$$NOW'?Mongo::now():$this->value($v,$d)); unset($d); }
            elseif (isset($stage['$unset'])) { $fields=(array)$stage['$unset']; foreach($rows as &$d)foreach($fields as $f)$this->unsetPath($d,(string)$f); unset($d); }
            elseif (isset($stage['$unwind'])) { $path=ltrim(is_array($stage['$unwind'])?$stage['$unwind']['path']:$stage['$unwind'],'$');$new=[];foreach($rows as $d){$arr=$this->getPath($d,$path);if(is_array($arr))foreach($arr as $v){$copy=$d;$this->setPath($copy,$path,$v);$new[]=$copy;}elseif(!empty($stage['$unwind']['preserveNullAndEmptyArrays']))$new[]=$d;}$rows=$new; }
            elseif (isset($stage['$group'])) {
                $spec=$stage['$group'];$groups=[];foreach($rows as $d){$key=$this->value($spec['_id']??null,$d);$hash=hash('sha256',serialize($key));if(!isset($groups[$hash])){$groups[$hash]=['_id'=>$key,'__n'=>[]];foreach($spec as $field=>$acc)if($field!=='_id'&&isset($acc['$first']))$groups[$hash][$field]=$this->value($acc['$first'],$d);}
                    foreach($spec as $field=>$acc){if($field==='_id')continue;$op=array_key_first($acc);$expr=$acc[$op];$val=$this->value($expr,$d);switch($op){case '$sum':$groups[$hash][$field]=($groups[$hash][$field]??0)+$this->numeric($val);break;case '$avg':$groups[$hash][$field]=($groups[$hash][$field]??0)+$this->numeric($val);$groups[$hash]['__n'][$field]=($groups[$hash]['__n'][$field]??0)+1;break;case '$first':if(!array_key_exists($field,$groups[$hash]))$groups[$hash][$field]=$val;break;case '$last':$groups[$hash][$field]=$val;break;case '$max':if(!array_key_exists($field,$groups[$hash])||$this->compare($val,$groups[$hash][$field])>0)$groups[$hash][$field]=$val;break;case '$min':if(!array_key_exists($field,$groups[$hash])||$this->compare($val,$groups[$hash][$field])<0)$groups[$hash][$field]=$val;break;case '$push':$groups[$hash][$field][]=$val;break;case '$addToSet':$groups[$hash][$field]=$groups[$hash][$field]??[];$found=false;foreach($groups[$hash][$field]as$old)if($this->same($old,$val))$found=true;if(!$found)$groups[$hash][$field][]=$val;break;}}
                }
                $rows=[];foreach($groups as $g){foreach($g['__n'] as $f=>$n)$g[$f]=$n?$g[$f]/$n:null;unset($g['__n']);$rows[]=$g;}
            }
            elseif (isset($stage['$merge'])) { $into=is_array($stage['$merge'])?($stage['$merge']['into']??''):$stage['$merge'];foreach($rows as $d){$id=$d['_id']??null;if($id!==null)$this->updateOne($into,['_id'=>$id],['$set'=>$d],['upsert'=>true]);}$rows=[]; }
        }
        return $rows;
    }
}
