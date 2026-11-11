<?php 
require_once __DIR__ . '/partials/header.php'; 

// Mapeamento de categorias/temas
$temaMap = [
    'eventos'        => 'Eventos',
    'direitos'       => 'Direitos',
    'inclusao'       => 'Inclusão',
    'saude'          => 'Saúde',
    'educacao'       => 'Educação',
    'acessibilidade' => 'Acessibilidade',
    'emprego'        => 'Emprego',
    'informativos'   => 'Informativos',
    'outros'         => 'Outros'
];

$categoria = $temaMap[$noticia['tema'] ?? 'outros'] ?? ucfirst($noticia['tema'] ?? 'Outros');
$dataRaw = $noticia['data_publicacao'] ?? $noticia['criado_em'] ?? '';
$dataStr = !empty($dataRaw) ? date('d/m/Y', strtotime($dataRaw)) : date('d/m/Y');
$dataISO = !empty($dataRaw) ? date('c', strtotime($dataRaw)) : date('c');

$imagem = !empty($noticia['imagem_capa']) 
    ? (strpos($noticia['imagem_capa'], 'http') === 0 ? $noticia['imagem_capa'] : BASE_URL . '/' . ltrim($noticia['imagem_capa'], '/'))
    : 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=1200&h=600&fit=crop';

// Estimativa de leitura
$textoSemTags = strip_tags($noticia['conteudo'] ?? '');
$totalPalavras = str_word_count($textoSemTags);
$tempoLeitura = max(1, (int)ceil($totalPalavras / 200));

// URL atual para compartilhamento
$httpScheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$currentUrl = $httpScheme . '://' . $host . BASE_URL . '/noticia?id=' . urlencode($noticia['id']);
$encodedUrl = urlencode($currentUrl);
$encodedTitulo = urlencode($noticia['titulo'] ?? 'Notícia CMDPCD Jahu');
?>

<main id="conteudo-principal" class="pt-20 pb-20 bg-background min-h-screen">
  
  <!-- Breadcrumbs & Navegação Superior -->
  <section class="border-b border-border bg-card/40 py-4 mb-8">
    <div class="container mx-auto px-4 max-w-5xl">
      <nav aria-label="Navegação estrutural" class="flex items-center flex-wrap gap-2 text-xs md:text-sm text-muted-foreground">
        <a href="<?= BASE_URL ?>/" class="hover:text-primary transition-colors flex items-center gap-1">
          <i data-lucide="home" class="w-3.5 h-3.5" aria-hidden="true"></i> Início
        </a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-muted-foreground/60" aria-hidden="true"></i>
        <a href="<?= BASE_URL ?>/noticias" class="hover:text-primary transition-colors">
          Notícias
        </a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-muted-foreground/60" aria-hidden="true"></i>
        <span class="text-foreground font-medium truncate max-w-[200px] sm:max-w-xs md:max-w-md" aria-current="page">
          <?= htmlspecialchars($noticia['titulo']) ?>
        </span>
      </nav>
    </div>
  </section>

  <div class="container mx-auto px-4 max-w-4xl">
    
    <!-- Botão de Voltar -->
    <div class="mb-6">
      <a href="<?= BASE_URL ?>/noticias" class="inline-flex items-center gap-2 text-sm font-bold text-primary hover:underline group">
        <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform" aria-hidden="true"></i>
        Voltar para todas as notícias
      </a>
    </div>

    <!-- Artigo Principal -->
    <article class="bg-card rounded-3xl border border-border p-6 md:p-10 shadow-sm overflow-hidden mb-12">
      
      <!-- Cabeçalho da Notícia -->
      <header class="mb-8 space-y-4">
        
        <!-- Categoria e Metadados -->
        <div class="flex items-center flex-wrap gap-3">
          <span class="inline-block text-xs font-bold text-primary bg-primary/10 px-3.5 py-1 rounded-full uppercase tracking-wider">
            <?= htmlspecialchars($categoria) ?>
          </span>

          <time datetime="<?= $dataISO ?>" class="text-xs md:text-sm text-muted-foreground flex items-center gap-1.5">
            <i data-lucide="calendar" class="w-4 h-4 text-primary" aria-hidden="true"></i>
            <?= $dataStr ?>
          </time>

          <span class="text-xs md:text-sm text-muted-foreground flex items-center gap-1.5">
            <i data-lucide="clock" class="w-4 h-4 text-primary" aria-hidden="true"></i>
            <?= $tempoLeitura ?> min de leitura
          </span>

          <span class="text-xs md:text-sm text-muted-foreground flex items-center gap-1.5">
            <i data-lucide="user" class="w-4 h-4 text-primary" aria-hidden="true"></i>
            <?= htmlspecialchars($noticia['autor_nome'] ?? 'CMDPCD Jahu') ?>
          </span>
        </div>

        <!-- Título -->
        <h1 class="text-2xl sm:text-3xl md:text-5xl font-black text-foreground leading-tight tracking-tight">
          <?= htmlspecialchars($noticia['titulo']) ?>
        </h1>
      </header>

      <!-- Imagem de Capa -->
      <figure class="mb-8 rounded-2xl overflow-hidden border border-border/80 shadow-md bg-muted">
        <img src="<?= e($imagem) ?>" 
             alt="<?= htmlspecialchars($noticia['titulo']) ?>" 
             class="w-full max-h-[460px] object-cover hover:scale-105 transition-transform duration-500" 
             loading="eager">
      </figure>

      <!-- Corpo da Notícia -->
      <div class="noticia-corpo border-b border-border/70 pb-10 mb-8">
        <?= sanitize_html($noticia['conteudo']) ?>
      </div>

      <!-- Barra de Compartilhamento e Ações -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
        <div class="flex items-center gap-2">
          <span class="text-xs font-bold text-muted-foreground uppercase tracking-wider flex items-center gap-1.5">
            <i data-lucide="share-2" class="w-4 h-4 text-primary" aria-hidden="true"></i> Compartilhar:
          </span>
          
          <!-- WhatsApp -->
          <a href="https://api.whatsapp.com/send?text=<?= $encodedTitulo ?>%20<?= $encodedUrl ?>" 
             target="_blank" 
             rel="noopener noreferrer" 
             class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-600 hover:bg-emerald-500/20 transition-all" 
             title="Compartilhar no WhatsApp"
             aria-label="Compartilhar no WhatsApp">
            <i data-lucide="message-circle" class="w-4 h-4" aria-hidden="true"></i>
          </a>

          <!-- Facebook -->
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $encodedUrl ?>" 
             target="_blank" 
             rel="noopener noreferrer" 
             class="p-2.5 rounded-xl bg-blue-500/10 text-blue-600 hover:bg-blue-500/20 transition-all" 
             title="Compartilhar no Facebook"
             aria-label="Compartilhar no Facebook">
            <i data-lucide="facebook" class="w-4 h-4" aria-hidden="true"></i>
          </a>

          <!-- Copiar Link -->
          <button type="button" 
                  id="btn-copiar-link" 
                  class="p-2.5 rounded-xl bg-muted hover:bg-muted/80 text-foreground transition-all cursor-pointer flex items-center gap-1.5 text-xs font-semibold" 
                  title="Copiar link da notícia"
                  aria-label="Copiar link da notícia">
            <i data-lucide="copy" class="w-4 h-4 text-primary" aria-hidden="true"></i>
            <span id="btn-copiar-texto" class="hidden sm:inline">Copiar Link</span>
          </button>
        </div>

        <div>
          <button type="button" 
                  onclick="window.print()" 
                  class="px-4 py-2 rounded-xl bg-muted/60 hover:bg-muted text-muted-foreground hover:text-foreground text-xs font-semibold inline-flex items-center gap-1.5 transition-colors cursor-pointer" 
                  title="Imprimir esta notícia">
            <i data-lucide="printer" class="w-3.5 h-3.5" aria-hidden="true"></i> Imprimir
          </button>
        </div>
      </div>
    </article>

    <!-- Caixa Institucional / Sobre o CMDPCD -->
    <div class="bg-primary/5 rounded-2xl border border-primary/20 p-6 md:p-8 mb-16 flex flex-col md:flex-row items-center gap-6">
      <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
        <i data-lucide="accessibility" class="w-8 h-8" aria-hidden="true"></i>
      </div>
      <div class="flex-1 text-center md:text-left">
        <h3 class="font-bold text-foreground text-lg mb-1">CMDPCD Jahu</h3>
        <p class="text-sm text-muted-foreground leading-relaxed">
          O Conselho Municipal dos Direitos da Pessoa com Deficiência atua ativamente formulando, deliberando e fiscalizando as políticas públicas de inclusão, acessibilidade e cidadania no município de Jahu.
        </p>
      </div>
      <div class="shrink-0">
        <a href="<?= BASE_URL ?>/contato" class="inline-flex items-center gap-2 bg-primary text-primary-foreground text-xs font-bold px-4 py-2.5 rounded-xl hover:bg-primary/90 transition-colors shadow-sm">
          Fale Conosco <i data-lucide="arrow-right" class="w-3.5 h-3.5" aria-hidden="true"></i>
        </a>
      </div>
    </div>

    <!-- Mais Notícias / Recomendações -->
    <?php if (!empty($outrasNoticias)): ?>
      <section class="space-y-6">
        <div class="flex items-center justify-between border-b border-border pb-4">
          <h2 class="text-xl md:text-2xl font-black text-foreground flex items-center gap-2">
            <i data-lucide="newspaper" class="w-6 h-6 text-primary" aria-hidden="true"></i> Mais Notícias
          </h2>
          <a href="<?= BASE_URL ?>/noticias" class="text-sm font-bold text-primary hover:underline inline-flex items-center gap-1">
            Ver todas <i data-lucide="arrow-right" class="w-4 h-4" aria-hidden="true"></i>
          </a>
        </div>

        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6">
          <?php foreach ($outrasNoticias as $item): ?>
            <?php
              $itemCategoria = $temaMap[$item['tema'] ?? 'outros'] ?? ucfirst($item['tema'] ?? 'Outros');
              $itemDataRaw = $item['data_publicacao'] ?? $item['criado_em'] ?? '';
              $itemDataStr = !empty($itemDataRaw) ? date('d/m/Y', strtotime($itemDataRaw)) : '';
              $itemImagem = !empty($item['imagem_capa'])
                  ? (strpos($item['imagem_capa'], 'http') === 0 ? $item['imagem_capa'] : BASE_URL . '/' . ltrim($item['imagem_capa'], '/'))
                  : 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=600&h=400&fit=crop';
              $itemResumo = mb_strimwidth(strip_tags($item['conteudo']), 0, 100, '...');
            ?>
            <article class="bg-card rounded-2xl border border-border overflow-hidden hover:-translate-y-1 transition-all duration-300 flex flex-col group shadow-sm">
              <a href="<?= BASE_URL ?>/noticia?id=<?= $item['id'] ?>" class="block overflow-hidden h-40 bg-muted">
                <img src="<?= e($itemImagem) ?>" alt="<?= htmlspecialchars($item['titulo']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
              </a>
              <div class="p-5 flex flex-col flex-1">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-[10px] font-bold text-primary bg-primary/10 px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                    <?= htmlspecialchars($itemCategoria) ?>
                  </span>
                  <span class="text-[11px] text-muted-foreground flex items-center gap-1">
                    <i data-lucide="calendar" class="w-3 h-3" aria-hidden="true"></i> <?= $itemDataStr ?>
                  </span>
                </div>
                <h3 class="font-bold text-foreground text-sm mb-2 line-clamp-2 group-hover:text-primary transition-colors">
                  <a href="<?= BASE_URL ?>/noticia?id=<?= $item['id'] ?>">
                    <?= htmlspecialchars($item['titulo']) ?>
                  </a>
                </h3>
                <p class="text-xs text-muted-foreground line-clamp-2 mb-4 flex-1 leading-relaxed">
                  <?= e($itemResumo) ?>
                </p>
                <div class="pt-2 border-t border-border/50 mt-auto flex justify-end">
                  <a href="<?= BASE_URL ?>/noticia?id=<?= $item['id'] ?>" class="text-xs font-bold text-primary hover:underline inline-flex items-center gap-1" aria-label="Ver mais sobre <?= htmlspecialchars($item['titulo']) ?>">
                    Ver mais <i data-lucide="arrow-right" class="w-3.5 h-3.5" aria-hidden="true"></i>
                  </a>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>

  </div>
</main>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Copiar link da notícia com feedback visual
    const btnCopiar = document.getElementById('btn-copiar-link');
    const textoCopiar = document.getElementById('btn-copiar-texto');
    
    if (btnCopiar) {
      btnCopiar.addEventListener('click', async () => {
        try {
          await navigator.clipboard.writeText(window.location.href);
          if (textoCopiar) textoCopiar.textContent = 'Copiado!';
          btnCopiar.classList.add('bg-primary/20', 'text-primary');
          setTimeout(() => {
            if (textoCopiar) textoCopiar.textContent = 'Copiar Link';
            btnCopiar.classList.remove('bg-primary/20', 'text-primary');
          }, 2500);
        } catch (err) {
          // Fallback para navegadores antigos
          const input = document.createElement('input');
          input.value = window.location.href;
          document.body.appendChild(input);
          input.select();
          document.execCommand('copy');
          document.body.removeChild(input);
          if (textoCopiar) textoCopiar.textContent = 'Copiado!';
          setTimeout(() => {
            if (textoCopiar) textoCopiar.textContent = 'Copiar Link';
          }, 2500);
        }
      });
    }

    if (window.lucide) {
      lucide.createIcons();
    }
  });
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
