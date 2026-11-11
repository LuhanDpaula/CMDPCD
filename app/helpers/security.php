<?php
/**
 * ============================================================
 *  HELPERS DE SEGURANÇA - CMDPCD Jahu
 * ============================================================
 *  - Escape de saída (XSS)
 *  - Token CSRF
 *  - Mensagens de erro seguras (sem vazar detalhes internos)
 *  - Limite de tentativas (força bruta / spam)
 *  - Validação real de arquivos enviados (MIME)
 *  - Diretório privado para documentos sensíveis dos PCDs
 */

/**
 * Exceção cuja mensagem PODE ser exibida ao usuário
 * (ex.: "Arquivo maior que 5MB"). Qualquer outra exceção
 * é registrada no log e o usuário vê só uma mensagem genérica.
 */
class AppException extends Exception {}

/** Pasta privada (fora de /public) onde ficam RG, laudo e comprovante dos PCDs. */
define('PRIVATE_STORAGE', realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR . 'storage');
define('PCD_DOCS_DIR', PRIVATE_STORAGE . DIRECTORY_SEPARATOR . 'pcd_docs');

/** Escapa texto para HTML (use em TODA saída de dado vindo do banco/usuário). */
function e($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** json_encode seguro para ser impresso dentro de <script> (evita fechar a tag). */
function js_json($value): string {
    return json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
}

// ------------------------------------------------------------
// CSRF
// ------------------------------------------------------------
function csrf_token(): string {
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

/** Para links de ação via GET (excluir, deferir...). */
function csrf_query(): string {
    return '_token=' . urlencode(csrf_token());
}

function csrf_verify(): bool {
    $sent = $_POST['_token']
        ?? $_GET['_token']
        ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    return is_string($sent)
        && !empty($_SESSION['_csrf_token'])
        && hash_equals($_SESSION['_csrf_token'], $sent);
}

// ------------------------------------------------------------
// Erros
// ------------------------------------------------------------
/**
 * Retorna uma mensagem segura para o usuário.
 * Detalhes técnicos (SQL, caminhos, nomes de tabelas) vão só para o log do servidor.
 */
function safeError(Throwable $e): string {
    if ($e instanceof AppException) {
        return $e->getMessage();
    }
    error_log('[CMDPCD] ' . get_class($e) . ': ' . $e->getMessage() . ' em ' . $e->getFile() . ':' . $e->getLine());
    return 'Ocorreu um erro interno. Tente novamente ou contate o administrador.';
}

// ------------------------------------------------------------
// Limite de tentativas (por IP, salvo em arquivo privado)
// ------------------------------------------------------------
function rate_limit_file(string $key): string {
    $dir = PRIVATE_STORAGE . DIRECTORY_SEPARATOR . 'ratelimit';
    if (!is_dir($dir)) {
        @mkdir($dir, 0750, true);
    }
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'cli';
    return $dir . DIRECTORY_SEPARATOR . hash('sha256', $key . '|' . $ip) . '.json';
}

/** true se o IP ainda pode tentar; false se estourou o limite na janela. */
function rate_limit_check(string $key, int $maxAttempts, int $windowSeconds): bool {
    $file = rate_limit_file($key);
    if (!is_file($file)) return true;
    $data = json_decode((string)@file_get_contents($file), true) ?: [];
    $now = time();
    $hits = array_filter($data, fn($t) => ($now - (int)$t) < $windowSeconds);
    return count($hits) < $maxAttempts;
}

function rate_limit_hit(string $key, int $windowSeconds): void {
    $file = rate_limit_file($key);
    $data = is_file($file) ? (json_decode((string)@file_get_contents($file), true) ?: []) : [];
    $now = time();
    $data = array_values(array_filter($data, fn($t) => ($now - (int)$t) < $windowSeconds));
    $data[] = $now;
    @file_put_contents($file, json_encode($data), LOCK_EX);
}

function rate_limit_clear(string $key): void {
    $file = rate_limit_file($key);
    if (is_file($file)) @unlink($file);
}

// ------------------------------------------------------------
// Uploads
// ------------------------------------------------------------
/**
 * Valida um arquivo enviado pelo CONTEÚDO real (magic bytes), não só pela extensão.
 * Impede, por exemplo, enviar "virus.php" renomeado para "foto.jpg".
 *
 * @param array $file    Item de $_FILES
 * @param array $allowed Mapa mime => extensão permitida
 * @return string        Extensão segura a ser usada no nome final
 */
function validate_upload(array $file, array $allowed, int $maxBytes, string $label = 'arquivo'): string {
    if (!isset($file['error']) || is_array($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        throw new AppException("Falha no envio do $label.");
    }
    if (!is_uploaded_file($file['tmp_name'])) {
        throw new AppException("Envio inválido do $label.");
    }
    if ($file['size'] <= 0 || $file['size'] > $maxBytes) {
        throw new AppException("O $label excede o limite de " . round($maxBytes / 1048576) . "MB.");
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        throw new AppException("Tipo de arquivo inválido para $label.");
    }

    // Para imagens, confirma que é realmente uma imagem decodificável
    if (strpos($mime, 'image/') === 0 && @getimagesize($file['tmp_name']) === false) {
        throw new AppException("A imagem enviada em $label está corrompida ou é inválida.");
    }

    // Bloqueia PDFs/imagens com código PHP embutido (polyglot)
    $head = (string)@file_get_contents($file['tmp_name'], false, null, 0, 1024 * 1024);
    if (preg_match('/<\?php|<\?=|<script/i', $head)) {
        throw new AppException("O $label contém conteúdo não permitido.");
    }

    return $allowed[$mime];
}

/** Nome de arquivo aleatório e imprevisível (não dá para "adivinhar" o arquivo de outra pessoa). */
function random_filename(string $prefix, string $ext): string {
    return $prefix . bin2hex(random_bytes(16)) . '.' . $ext;
}

// ------------------------------------------------------------
// Validações simples
// ------------------------------------------------------------
function only_digits($v): string {
    if (!is_scalar($v)) return '';   // arrays/objetos (ex.: campo[$ne]) são descartados
    return preg_replace('/\D/', '', (string)$v);
}

function valid_cpf($cpf): bool {
    $cpf = only_digits($cpf);
    if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) return false;
    for ($t = 9; $t < 11; $t++) {
        $d = 0;
        for ($c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ((int)$cpf[$t] !== $d) return false;
    }
    return true;
}

/** Garante que o valor está numa lista permitida; senão devolve o padrão. */
function in_list($value, array $allowed, $default = null) {
    return in_array($value, $allowed, true) ? $value : $default;
}

/** Corta strings longas demais (evita lixo/ataques com payloads gigantes). */
function limit_str($value, int $max): string {
    if (!is_scalar($value)) return '';   // arrays/objetos (ex.: campo[$ne]) são descartados
    return mb_substr(trim((string)$value), 0, $max);
}

// ------------------------------------------------------------
// HTML seguro para o conteúdo das notícias
// ------------------------------------------------------------
/**
 * Mantém só formatação básica (parágrafos, negrito, listas, links http/https)
 * e remove scripts, eventos (onclick, onerror...), iframes, estilos etc.
 * Assim um usuário "editor" não consegue injetar JavaScript no site.
 */
function sanitize_html($html): string {
    $html = (string)$html;
    if (trim($html) === '') return '';

    // Texto puro (sem tags): converte quebras de linha em parágrafos
    if ($html === strip_tags($html)) {
        $parts = preg_split('/\R{2,}/', trim($html));
        return implode('', array_map(fn($p) => '<p>' . nl2br(e(trim($p))) . '</p>', $parts));
    }

    // Se a extensão DOM não existir no servidor, remove todas as tags (modo mais seguro)
    if (!class_exists('DOMDocument')) {
        return '<p>' . nl2br(e(strip_tags($html))) . '</p>';
    }

    $allowedTags = ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li', 'h2', 'h3', 'h4', 'blockquote', 'a'];

    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="UTF-8"><div id="__root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET);
    libxml_clear_errors();

    $root = $doc->getElementById('__root');
    if (!$root) return e(strip_tags($html));

    $clean = function (DOMNode $node) use (&$clean, $allowedTags) {
        for ($i = $node->childNodes->length - 1; $i >= 0; $i--) {
            $child = $node->childNodes->item($i);
            if ($child instanceof DOMElement) {
                $tag = strtolower($child->tagName);
                if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'form', 'svg', 'math', 'template'], true)) {
                    $node->removeChild($child);
                    continue;
                }
                $clean($child);
                if (!in_array($tag, $allowedTags, true)) {
                    // Remove a tag mas mantém o texto de dentro
                    while ($child->firstChild) {
                        $node->insertBefore($child->firstChild, $child);
                    }
                    $node->removeChild($child);
                    continue;
                }
                // Remove TODOS os atributos, exceto href seguro em <a>
                $href = $tag === 'a' ? trim($child->getAttribute('href')) : '';
                while ($child->attributes->length) {
                    $child->removeAttribute($child->attributes->item(0)->nodeName);
                }
                if ($tag === 'a' && preg_match('#^(https?:|mailto:)#i', $href)) {
                    $child->setAttribute('href', $href);
                    $child->setAttribute('target', '_blank');
                    $child->setAttribute('rel', 'noopener noreferrer');
                }
            } elseif ($child instanceof DOMComment) {
                $node->removeChild($child);
            }
        }
    };
    $clean($root);

    $out = '';
    foreach ($root->childNodes as $c) {
        $out .= $doc->saveHTML($c);
    }
    return $out;
}
