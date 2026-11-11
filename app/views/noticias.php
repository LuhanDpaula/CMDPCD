<?php 
require_once __DIR__ . '/partials/header.php'; 

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

// Fallback caso não haja nenhuma notícia no banco ainda
$listaNoticias = $noticias ?? [];
if (empty($listaNoticias)) {
    $listaNoticias = [
        [
            'id' => 1,
            'titulo' => 'CMDPCD Jahu realiza audiência pública sobre acessibilidade urbana',
            'tema' => 'acessibilidade',
            'conteudo' => 'Evento reuniu representantes da sociedade civil e do poder público para discutir melhorias na infraestrutura urbana e calçadas acessíveis.',
            'imagem_capa' => 'https://images.unsplash.com/photo-1573497620053-ea5300f94f21?w=800&h=400&fit=crop',
            'data_publicacao' => '2026-03-15 14:00:00',
            'autor_nome' => 'Comunicação CMDPCD'
        ],
        [
            'id' => 2,
            'titulo' => 'Parceria com APAE amplia atendimento a pessoas com deficiência intelectual',
            'tema' => 'inclusao',
            'conteudo' => 'Novo convênio firmado entre o conselho e a APAE visa expandir os serviços de reabilitação e inclusão social em Jahu.',
            'imagem_capa' => 'https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?w=800&h=400&fit=crop',
            'data_publicacao' => '2026-03-10 10:00:00',
            'autor_nome' => 'Comunicação CMDPCD'
        ],
        [
            'id' => 3,
            'titulo' => 'Campanha de conscientização sobre direitos das pessoas com deficiência',
            'tema' => 'direitos',
            'conteudo' => 'Ação educativa visa informar a população sobre os direitos garantidos por lei e o respeito às vagas exclusivas de estacionamento.',
            'imagem_capa' => 'https://images.unsplash.com/photo-1491438590914-bc09fcaaf77a?w=800&h=400&fit=crop',
            'data_publicacao' => '2026-02-28 09:30:00',
            'autor_nome' => 'Comunicação CMDPCD'
        ]
    ];
}
?>

  <main id="conteudo-principal" class="pt-16">
    <!-- Page Hero -->
    <section class="py-16 md:py-24 bg-card border-b border-border">
      <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl md:text-5xl font-black text-foreground mb-4 fade-in-up">Notícias</h1>
        <p class="text-lg md:text-xl text-muted-foreground max-w-2xl mx-auto fade-in-up">
          Fique por dentro das ações, resoluções e novidades do CMDPCD Jahu
        </p>
      </div>
    </section>

    <!-- Conteúdo e Listagem de Notícias -->
    <section class="py-16 bg-background">
      <div class="container px-4 max-w-6xl mx-auto">

        <!-- Barra de Busca e Filtro de Categorias -->
        <div class="mb-10 space-y-4">
          <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            
            <!-- Campo de busca -->
            <div class="relative w-full sm:w-80">
              <input type="text" 
                     id="filtro-busca" 
                     placeholder="Buscar por notícia..." 
                     aria-label="Buscar notícia por título ou palavra-chave"
                     class="w-full pl-10 pr-4 py-2.5 bg-card border border-border rounded-xl text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
              <i data-lucide="search" class="w-4 h-4 text-muted-foreground absolute left-3.5 top-1/2 -translate-y-1/2" aria-hidden="true"></i>
            </div>

            <!-- Contador de notícias -->
            <div class="text-xs text-muted-foreground self-start sm:self-auto">
              Total: <span id="contador-noticias" class="font-bold text-foreground"><?= count($listaNoticias) ?></span> notícias encontradas
            </div>
          </div>

          <!-- Filtros de Tema (Chips) -->
          <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none" id="filtros-tema" role="tablist" aria-label="Filtro por categorias">
            <button type="button" 
                    data-tema="todos" 
                    class="btn-filtro-tema active px-4 py-1.5 rounded-full text-xs font-bold transition-all bg-primary text-primary-foreground shadow-sm cursor-pointer whitespace-nowrap">
              Todas
            </button>
            <?php foreach ($temaMap as $slug => $label): ?>
              <button type="button" 
                      data-tema="<?= $slug ?>" 
                      class="btn-filtro-tema px-4 py-1.5 rounded-full text-xs font-bold transition-all bg-muted hover:bg-muted/80 text-muted-foreground hover:text-foreground cursor-pointer whitespace-nowrap">
                <?= $label ?>
              </button>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Mensagem de Nenhuma Notícia Encontrada -->
        <div id="no-results" class="text-center text-muted-foreground py-16 hidden bg-card rounded-2xl border border-border">
          <div class="w-12 h-12 rounded-2xl bg-muted/50 flex items-center justify-center text-muted-foreground mx-auto mb-3">
            <i data-lucide="newspaper" class="w-6 h-6" aria-hidden="true"></i>
          </div>
          <p class="font-bold text-foreground">Nenhuma notícia encontrada com os filtros selecionados.</p>
          <p class="text-sm mt-1">Tente buscar por outro termo ou selecione a categoria "Todas".</p>
        </div>

        <!-- Grid de Notícias -->
        <div id="noticias-grid" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <?php foreach ($listaNoticias as $n): ?>
            <?php
              $categoria = $temaMap[$n['tema'] ?? 'outros'] ?? ucfirst($n['tema'] ?? 'Outros');
              $dataRaw = $n['data_publicacao'] ?? $n['criado_em'] ?? '';
              $dataStr = !empty($dataRaw) ? date('d/m/Y', strtotime($dataRaw)) : '';
              $resumo = mb_strimwidth(strip_tags($n['conteudo']), 0, 140, '...');
              $imagem = !empty($n['imagem_capa'])
                  ? (strpos($n['imagem_capa'], 'http') === 0 ? $n['imagem_capa'] : BASE_URL . '/' . ltrim($n['imagem_capa'], '/'))
                  : 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop';
              $noticiaUrl = BASE_URL . '/noticia?id=' . $n['id'];
            ?>
            <article class="noticia-card fade-in-up bg-card rounded-2xl border border-border overflow-hidden hover:-translate-y-1 transition-all duration-300 flex flex-col group shadow-sm"
                     data-tema="<?= e(strtolower($n['tema'] ?? 'outros')) ?>"
                     data-titulo="<?= e(mb_strtolower($n['titulo'])) ?>"
                     data-conteudo="<?= e(mb_strtolower(strip_tags($n['conteudo']))) ?>">
              
              <!-- Imagem da notícia com link -->
              <a href="<?= $noticiaUrl ?>" class="block overflow-hidden h-48 bg-muted relative" aria-label="<?= htmlspecialchars($n['titulo']) ?>">
                <img src="<?= e($imagem) ?>" 
                     alt="<?= htmlspecialchars($n['titulo']) ?>" 
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" 
                     loading="lazy">
              </a>

              <!-- Conteúdo do Card -->
              <div class="p-6 flex flex-col flex-1">
                <div class="flex items-center justify-between mb-3">
                  <span class="inline-block text-xs font-bold text-primary bg-primary/10 px-3 py-1 rounded-full uppercase tracking-wider">
                    <?= htmlspecialchars($categoria) ?>
                  </span>
                  <?php if (!empty($dataStr)): ?>
                    <span class="text-xs text-muted-foreground flex items-center gap-1">
                      <i data-lucide="calendar" class="w-3.5 h-3.5" aria-hidden="true"></i> <?= $dataStr ?>
                    </span>
                  <?php endif; ?>
                </div>

                <!-- Título clicável -->
                <h2 class="font-bold text-foreground mb-2 text-lg line-clamp-2 group-hover:text-primary transition-colors leading-snug">
                  <a href="<?= $noticiaUrl ?>">
                    <?= htmlspecialchars($n['titulo']) ?>
                  </a>
                </h2>

                <!-- Resumo -->
                <p class="text-sm text-muted-foreground line-clamp-2 mb-4 flex-1 leading-relaxed">
                  <?= e($resumo) ?>
                </p>

                <!-- Rodapé do Card com Link Ver mais -->
                <div class="flex items-center justify-between mt-auto pt-3 border-t border-border/50">
                  <span class="text-xs text-muted-foreground">
                    <?= htmlspecialchars($n['autor_nome'] ?? 'CMDPCD') ?>
                  </span>
                  <a href="<?= $noticiaUrl ?>" 
                     class="text-sm text-primary font-bold hover:underline inline-flex items-center gap-1.5" 
                     aria-label="Ver mais sobre: <?= htmlspecialchars($n['titulo']) ?>">
                    Ver mais <i data-lucide="arrow-right" class="w-3.5 h-3.5" aria-hidden="true"></i>
                  </a>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>

      </div>
    </section>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const inputBusca = document.getElementById('filtro-busca');
      const botoesTema = document.querySelectorAll('.btn-filtro-tema');
      const cards = document.querySelectorAll('.noticia-card');
      const noResults = document.getElementById('no-results');
      const grid = document.getElementById('noticias-grid');
      const contador = document.getElementById('contador-noticias');

      let temaAtual = 'todos';
      let termoBusca = '';

      function filtrarNoticias() {
        let visiveis = 0;

        cards.forEach(card => {
          const cardTema = card.getAttribute('data-tema') || '';
          const cardTitulo = card.getAttribute('data-titulo') || '';
          const cardConteudo = card.getAttribute('data-conteudo') || '';

          const matchTema = (temaAtual === 'todos' || cardTema === temaAtual);
          const matchBusca = (!termoBusca || cardTitulo.includes(termoBusca) || cardConteudo.includes(termoBusca));

          if (matchTema && matchBusca) {
            card.classList.remove('hidden');
            visiveis++;
          } else {
            card.classList.add('hidden');
          }
        });

        if (contador) {
          contador.textContent = visiveis;
        }

        if (visiveis === 0) {
          noResults.classList.remove('hidden');
          grid.classList.add('hidden');
        } else {
          noResults.classList.add('hidden');
          grid.classList.remove('hidden');
        }
      }

      // Evento de busca por texto
      if (inputBusca) {
        inputBusca.addEventListener('input', (e) => {
          termoBusca = e.target.value.toLowerCase().trim();
          filtrarNoticias();
        });
      }

      // Evento de clique nos chips de categoria
      botoesTema.forEach(btn => {
        btn.addEventListener('click', () => {
          botoesTema.forEach(b => {
            b.classList.remove('active', 'bg-primary', 'text-primary-foreground', 'shadow-sm');
            b.classList.add('bg-muted', 'text-muted-foreground');
          });

          btn.classList.add('active', 'bg-primary', 'text-primary-foreground', 'shadow-sm');
          btn.classList.remove('bg-muted', 'text-muted-foreground');

          temaAtual = btn.getAttribute('data-tema');
          filtrarNoticias();
        });
      });

      if (window.lucide) {
        lucide.createIcons();
      }
    });
  </script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
