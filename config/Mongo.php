<?php
/**
 * ============================================================
 *  CONEXÃO COM O MONGODB - CMDPCD Jahu
 * ============================================================
 *  Arquitetura do projeto (persistência poliglota):
 *   - MySQL   -> núcleo do sistema (PCDs, usuários, notícias...)
 *   - MongoDB -> chat de perguntas frequentes, pedidos de ajuda,
 *                perguntas sem resposta, resumos mensais e logs de auditoria.
 *
 *  Suporta tanto a extensão nativa "mongodb" do PHP quanto conexão
 *  direta via protocolo nativo do MongoDB (Wire Protocol OP_MSG + BSON)
 *  sobre sockets TCP do PHP — funcionando automaticamente no XAMPP
 *  mesmo sem precisar instalar DLLs extras no php.ini.
 * ============================================================
 */

namespace MongoDB\BSON {
    if (!class_exists('MongoDB\BSON\ObjectId', false)) {
        class ObjectId implements \JsonSerializable
        {
            private string $oid;
            private static int $counter = 0;

            public function __construct(?string $id = null)
            {
                if ($id !== null) {
                    if (!preg_match('/^[a-f0-9]{24}$/i', $id)) {
                        throw new \InvalidArgumentException('ObjectId inválido.');
                    }
                    $this->oid = strtolower($id);
                } else {
                    if (self::$counter === 0) {
                        self::$counter = random_int(1, 0xFFFFFF);
                    }
                    $ts = pack('N', time());
                    $rand = random_bytes(5);
                    $cnt = substr(pack('N', ++self::$counter & 0xFFFFFF), 1, 3);
                    $this->oid = bin2hex($ts . $rand . $cnt);
                }
            }

            public function __toString(): string
            {
                return $this->oid;
            }

            #[\ReturnTypeWillChange]
            public function jsonSerialize()
            {
                return $this->oid;
            }
        }
    }

    if (!class_exists('MongoDB\BSON\UTCDateTime', false)) {
        class UTCDateTime implements \JsonSerializable
        {
            private int $ms;

            public function __construct($milliseconds = null)
            {
                if ($milliseconds === null) {
                    $this->ms = (int)round(microtime(true) * 1000);
                } elseif ($milliseconds instanceof \DateTimeInterface) {
                    $this->ms = (int)($milliseconds->getTimestamp() * 1000);
                } else {
                    $this->ms = (int)(string)$milliseconds;
                }
            }

            public function toDateTime(): \DateTime
            {
                $sec = intdiv($this->ms, 1000);
                $usec = abs($this->ms % 1000) * 1000;
                $dt = \DateTime::createFromFormat('U u', sprintf('%d %06d', $sec, $usec), new \DateTimeZone('UTC'));
                return $dt ?: new \DateTime('@' . $sec, new \DateTimeZone('UTC'));
            }

            public function __toString(): string
            {
                return (string)$this->ms;
            }

            #[\ReturnTypeWillChange]
            public function jsonSerialize()
            {
                return $this->toDateTime()->format('c');
            }
        }
    }

    if (!class_exists('MongoDB\BSON\Regex', false)) {
        class Regex
        {
            private string $pattern;
            private string $flags;

            public function __construct(string $pattern, string $flags = '')
            {
                $this->pattern = $pattern;
                $this->flags = $flags;
            }

            public function getPattern(): string
            {
                return $this->pattern;
            }

            public function getFlags(): string
            {
                return $this->flags;
            }

            public function __toString(): string
            {
                return '/' . $this->pattern . '/' . $this->flags;
            }
        }
    }
}

namespace MongoDB\Driver {
    if (!class_exists('MongoDB\Driver\Command', false)) {
        class Command
        {
            public array $cmd;
            public function __construct(array $cmd)
            {
                $this->cmd = $cmd;
            }
        }
    }
}

namespace {

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Driver\Command;

/**
 * Cliente nativo MongoDB Wire Protocol (OP_MSG / opcode 2013 + BSON) em PHP puro.
 * Usado automaticamente quando a extensão C "mongodb" não está habilitada no php.ini.
 */
class MongoNativeWireClient
{
    private string $host = '127.0.0.1';
    private int $port = 27017;
    private float $timeout = 2.0;
    /** @var resource|null */
    private $socket = null;
    private static int $requestId = 1;

    public function __construct(string $uri, array $options = [])
    {
        $parts = parse_url($uri);
        if (!empty($parts['host'])) {
            $this->host = $parts['host'];
        }
        if (!empty($parts['port'])) {
            $this->port = (int)$parts['port'];
        }
        if (!empty($options['connectTimeoutMS'])) {
            $this->timeout = max(0.5, ((int)$options['connectTimeoutMS']) / 1000);
        }
    }

    private function connect()
    {
        if (is_resource($this->socket) && !feof($this->socket)) {
            return $this->socket;
        }
        $errno = 0;
        $errstr = '';
        $sock = @stream_socket_client(
            "tcp://{$this->host}:{$this->port}",
            $errno,
            $errstr,
            $this->timeout,
            STREAM_CLIENT_CONNECT
        );
        if (!$sock) {
            throw new Exception("Não foi possível conectar ao MongoDB em {$this->host}:{$this->port} ($errstr [$errno])");
        }
        stream_set_timeout($sock, 5, 0);
        $this->socket = $sock;
        return $this->socket;
    }

    /**
     * Compatível com MongoDB\Driver\Manager::executeCommand()
     */
    public function executeCommand(string $db, $command): MongoNativeCursor
    {
        $cmdArray = ($command instanceof Command) ? $command->cmd : (array)$command;
        $res = $this->runCommand($db, $cmdArray);
        if (isset($res['cursor']['firstBatch']) && is_array($res['cursor']['firstBatch'])) {
            return new MongoNativeCursor($res['cursor']['firstBatch']);
        }
        return new MongoNativeCursor([$res]);
    }

    /**
     * Envia um comando via OP_MSG (opcode 2013) e retorna o documento BSON decodificado.
     */
    public function runCommand(string $db, array $cmd): array
    {
        $sock = $this->connect();
        $cmd['$db'] = $db;

        $bson = self::bsonEncodeDocument($cmd);
        // OP_MSG: flagBits (int32 = 0) + sectionKind (byte = 0) + BSON document
        $body = pack('VC', 0, 0) . $bson;
        $reqId = ++self::$requestId;
        $header = pack('VVVV', 16 + strlen($body), $reqId, 0, 2013);

        $msg = $header . $body;
        $written = 0;
        $total = strlen($msg);
        while ($written < $total) {
            $n = @fwrite($sock, substr($msg, $written));
            if ($n === false || $n === 0) {
                @fclose($sock);
                $this->socket = null;
                throw new Exception('Falha ao enviar comando ao MongoDB.');
            }
            $written += $n;
        }

        $hdrBuf = $this->readBytes($sock, 16);
        $hdr = unpack('Vlen/VreqId/VrespTo/VopCode', $hdrBuf);
        $bodyLen = $hdr['len'] - 16;
        if ($bodyLen <= 0 || $bodyLen > 67108864) {
            throw new Exception('Resposta inválida do servidor MongoDB.');
        }

        $respBody = $this->readBytes($sock, $bodyLen);
        if ((int)$hdr['opCode'] !== 2013) {
            throw new Exception('Opcode inesperado do MongoDB: ' . $hdr['opCode']);
        }

        // Pula flagBits (4 bytes)
        $pos = 4;
        $doc = [];
        while ($pos < $bodyLen) {
            $kind = ord($respBody[$pos]);
            $pos++;
            if ($kind === 0) {
                $doc = self::bsonDecodeDocument($respBody, $pos, false);
                break;
            } elseif ($kind === 1) {
                $secSize = unpack('V', substr($respBody, $pos, 4))[1];
                $pos += $secSize;
            } else {
                break;
            }
        }

        if (isset($doc['ok']) && (float)$doc['ok'] == 0.0) {
            $errmsg = $doc['errmsg'] ?? 'Erro no comando MongoDB';
            $code = (int)($doc['code'] ?? 0);
            throw new Exception($errmsg, $code);
        }

        return $doc;
    }

    private function readBytes($sock, int $len): string
    {
        $buf = '';
        while (strlen($buf) < $len) {
            $chunk = @fread($sock, $len - strlen($buf));
            if ($chunk === false || $chunk === '') {
                @fclose($sock);
                $this->socket = null;
                throw new Exception('Conexão encerrada inesperadamente pelo MongoDB.');
            }
            $buf .= $chunk;
        }
        return $buf;
    }

    // ------------------------------------------------------------
    // Codificador e Decodificador BSON (Especificação 1.1)
    // ------------------------------------------------------------
    private static function isSequentialList(array $arr): bool
    {
        if ($arr === []) return true;
        $i = 0;
        foreach ($arr as $k => $_) {
            if ($k !== $i++) return false;
        }
        return true;
    }

    public static function bsonEncodeDocument($doc): string
    {
        if ($doc instanceof stdClass) {
            $arr = get_object_vars($doc);
        } elseif (is_array($doc)) {
            $arr = $doc;
        } else {
            $arr = (array)$doc;
        }

        $elements = '';
        foreach ($arr as $key => $val) {
            $k = (string)$key;
            $elements .= self::bsonEncodeElement($k, $val);
        }

        return pack('V', strlen($elements) + 5) . $elements . "\x00";
    }

    private static function bsonEncodeElement(string $key, $val): string
    {
        $ckey = $key . "\x00";

        if ($val instanceof ObjectId) {
            return "\x07" . $ckey . hex2bin((string)$val);
        }
        if ($val instanceof UTCDateTime) {
            return "\x09" . $ckey . pack('P', (int)(string)$val);
        }
        if ($val instanceof Regex) {
            return "\x0B" . $ckey . $val->getPattern() . "\x00" . $val->getFlags() . "\x00";
        }
        if ($val === null) {
            return "\x0A" . $ckey;
        }
        if (is_bool($val)) {
            return "\x08" . $ckey . ($val ? "\x01" : "\x00");
        }
        if (is_int($val)) {
            if ($val >= -2147483648 && $val <= 2147483647) {
                return "\x10" . $ckey . pack('V', $val & 0xFFFFFFFF);
            }
            return "\x12" . $ckey . pack('P', $val);
        }
        if (is_float($val)) {
            return "\x01" . $ckey . pack('e', $val);
        }
        if (is_string($val)) {
            return "\x02" . $ckey . pack('V', strlen($val) + 1) . $val . "\x00";
        }
        if ($val instanceof stdClass) {
            return "\x03" . $ckey . self::bsonEncodeDocument($val);
        }
        if (is_array($val)) {
            if (self::isSequentialList($val)) {
                $listObj = new stdClass();
                foreach ($val as $idx => $item) {
                    $listObj->{(string)$idx} = $item;
                }
                return "\x04" . $ckey . self::bsonEncodeDocument($listObj);
            }
            return "\x03" . $ckey . self::bsonEncodeDocument($val);
        }
        if (is_object($val)) {
            return "\x03" . $ckey . self::bsonEncodeDocument(get_object_vars($val));
        }

        $s = (string)$val;
        return "\x02" . $ckey . pack('V', strlen($s) + 1) . $s . "\x00";
    }

    public static function bsonDecodeDocument(string $buf, int &$offset, bool $asList = false): array
    {
        $docLen = unpack('V', substr($buf, $offset, 4))[1];
        $end = $offset + $docLen;
        $offset += 4;
        $out = [];

        while ($offset < $end - 1) {
            $type = ord($buf[$offset]);
            $offset++;
            if ($type === 0) {
                break;
            }

            $nullPos = strpos($buf, "\x00", $offset);
            $key = substr($buf, $offset, $nullPos - $offset);
            $offset = $nullPos + 1;

            switch ($type) {
                case 0x01: // double
                    $val = unpack('e', substr($buf, $offset, 8))[1];
                    $offset += 8;
                    break;
                case 0x02: // UTF-8 string
                case 0x0D: // JavaScript code
                case 0x0E: // Symbol
                    $len = unpack('V', substr($buf, $offset, 4))[1];
                    $offset += 4;
                    $val = substr($buf, $offset, $len - 1);
                    $offset += $len;
                    break;
                case 0x03: // Embedded document
                    $val = self::bsonDecodeDocument($buf, $offset, false);
                    break;
                case 0x04: // Array
                    $val = self::bsonDecodeDocument($buf, $offset, true);
                    break;
                case 0x05: // Binary data
                    $len = unpack('V', substr($buf, $offset, 4))[1];
                    $offset += 5; // 4 bytes length + 1 byte subtype
                    $val = substr($buf, $offset, $len);
                    $offset += $len;
                    break;
                case 0x06: // Undefined
                case 0x0A: // Null
                case 0xFF: // MinKey
                case 0x7F: // MaxKey
                    $val = null;
                    break;
                case 0x07: // ObjectId
                    $val = new ObjectId(bin2hex(substr($buf, $offset, 12)));
                    $offset += 12;
                    break;
                case 0x08: // Boolean
                    $val = ord($buf[$offset]) !== 0;
                    $offset += 1;
                    break;
                case 0x09: // UTCDateTime
                    $ms = (int)unpack('P', substr($buf, $offset, 8))[1];
                    $offset += 8;
                    $val = new UTCDateTime($ms);
                    break;
                case 0x0B: // Regex
                    $pEnd = strpos($buf, "\x00", $offset);
                    $pattern = substr($buf, $offset, $pEnd - $offset);
                    $offset = $pEnd + 1;
                    $fEnd = strpos($buf, "\x00", $offset);
                    $flags = substr($buf, $offset, $fEnd - $offset);
                    $offset = $fEnd + 1;
                    $val = new Regex($pattern, $flags);
                    break;
                case 0x10: // int32
                    $u = unpack('V', substr($buf, $offset, 4))[1];
                    if ($u >= 0x80000000) {
                        $u -= 0x100000000;
                    }
                    $val = (int)$u;
                    $offset += 4;
                    break;
                case 0x11: // Timestamp
                case 0x12: // int64
                    $val = (int)unpack('P', substr($buf, $offset, 8))[1];
                    $offset += 8;
                    break;
                case 0x13: // Decimal128
                    $val = bin2hex(substr($buf, $offset, 16));
                    $offset += 16;
                    break;
                default:
                    throw new Exception("Tipo BSON desconhecido: 0x" . dechex($type));
            }

            $out[$key] = $val;
        }

        $offset = $end;
        return $asList ? array_values($out) : $out;
    }
}

class MongoNativeCursor
{
    private array $docs;

    public function __construct(array $docs)
    {
        $this->docs = $docs;
    }

    public function setTypeMap(array $map): void {}

    public function toArray(): array
    {
        return $this->docs;
    }
}

class Mongo
{
    /** Versão da estrutura (índices). Aumente quando criar índices novos. */
    const SETUP_VERSION = 2;

    /** 1 ano em segundos: pedidos encerrados são apagados depois disso (TTL) */
    const TTL_PEDIDOS = 31536000;

    /** 2 anos em segundos: retenção de logs de auditoria (TTL) */
    const TTL_LOGS = 63072000;

    /** 90 dias: perguntas que o chat não soube responder */
    const TTL_SEM_RESPOSTA = 7776000;

    private static $manager = null;
    private static $dbName = null;
    private static $available = null;
    private static $setupChecked = false;

    private static $typeMap = ['root' => 'array', 'document' => 'array', 'array' => 'array'];

    // ------------------------------------------------------------
    // Conexão
    // ------------------------------------------------------------
    private static function config(): array
    {
        $local = is_file(__DIR__ . '/db.local.php') ? (require __DIR__ . '/db.local.php') : [];
        return [
            'uri' => $local['mongo_uri'] ?? (getenv('MONGO_URI') ?: 'mongodb://127.0.0.1:27017'),
            'db'  => $local['mongo_db'] ?? (getenv('MONGO_DB') ?: 'cmpcd_jau'),
        ];
    }

    public static function hasNativeExtension(): bool
    {
        return extension_loaded('mongodb') && class_exists('MongoDB\Driver\Manager', false);
    }

    public static function manager()
    {
        if (self::$manager === null) {
            $cfg = self::config();
            if (!preg_match('/^[A-Za-z0-9_]+$/', $cfg['db'])) {
                throw new Exception('Nome de banco MongoDB inválido.');
            }
            self::$dbName = $cfg['db'];

            $opts = [
                'serverSelectionTimeoutMS' => 2000,
                'connectTimeoutMS' => 2000,
            ];

            if (self::hasNativeExtension()) {
                $cls = 'MongoDB\Driver\Manager';
                self::$manager = new $cls($cfg['uri'], $opts);
            } else {
                self::$manager = new MongoNativeWireClient($cfg['uri'], $opts);
            }
        }
        return self::$manager;
    }

    public static function db(): string
    {
        self::manager();
        return self::$dbName;
    }

    /** O MongoDB está acessível? (resultado guardado durante a requisição) */
    public static function available(): bool
    {
        if (self::$available !== null) {
            return self::$available;
        }
        try {
            self::manager()->executeCommand('admin', new Command(['ping' => 1]));
            self::$available = true;
            if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['mongo_off_ate'])) {
                unset($_SESSION['mongo_off_ate']);
            }
            self::ensureSetup();
        } catch (Throwable $e) {
            error_log('[CMDPCD][Mongo] indisponível: ' . $e->getMessage());
            self::$available = false;
        }
        return self::$available;
    }

    // ------------------------------------------------------------
    // Operações básicas (CRUD)
    // ------------------------------------------------------------
    public static function find(string $coll, array $filter = [], array $options = []): array
    {
        if (self::hasNativeExtension()) {
            $queryCls = 'MongoDB\Driver\Query';
            $cursor = self::manager()->executeQuery(self::db() . '.' . $coll, new $queryCls($filter, $options));
            $cursor->setTypeMap(self::$typeMap);
            return $cursor->toArray();
        }

        $cmd = [
            'find' => $coll,
            'filter' => (object)$filter,
        ];
        if (!empty($options['sort'])) {
            $cmd['sort'] = (object)$options['sort'];
        }
        if (!empty($options['projection'])) {
            $cmd['projection'] = (object)$options['projection'];
        }
        if (isset($options['limit']) && (int)$options['limit'] > 0) {
            $cmd['limit'] = (int)$options['limit'];
            $cmd['batchSize'] = (int)$options['limit'];
        } else {
            $cmd['batchSize'] = 1000;
        }
        if (isset($options['skip'])) {
            $cmd['skip'] = (int)$options['skip'];
        }

        $res = self::manager()->runCommand(self::db(), $cmd);
        return $res['cursor']['firstBatch'] ?? [];
    }

    public static function findOne(string $coll, array $filter, array $options = []): ?array
    {
        $options['limit'] = 1;
        $rows = self::find($coll, $filter, $options);
        return $rows[0] ?? null;
    }

    public static function insertOne(string $coll, array $doc): string
    {
        if (self::hasNativeExtension()) {
            $bulkCls = 'MongoDB\Driver\BulkWrite';
            $wcCls = 'MongoDB\Driver\WriteConcern';
            $bulk = new $bulkCls();
            $id = $bulk->insert($doc);
            self::manager()->executeBulkWrite(self::db() . '.' . $coll, $bulk, ['writeConcern' => new $wcCls($wcCls::MAJORITY, 5000)]);
            return (string)($doc['_id'] ?? $id);
        }

        if (!isset($doc['_id'])) {
            $doc['_id'] = new ObjectId();
        }
        $res = self::manager()->runCommand(self::db(), [
            'insert' => $coll,
            'documents' => [(object)$doc],
            'ordered' => true,
        ]);
        if (!empty($res['writeErrors'])) {
            throw new Exception($res['writeErrors'][0]['errmsg'] ?? 'Erro ao inserir documento.');
        }
        return (string)$doc['_id'];
    }

    public static function insertMany(string $coll, array $docs): int
    {
        if (!$docs) return 0;
        if (self::hasNativeExtension()) {
            $bulkCls = 'MongoDB\Driver\BulkWrite';
            $bulk = new $bulkCls(['ordered' => false]);
            foreach ($docs as $d) {
                $bulk->insert($d);
            }
            return self::manager()->executeBulkWrite(self::db() . '.' . $coll, $bulk)->getInsertedCount();
        }

        $prepared = [];
        foreach ($docs as $d) {
            if (!isset($d['_id'])) {
                $d['_id'] = new ObjectId();
            }
            $prepared[] = (object)$d;
        }
        $res = self::manager()->runCommand(self::db(), [
            'insert' => $coll,
            'documents' => $prepared,
            'ordered' => false,
        ]);
        return (int)($res['n'] ?? 0);
    }

    public static function updateOne(string $coll, array $filter, array $update, array $options = []): int
    {
        if (self::hasNativeExtension()) {
            $bulkCls = 'MongoDB\Driver\BulkWrite';
            $bulk = new $bulkCls();
            $bulk->update($filter, $update, array_merge(['multi' => false, 'upsert' => false], $options));
            $res = self::manager()->executeBulkWrite(self::db() . '.' . $coll, $bulk);
            return $res->getModifiedCount() + $res->getUpsertedCount();
        }

        $res = self::manager()->runCommand(self::db(), [
            'update' => $coll,
            'updates' => [[
                'q' => (object)$filter,
                'u' => (object)$update,
                'upsert' => !empty($options['upsert']),
                'multi' => !empty($options['multi']),
            ]],
        ]);
        if (!empty($res['writeErrors'])) {
            throw new Exception($res['writeErrors'][0]['errmsg'] ?? 'Erro ao atualizar documento.');
        }
        return (int)($res['nModified'] ?? 0) + count($res['upserted'] ?? []);
    }

    public static function deleteOne(string $coll, array $filter): int
    {
        if (self::hasNativeExtension()) {
            $bulkCls = 'MongoDB\Driver\BulkWrite';
            $bulk = new $bulkCls();
            $bulk->delete($filter, ['limit' => 1]);
            return self::manager()->executeBulkWrite(self::db() . '.' . $coll, $bulk)->getDeletedCount();
        }

        $res = self::manager()->runCommand(self::db(), [
            'delete' => $coll,
            'deletes' => [[
                'q' => (object)$filter,
                'limit' => 1,
            ]],
        ]);
        return (int)($res['n'] ?? 0);
    }

    public static function deleteMany(string $coll, array $filter): int
    {
        if (self::hasNativeExtension()) {
            $bulkCls = 'MongoDB\Driver\BulkWrite';
            $bulk = new $bulkCls();
            $bulk->delete($filter, ['limit' => 0]);
            return self::manager()->executeBulkWrite(self::db() . '.' . $coll, $bulk)->getDeletedCount();
        }

        $res = self::manager()->runCommand(self::db(), [
            'delete' => $coll,
            'deletes' => [[
                'q' => (object)$filter,
                'limit' => 0,
            ]],
        ]);
        return (int)($res['n'] ?? 0);
    }

    public static function count(string $coll, array $filter = []): int
    {
        $res = self::aggregate($coll, [
            ['$match' => (object)$filter],
            ['$count' => 'total'],
        ]);
        return (int)($res[0]['total'] ?? 0);
    }

    public static function aggregate(string $coll, array $pipeline): array
    {
        $normPipeline = array_map(fn($stage) => (object)$stage, $pipeline);
        $cursor = self::manager()->executeCommand(self::db(), new Command([
            'aggregate' => $coll,
            'pipeline' => $normPipeline,
            'cursor' => (object)['batchSize' => 1000],
        ]));
        $cursor->setTypeMap(self::$typeMap);
        return $cursor->toArray();
    }

    public static function command(array $cmd): array
    {
        $cursor = self::manager()->executeCommand(self::db(), new Command($cmd));
        $cursor->setTypeMap(self::$typeMap);
        return $cursor->toArray();
    }

    /**
     * Contador sequencial (usado no número de protocolo).
     * findAndModify com $inc é atômico: dois pedidos ao mesmo tempo nunca recebem o mesmo número.
     */
    public static function nextSequence(string $name): int
    {
        $res = self::command([
            'findAndModify' => 'contadores',
            'query' => (object)['_id' => $name],
            'update' => (object)['$inc' => (object)['seq' => 1]],
            'new' => true,
            'upsert' => true,
        ]);
        return (int)($res[0]['value']['seq'] ?? 1);
    }

    // ------------------------------------------------------------
    // Utilitários
    // ------------------------------------------------------------
    /**
     * Converte texto em ObjectId com validação.
     * Retorna null se não for um ID válido (evita injeção de operadores como {"$ne": ...}).
     */
    public static function id($value): ?ObjectId
    {
        if ($value instanceof ObjectId) {
            return $value;
        }
        if (!is_string($value) || !preg_match('/^[a-f0-9]{24}$/i', $value)) {
            return null;
        }
        return new ObjectId($value);
    }

    public static function now(): UTCDateTime
    {
        return new UTCDateTime();
    }

    public static function date(?UTCDateTime $d, string $format = 'd/m/Y H:i'): string
    {
        if (!$d) return '';
        $dt = $d->toDateTime();
        $dt->setTimezone(new DateTimeZone('America/Sao_Paulo'));
        return $dt->format($format);
    }

    /** Regex segura (escapa caracteres especiais do texto digitado), sem diferenciar maiúsculas. */
    public static function regex(string $text): Regex
    {
        return new Regex(preg_quote($text, '/'), 'i');
    }

    // ------------------------------------------------------------
    // Configuração inicial automática (índices + perguntas iniciais)
    // ------------------------------------------------------------
    public static function ensureSetup(): void
    {
        if (self::$setupChecked) return;
        self::$setupChecked = true;

        $meta = self::findOne('config_sistema', ['_id' => 'setup']);
        if ($meta && (int)($meta['versao'] ?? 0) >= self::SETUP_VERSION) {
            return;
        }

        self::createIndexes();

        // Popula as perguntas frequentes iniciais apenas se a coleção estiver vazia
        if (self::count('faq') === 0) {
            $seed = __DIR__ . '/../database/mongo/faq_seed.json';
            if (is_file($seed)) {
                $items = json_decode(file_get_contents($seed), true) ?: [];
                $now = self::now();
                $docs = [];
                foreach ($items as $i => $f) {
                    $docs[] = [
                        'categoria' => $f['categoria'],
                        'pergunta' => $f['pergunta'],
                        'resposta' => $f['resposta'],
                        'palavras_chave' => array_values($f['palavras_chave'] ?? []),
                        'ordem' => (int)($f['ordem'] ?? ($i + 1)),
                        'ativo' => true,
                        'util' => ['sim' => 0, 'nao' => 0],
                        'visualizacoes' => 0,
                        'criado_em' => $now,
                        'atualizado_em' => $now,
                    ];
                }
                self::insertMany('faq', $docs);
            }
        }

        self::updateOne('config_sistema', ['_id' => 'setup'],
            ['$set' => ['versao' => self::SETUP_VERSION, 'atualizado_em' => self::now()]],
            ['upsert' => true]);
    }

    /** Cria todos os índices. Cada um é independente: se um falhar, os outros continuam. */
    public static function createIndexes(): array
    {
        $indexes = [
            'faq' => [
                // Índice de TEXTO em português: busca por palavras, ignorando acentos e plural/singular
                [
                    'key' => (object)['pergunta' => 'text', 'palavras_chave' => 'text', 'resposta' => 'text'],
                    'name' => 'faq_busca_texto',
                    'default_language' => 'portuguese',
                    'weights' => (object)['pergunta' => 10, 'palavras_chave' => 8, 'resposta' => 2],
                ],
                ['key' => (object)['ativo' => 1, 'categoria' => 1, 'ordem' => 1], 'name' => 'faq_categoria_ordem'],
            ],
            'pedidos_ajuda' => [
                // Fila de atendimento: filtra por status e ordena por data
                ['key' => (object)['status' => 1, 'criado_em' => 1], 'name' => 'pedidos_fila'],
                ['key' => (object)['protocolo' => 1], 'name' => 'pedidos_protocolo', 'unique' => true],
                // TTL: apaga o pedido 1 ano após o ENCERRAMENTO.
                // Pedidos em aberto não têm "encerrado_em", então nunca são apagados.
                ['key' => (object)['encerrado_em' => 1], 'name' => 'pedidos_ttl_lgpd', 'expireAfterSeconds' => self::TTL_PEDIDOS],
            ],
            'perguntas_sem_resposta' => [
                ['key' => (object)['criado_em' => 1], 'name' => 'sem_resposta_ttl', 'expireAfterSeconds' => self::TTL_SEM_RESPOSTA],
                ['key' => (object)['texto_normalizado' => 1], 'name' => 'sem_resposta_texto'],
            ],
            'logs_auditoria' => [
                ['key' => (object)['data_hora' => -1], 'name' => 'logs_data_hora'],
                ['key' => (object)['modulo' => 1, 'data_hora' => -1], 'name' => 'logs_modulo_data'],
                ['key' => (object)['usuario.id' => 1, 'data_hora' => -1], 'name' => 'logs_usuario_data'],
                ['key' => (object)['data_hora' => 1], 'name' => 'logs_ttl_2anos', 'expireAfterSeconds' => self::TTL_LOGS],
            ],
        ];

        $result = [];
        foreach ($indexes as $coll => $list) {
            foreach ($list as $idx) {
                try {
                    self::command(['createIndexes' => $coll, 'indexes' => [(object)$idx]]);
                    $result[] = "OK   $coll.{$idx['name']}";
                } catch (Throwable $e) {
                    $result[] = "ERRO $coll.{$idx['name']}: " . $e->getMessage();
                    error_log('[CMDPCD][Mongo] índice ' . $idx['name'] . ': ' . $e->getMessage());
                }
            }
        }
        return $result;
    }
}
}

