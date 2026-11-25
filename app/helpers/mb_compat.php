<?php
/**
 * Compatibilidade mínima com funções mbstring para instalações PHP locais
 * onde a extensão ainda não foi habilitada. Quando mbstring existe, nada muda.
 */
if (!defined('MB_CASE_TITLE')) define('MB_CASE_TITLE', 2);

if (!function_exists('mb_str_split')) {
    function mb_str_split(string $string, int $length = 1, ?string $encoding = null): array {
        if ($length < 1) return [];
        $chars = preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
        if ($chars === false) $chars = str_split($string);
        return $length === 1 ? $chars : array_map(static fn($chunk) => implode('', $chunk), array_chunk($chars, $length));
    }
}
if (!function_exists('mb_strlen')) {
    function mb_strlen(string $string, ?string $encoding = null): int {
        return count(mb_str_split($string, 1, $encoding));
    }
}
if (!function_exists('mb_substr')) {
    function mb_substr(string $string, int $start, ?int $length = null, ?string $encoding = null): string {
        $chars = mb_str_split($string, 1, $encoding);
        $slice = array_slice($chars, $start, $length);
        return implode('', $slice);
    }
}
if (!function_exists('mb_strpos')) {
    function mb_strpos(string $haystack, string $needle, int $offset = 0, ?string $encoding = null) {
        $prefix = mb_substr($haystack, 0, $offset, $encoding);
        $byteOffset = strlen($prefix);
        $found = strpos($haystack, $needle, $byteOffset);
        return $found === false ? false : mb_strlen(substr($haystack, 0, $found), $encoding);
    }
}
if (!function_exists('mb_strtolower')) {
    function mb_strtolower(string $string, ?string $encoding = null): string {
        $upper = ['Á'=>'á','À'=>'à','Â'=>'â','Ã'=>'ã','Ä'=>'ä','É'=>'é','È'=>'è','Ê'=>'ê','Ë'=>'ë','Í'=>'í','Ì'=>'ì','Î'=>'î','Ï'=>'ï','Ó'=>'ó','Ò'=>'ò','Ô'=>'ô','Õ'=>'õ','Ö'=>'ö','Ú'=>'ú','Ù'=>'ù','Û'=>'û','Ü'=>'ü','Ç'=>'ç','Ñ'=>'ñ'];
        return strtolower(strtr($string, $upper));
    }
}
if (!function_exists('mb_strtoupper')) {
    function mb_strtoupper(string $string, ?string $encoding = null): string {
        $lower = ['á'=>'Á','à'=>'À','â'=>'Â','ã'=>'Ã','ä'=>'Ä','é'=>'É','è'=>'È','ê'=>'Ê','ë'=>'Ë','í'=>'Í','ì'=>'Ì','î'=>'Î','ï'=>'Ï','ó'=>'Ó','ò'=>'Ò','ô'=>'Ô','õ'=>'Õ','ö'=>'Ö','ú'=>'Ú','ù'=>'Ù','û'=>'Û','ü'=>'Ü','ç'=>'Ç','ñ'=>'Ñ'];
        return strtoupper(strtr($string, $lower));
    }
}
if (!function_exists('mb_convert_case')) {
    function mb_convert_case(string $string, int $mode, ?string $encoding = null): string {
        if ($mode !== MB_CASE_TITLE) return mb_strtolower($string, $encoding);
        $lower = mb_strtolower($string, $encoding);
        return preg_replace_callback('/(^|[\s\-])([\p{L}\p{N}])/u', static fn($m) => $m[1] . mb_strtoupper($m[2], $encoding), $lower) ?? $lower;
    }
}
if (!function_exists('mb_strwidth')) {
    function mb_strwidth(string $string, ?string $encoding = null): int {
        $width = 0;
        foreach (mb_str_split($string, 1, $encoding) as $char) {
            $width += preg_match('/[\x{1100}-\x{115F}\x{2E80}-\x{A4CF}\x{AC00}-\x{D7A3}\x{F900}-\x{FAFF}\x{FE10}-\x{FE6F}\x{FF01}-\x{FF60}\x{FFE0}-\x{FFE6}]/u', $char) ? 2 : 1;
        }
        return $width;
    }
}
if (!function_exists('mb_strimwidth')) {
    function mb_strimwidth(string $string, int $start, int $width, string $trim_marker = '', ?string $encoding = null): string {
        $result = ''; $used = 0;
        foreach (mb_str_split(mb_substr($string, $start, null, $encoding), 1, $encoding) as $char) {
            $charWidth = mb_strwidth($char, $encoding);
            if ($used + $charWidth > $width) break;
            $result .= $char; $used += $charWidth;
        }
        return mb_strlen($result, $encoding) < mb_strlen(mb_substr($string, $start, null, $encoding), $encoding) ? $result . $trim_marker : $result;
    }
}
