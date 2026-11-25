<?php
require_once __DIR__ . '/mb_compat.php';
/**
 * PDFDocument - gerador de documentos PDF puro PHP, offline e sem Composer.
 * API: addTitle(), addSection(), addText(), addField(), addSeparator(), output().
 * Usa fontes PDF padrão (Helvetica) e codificação WinAnsi para acentos em português.
 */
class PdfDocument
{
    private array $pages = [[]];
    private int $page = 0;
    private float $y = 790;
    private string $title;
    private const PAGE_WIDTH = 595.28;
    private const PAGE_HEIGHT = 841.89;

    public function __construct(string $title = 'Documento')
    {
        $this->title = $title;
    }

    /** Cria um documento a partir de HTML simples (texto, títulos, listas e parágrafos). */
    public static function fromHtml(string $html, string $title = 'Documento'): self
    {
        $document = new self($title);
        $document->addHtml($html);
        return $document;
    }

    private function newPage(): void
    {
        $this->page++;
        $this->pages[$this->page] = [];
        $this->y = 790;
        $this->line(45, $this->y + 18, 550, $this->y + 18, '0.78 0.82 0.86');
        $this->text('CMDPCD Jahu', 45, $this->y + 28, 8, true, '0.32 0.38 0.44');
    }

    private function addCommand(string $command): void
    {
        $this->pages[$this->page][] = $command;
    }

    private function pdfText(string $text): string
    {
        $converted = function_exists('iconv') ? @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text) : $text;
        if ($converted === false) $converted = preg_replace('/[^\x20-\x7E]/', '?', $text);
        return str_replace(['\\', '(', ')', "\r", "\n"], ['\\\\', '\\(', '\\)', '', ' '], (string)$converted);
    }

    private function text(string $text, float $x, float $y, int $size = 10, bool $bold = false, string $color = '0.12 0.16 0.20'): void
    {
        $font = $bold ? 'F2' : 'F1';
        $this->addCommand(sprintf("BT %s rg /%s %d Tf 1 0 0 1 %.2F %.2F Tm (%s) Tj ET", $color, $font, $size, $x, $y, $this->pdfText($text)));
    }

    private function line(float $x1, float $y1, float $x2, float $y2, string $color = '0.86 0.88 0.90'): void
    {
        $this->addCommand(sprintf("q %s RG 0.6 w %.2F %.2F m %.2F %.2F l S Q", $color, $x1, $y1, $x2, $y2));
    }

    private function ensureSpace(float $height = 18): void
    {
        if ($this->y - $height < 48) $this->newPage();
    }

    private function wrap(string $text, int $maxChars): array
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text));
        if ($text === '') return ['—'];
        if (function_exists('mb_strwidth')) {
            $words = preg_split('/\s+/u', $text); $lines = []; $line = '';
            foreach ($words as $word) {
                $candidate = $line === '' ? $word : $line . ' ' . $word;
                if (mb_strwidth($candidate, 'UTF-8') > $maxChars && $line !== '') { $lines[] = $line; $line = $word; }
                else $line = $candidate;
            }
            if ($line !== '') $lines[] = $line;
            return $lines ?: ['—'];
        }
        return explode("\n", wordwrap($text, $maxChars, "\n", true));
    }

    public function addTitle(string $title, ?string $subtitle = null): self
    {
        $this->ensureSpace(55);
        $this->text($title, 45, $this->y, 19, true, '0.08 0.20 0.34');
        $this->y -= 20;
        if ($subtitle) {
            $this->text($subtitle, 45, $this->y, 9, false, '0.38 0.42 0.46');
            $this->y -= 19;
        } else $this->y -= 14;
        $this->line(45, $this->y, 550, $this->y, '0.70 0.76 0.82');
        $this->y -= 22;
        return $this;
    }

    public function addSection(string $title): self
    {
        $this->ensureSpace(30);
        $this->y -= 4;
        $this->text($title, 45, $this->y, 11, true, '0.08 0.28 0.45');
        $this->y -= 15;
        return $this;
    }

    public function addText(string $text, int $size = 10): self
    {
        foreach ($this->wrap($text, $size >= 12 ? 78 : 96) as $line) {
            $this->ensureSpace(15);
            $this->text($line, 45, $this->y, $size, false);
            $this->y -= 13;
        }
        return $this;
    }

    public function addField(string $label, $value): self
    {
        if (is_array($value)) $value = implode(', ', array_filter(array_map(function ($item) {
            if (is_array($item)) return $item['nome'] ?? $item['titulo'] ?? '';
            return (string)$item;
        }, $value)));
        if (is_bool($value)) $value = $value ? 'Sim' : 'Não';
        $value = trim((string)($value ?? ''));
        if ($value === '') $value = 'Não informado';
        $lines = $this->wrap($value, 82);
        $this->ensureSpace(14 + count($lines) * 12);
        $this->text($label . ':', 45, $this->y, 9, true, '0.25 0.30 0.35');
        $this->text(array_shift($lines) ?? '—', 170, $this->y, 9, false);
        $this->y -= 13;
        foreach ($lines as $line) {
            $this->ensureSpace(13);
            $this->text($line, 170, $this->y, 9, false);
            $this->y -= 12;
        }
        return $this;
    }

    /** Converte conteúdo HTML simples em texto formatado no PDF (sem depender de navegador/CDN). */
    public function addHtml(string $html, int $size = 10): self
    {
        $html = preg_replace('#<(br\s*/?|/p|/div|/li|/h[1-6]|/tr)\s*>#i', "\n", $html);
        $html = preg_replace('#<li\b[^>]*>#i', "• ", $html);
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        foreach (preg_split('/\n+/', $text) as $paragraph) {
            $paragraph = trim($paragraph);
            if ($paragraph !== '') { $this->addText($paragraph, $size); $this->y -= 5; }
        }
        return $this;
    }

    public function addSeparator(): self
    {
        $this->y -= 4;
        $this->line(45, $this->y, 550, $this->y);
        $this->y -= 12;
        return $this;
    }

    public function render(): string
    {
        $objects = [];
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $kids = [];
        foreach ($this->pages as $idx => $commands) {
            $pageNum = 5 + $idx * 2;
            $contentNum = $pageNum + 1;
            $kids[] = $pageNum . ' 0 R';
            $footer = "BT /F1 8 Tf 0.45 0.48 0.52 rg 1 0 0 1 45 28 Tm (CMDPCD Jahu - Documento administrativo) Tj ET\n";
            $footer .= "BT /F1 8 Tf 0.45 0.48 0.52 rg 1 0 0 1 520 28 Tm (" . ($idx + 1) . '/' . count($this->pages) . ") Tj ET\n";
            $stream = implode("\n", $commands) . "\n" . $footer;
            $objects[$pageNum] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595.28 841.89] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents ' . $contentNum . ' 0 R >>';
            $objects[$contentNum] = '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . "endstream";
        }
        $objects[2] = '<< /Type /Pages /Kids [' . implode(' ', $kids) . '] /Count ' . count($kids) . ' >>';
        $objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objects[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';
        ksort($objects);
        $pdf = "%PDF-1.4\n%CMDPCD\n"; $offsets = [0];
        $max = max(array_keys($objects));
        for ($i = 1; $i <= $max; $i++) {
            $offsets[$i] = strlen($pdf);
            $pdf .= $i . " 0 obj\n" . ($objects[$i] ?? '<< >>') . "\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . ($max + 1) . "\n0000000000 65535 f \n";
        for ($i = 1; $i <= $max; $i++) $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        $pdf .= "trailer\n<< /Size " . ($max + 1) . " /Root 1 0 R /Info << /Title (" . $this->pdfText($this->title) . ") >> >>\nstartxref\n" . $xref . "\n%%EOF";
        return $pdf;
    }

    public function output(string $filename): void
    {
        $filename = preg_replace('/[^A-Za-z0-9._-]/', '_', $filename);
        $content = $this->render();
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . ($filename ?: 'documento.pdf') . '"');
        header('Content-Length: ' . strlen($content));
        header('Cache-Control: private, no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        echo $content;
        exit;
    }
}
