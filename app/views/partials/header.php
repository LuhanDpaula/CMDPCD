<?php
$currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (BASE_URL !== '' && strpos($currentUri, BASE_URL) === 0) {
    $currentUri = substr($currentUri, strlen(BASE_URL));
}
$currentUri = '/' . trim($currentUri, '/');

function menuActive($path, $current) {
    if ($path === '/' && ($current === '/' || $current === '/home' || $current === '/index.html')) {
        return 'text-primary';
    }
    // Remove .html e barras para comparação
    $cleanPath = '/' . trim(str_replace('.html', '', $path), '/');
    $cleanCurrent = '/' . trim(str_replace('.html', '', $current), '/');
    
    if ($cleanPath === '/noticias' && ($cleanCurrent === '/noticias' || $cleanCurrent === '/noticia')) {
        return 'text-primary';
    }

    return $cleanPath === $cleanCurrent ? 'text-primary' : 'text-foreground/70 hover:text-primary';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CMDPCD Jahu - Conselho Municipal dos Direitos da Pessoa com Deficiência</title>
  <meta name="description" content="Conselho Municipal dos Direitos da Pessoa com Deficiência de Jahu. Promovendo inclusão, acessibilidade e cidadania.">
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/utilitarios.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/a11y.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/chat.css">
  <meta name="base-url" content="<?= e(BASE_URL) ?>">
  <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
  <script>
    // Aplica tamanho de fonte/contraste salvos antes de desenhar a página (evita "piscar")
    try { var a = JSON.parse(localStorage.getItem('cmdpcd_a11y')) || {}; if (a.font && a.font !== 100) document.documentElement.style.fontSize = a.font + '%'; if (a.contraste) document.documentElement.classList.add('high-contrast'); } catch (e) {}
  </script>
  
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-background text-foreground font-sans">
  <a href="#conteudo-principal" class="skip-link">Pular para o conteúdo principal</a>
  
  <!-- CABEÇALHO -->
  <header class="fixed top-0 left-0 right-0 z-50 bg-card/95 backdrop-blur-md border-b border-border">
    <div class="container mx-auto px-4 flex items-center justify-between h-16">
      <a href="<?= BASE_URL ?>/" class="flex items-center gap-2 font-extrabold text-xl text-primary" aria-label="CMDPCD Jahu - página inicial">
        <i data-lucide="accessibility" class="w-7 h-7" aria-hidden="true"></i>
        <span>CMDPCD Jahu</span>
      </a>

      <!-- Navegação Desktop -->
      <nav class="hidden lg:flex items-center gap-6" aria-label="Navegação principal">
        <a href="<?= BASE_URL ?>/" class="text-sm font-semibold <?= menuActive('/', $currentUri) ?> transition-colors">Início</a>
        <a href="<?= BASE_URL ?>/quem-somos" class="text-sm font-semibold <?= menuActive('/quem-somos', $currentUri) ?> transition-colors">Quem Somos</a>
        <a href="<?= BASE_URL ?>/iniciativas" class="text-sm font-semibold <?= menuActive('/iniciativas', $currentUri) ?> transition-colors">Iniciativas</a>
        <a href="<?= BASE_URL ?>/noticias" class="text-sm font-semibold <?= menuActive('/noticias', $currentUri) ?> transition-colors">Notícias</a>
        <a href="<?= BASE_URL ?>/transparencia" class="text-sm font-semibold <?= menuActive('/transparencia', $currentUri) ?> transition-colors">Transparência</a>
        <a href="<?= BASE_URL ?>/apoio" class="text-sm font-semibold <?= menuActive('/apoio', $currentUri) ?> transition-colors">Apoio</a>
        <a href="<?= BASE_URL ?>/contato" class="text-sm font-semibold <?= menuActive('/contato', $currentUri) ?> transition-colors">Contato</a>
        
        <a href="<?= BASE_URL ?>/cadastro-pcd" class="text-sm font-bold bg-primary text-primary-foreground px-4 py-2 rounded-full hover:bg-primary/90 transition-colors">
          Registrar PCD
        </a>
        <a href="<?= BASE_URL ?>/admin" class="text-sm font-semibold <?= menuActive('/admin', $currentUri) ?> transition-colors flex items-center gap-1.5"><i data-lucide="lock" class="w-3.5 h-3.5"></i>
          Admin
        </a>
      </nav>

      <!-- Botão Menu Mobile -->
      <button id="mobile-menu-btn" class="lg:hidden text-foreground" aria-label="Abrir menu" aria-expanded="false">
        <i data-lucide="menu" class="w-6 h-6"></i>
      </button>
    </div>

    <!-- Menu Mobile Oculto -->
    <nav id="mobile-menu" class="hidden lg:hidden bg-card border-b border-border" aria-label="Menu mobile">
      <div class="container mx-auto px-4 flex flex-col gap-3 py-4">
        <a href="<?= BASE_URL ?>/" class="text-sm font-semibold <?= menuActive('/', $currentUri) ?> transition-colors">Início</a>
        <a href="<?= BASE_URL ?>/quem-somos" class="text-sm font-semibold <?= menuActive('/quem-somos', $currentUri) ?> transition-colors">Quem Somos</a>
        <a href="<?= BASE_URL ?>/iniciativas" class="text-sm font-semibold <?= menuActive('/iniciativas', $currentUri) ?> transition-colors">Iniciativas</a>
        <a href="<?= BASE_URL ?>/noticias" class="text-sm font-semibold <?= menuActive('/noticias', $currentUri) ?> transition-colors">Notícias</a>
        <a href="<?= BASE_URL ?>/transparencia" class="text-sm font-semibold <?= menuActive('/transparencia', $currentUri) ?> transition-colors">Transparência</a>
        <a href="<?= BASE_URL ?>/apoio" class="text-sm font-semibold <?= menuActive('/apoio', $currentUri) ?> transition-colors">Apoio</a>
        <a href="<?= BASE_URL ?>/contato" class="text-sm font-semibold <?= menuActive('/contato', $currentUri) ?> transition-colors">Contato</a>
        
        <a href="<?= BASE_URL ?>/cadastro-pcd" class="text-sm font-bold bg-primary text-primary-foreground px-4 py-2 rounded-full text-center hover:bg-primary/90 transition-colors mt-2">
          Registrar PCD
        </a>
        <a href="<?= BASE_URL ?>/admin" class="text-sm font-semibold <?= menuActive('/admin', $currentUri) ?> transition-colors flex items-center gap-1.5 justify-center mt-1"><i data-lucide="lock" class="w-3.5 h-3.5"></i>
          Área Administrativa
        </a>
      </div>
    </nav>
  </header>
