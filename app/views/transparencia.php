<?php 
require_once __DIR__ . '/partials/header.php'; 

$tipoMap = [
    'ata'       => 'Ata',
    'resolucao' => 'Resolução',
    'edital'    => 'Edital',
    'oficio'    => 'Ofício',
    'relatorio' => 'Relatório',
    'outros'    => 'Outro'
];

$tipoPluralMap = [
    'ata'       => 'Atas',
    'resolucao' => 'Resoluções',
    'edital'    => 'Editais',
    'oficio'    => 'Ofícios',
    'relatorio' => 'Relatórios',
    'outros'    => 'Outros'
];

$iconMap = [
    'ata'       => 'file-text',
    'resolucao' => 'book-open',
    'edital'    => 'clipboard-list',
    'oficio'    => 'mail',
    'relatorio' => 'folder-open',
    'outros'    => 'file-check'
];

// Fallback estático caso o banco de dados não contenha documentos ainda
$listaDocs = $documentos ?? [];
if (empty($listaDocs)) {
    $listaDocs = [
        [
            'id' => 1,
            'titulo' => 'Ata da Reunião Ordinária - Março 2025',
            'tipo' => 'ata',
            'data_publicacao' => '2025-03-15',
            'descricao' => 'Deliberações sobre acessibilidade nas calçadas da região central e aprovação de novas diretrizes.',
            'caminho_arquivo' => 'uploads/ata_reuniao_ordinaria_marco_2025.pdf'
        ],
        [
            'id' => 2,
            'titulo' => 'Ata da Reunião Extraordinária - Fevereiro 2025',
            'tipo' => 'ata',
            'data_publicacao' => '2025-02-20',
            'descricao' => 'Discussão emergencial sobre o andamento das vistorias nos prédios públicos municipais.',
            'caminho_arquivo' => 'uploads/ata_reuniao_extraordinaria_fevereiro_2025.pdf'
        ],
        [
            'id' => 3,
            'titulo' => 'Resolução nº 05/2025 - Normas de Acessibilidade',
            'tipo' => 'resolucao',
            'data_publicacao' => '2025-03-01',
            'descricao' => 'Estabelece as normas e especificações técnicas de acessibilidade para estabelecimentos comerciais de Jahu.',
            'caminho_arquivo' => 'uploads/resolucao_05_2025_acessibilidade.pdf'
        ],
        [
            'id' => 4,
            'titulo' => 'Resolução nº 04/2025 - Programa de Inclusão',
            'tipo' => 'resolucao',
            'data_publicacao' => '2025-02-15',
            'descricao' => 'Regulamenta o selo municipal de empresa inclusiva para contratação de pessoas com deficiência.',
            'caminho_arquivo' => 'uploads/resolucao_04_2025_inclusao.pdf'
        ],
        [
            'id' => 5,
            'titulo' => 'Regimento Interno do CMPCD',
            'tipo' => 'outros',
            'data_publicacao' => '2024-06-01',
            'descricao' => 'Regimento interno completo com as competências, organização e atribuições do CMPCD de Jahu.',
            'caminho_arquivo' => 'uploads/regimento_interno_cmpcd.pdf'
        ],
        [
            'id' => 6,
            'titulo' => 'Relatório Anual de Atividades 2024',
            'tipo' => 'relatorio',
            'data_publicacao' => '2025-01-30',
            'descricao' => 'Relatório consolidando todas as ações, fiscalizações, pareceres e conquistas alcançadas no ano de 2024.',
            'caminho_arquivo' => 'uploads/relatorio_anual_atividades_2024.pdf'
        ]
    ];
}

// Extrai anos únicos disponíveis para o filtro
$anosDisponiveis = [];
foreach ($listaDocs as $d) {
    if (!empty($d['data_publicacao'])) {
        $ano = date('Y', strtotime($d['data_publicacao']));
        if (!in_array($ano, $anosDisponiveis)) {
            $anosDisponiveis[] = $ano;
        }
    }
}
rsort($anosDisponiveis);
?>

  <main id="conteudo-principal" class="pt-16">
    <!-- Hero Transparência -->
    <section class="py-16 md:py-24 bg-card border-b border-border">
      <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl md:text-5xl font-black text-foreground mb-4 fade-in-up">Transparência</h1>
        <p class="text-lg md:text-xl text-muted-foreground max-w-2xl mx-auto fade-in-up">
          Acesso público a atas, resoluções, editais, relatórios e prestação de contas do conselho (Lei nº 12.527/2011)
        </p>
      </div>
    </section>

    <!-- Documentos e Publicações -->
    <section class="py-16 bg-background">
      <div class="container mx-auto px-4 max-w-5xl">
        
        <div class="flex items-center justify-between mb-8 flex-wrap gap-4">
          <div>
            <h2 class="text-2xl font-extrabold text-foreground flex items-center gap-2">
              <i data-lucide="file-check" class="w-6 h-6 text-primary" aria-hidden="true"></i> Documentos e Publicações
            </h2>
            <p class="text-sm text-muted-foreground mt-1">Consulte e baixe os arquivos oficiais digitalizados.</p>
          </div>
          
          <div class="text-xs text-muted-foreground bg-muted/50 px-3.5 py-1.5 rounded-full border border-border">
            Total: <span id="contador-docs" class="font-bold text-foreground"><?= count($listaDocs) ?></span> documentos
          </div>
        </div>

        <!-- Barra de Busca e Filtros -->
        <div class="mb-8 space-y-4 bg-card p-5 rounded-2xl border border-border shadow-sm">
          
          <!-- Linha 1: Input de busca + Select de Ano -->
          <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1">
              <input type="text" 
                     id="filtro-doc-busca" 
                     placeholder="Buscar por título, assunto ou palavra-chave..." 
                     aria-label="Buscar documento por título ou descrição"
                     class="w-full pl-10 pr-4 py-2.5 bg-background border border-border rounded-xl text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
              <i data-lucide="search" class="w-4 h-4 text-muted-foreground absolute left-3.5 top-1/2 -translate-y-1/2" aria-hidden="true"></i>
            </div>

            <div class="flex items-center gap-2 shrink-0">
              <label for="filtro-doc-ano" class="text-xs font-bold text-muted-foreground uppercase tracking-wider shrink-0">Ano:</label>
              <select id="filtro-doc-ano" 
                      aria-label="Filtrar por ano de publicação"
                      class="bg-background border border-border rounded-xl px-4 py-2.5 text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all cursor-pointer">
                <option value="todos">Todos os Anos</option>
                <?php foreach ($anosDisponiveis as $ano): ?>
                  <option value="<?= $ano ?>"><?= $ano ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <!-- Linha 2: Chips de Tipo de Documento -->
          <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none pt-2 border-t border-border/50" id="filtros-doc-tipo" role="tablist" aria-label="Filtrar por tipo de documento">
            <button type="button" 
                    data-tipo="todos" 
                    class="btn-filtro-doc active px-3.5 py-1.5 rounded-full text-xs font-bold transition-all bg-primary text-primary-foreground shadow-sm cursor-pointer whitespace-nowrap">
              Todos
            </button>
            <?php foreach ($tipoMap as $slug => $label): ?>
              <button type="button" 
                      data-tipo="<?= $slug ?>" 
                      class="btn-filtro-doc px-3.5 py-1.5 rounded-full text-xs font-bold transition-all bg-muted hover:bg-muted/80 text-muted-foreground hover:text-foreground cursor-pointer whitespace-nowrap">
                <?= $tipoPluralMap[$slug] ?? $label ?>
              </button>
            <?php endforeach; ?>
          </div>

        </div>

        <!-- Mensagem de Nenhum Documento Encontrado -->
        <div id="no-docs-results" class="text-center text-muted-foreground py-16 hidden bg-card rounded-2xl border border-border">
          <div class="w-12 h-12 rounded-2xl bg-muted/50 flex items-center justify-center text-muted-foreground mx-auto mb-3">
            <i data-lucide="file-question" class="w-6 h-6" aria-hidden="true"></i>
          </div>
          <p class="font-bold text-foreground">Nenhum documento encontrado com os filtros selecionados.</p>
          <p class="text-sm mt-1">Tente buscar por outro termo ou selecione a categoria "Todos".</p>
        </div>

        <!-- Lista de Documentos -->
        <div id="documentos-lista" class="space-y-4">
          <?php foreach ($listaDocs as $doc): ?>
            <?php
              $tipo = strtolower($doc['tipo'] ?? 'outros');
              $tipoLabel = $tipoMap[$tipo] ?? ucfirst($tipo);
              $icon = $iconMap[$tipo] ?? 'file-text';
              $dataStr = !empty($doc['data_publicacao']) ? date('d/m/Y', strtotime($doc['data_publicacao'])) : '';
              $anoDoc = !empty($doc['data_publicacao']) ? date('Y', strtotime($doc['data_publicacao'])) : '';
              $fileUrl = !empty($doc['caminho_arquivo']) ? (strpos($doc['caminho_arquivo'], 'http') === 0 ? $doc['caminho_arquivo'] : BASE_URL . '/' . ltrim($doc['caminho_arquivo'], '/')) : '#';
            ?>
            <div class="doc-card flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-card rounded-2xl p-5 border border-border hover:border-primary/40 hover:-translate-y-0.5 transition-all duration-300 fade-in-up group" 
                 style="box-shadow: var(--card-shadow)"
                 data-tipo="<?= e($tipo) ?>"
                 data-ano="<?= e($anoDoc) ?>"
                 data-titulo="<?= e(mb_strtolower($doc['titulo'])) ?>"
                 data-desc="<?= e(mb_strtolower($doc['descricao'] ?? '')) ?>">
              
              <div class="flex items-start gap-4 flex-1 min-w-0">
                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0 text-primary group-hover:scale-110 transition-transform">
                  <i data-lucide="<?= $icon ?>" class="w-6 h-6" aria-hidden="true"></i>
                </div>
                
                <div class="flex-1 min-w-0 space-y-1">
                  <div class="flex items-center gap-2 flex-wrap">
                    <span class="inline-block text-[11px] font-bold text-primary bg-primary/10 px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                      <?= htmlspecialchars($tipoLabel) ?>
                    </span>
                    <?php if (!empty($dataStr)): ?>
                      <span class="text-xs text-muted-foreground flex items-center gap-1">
                        <i data-lucide="calendar" class="w-3.5 h-3.5" aria-hidden="true"></i> Publicado em <?= $dataStr ?>
                      </span>
                    <?php endif; ?>
                  </div>
                  
                  <h3 class="font-bold text-foreground text-sm md:text-base leading-snug group-hover:text-primary transition-colors" title="<?= htmlspecialchars($doc['titulo']) ?>">
                    <?= htmlspecialchars($doc['titulo']) ?>
                  </h3>
                  
                  <?php if (!empty($doc['descricao'])): ?>
                    <p class="text-xs md:text-sm text-muted-foreground line-clamp-2 leading-relaxed">
                      <?= htmlspecialchars($doc['descricao']) ?>
                    </p>
                  <?php endif; ?>
                </div>
              </div>

              <div class="w-full sm:w-auto flex items-center justify-end shrink-0 pt-3 sm:pt-0 border-t sm:border-t-0 border-border/50">
                <a href="<?= e($fileUrl) ?>" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-primary/10 hover:bg-primary text-primary hover:text-primary-foreground text-xs font-bold transition-all shadow-sm cursor-pointer" 
                   aria-label="Acessar documento: <?= htmlspecialchars($doc['titulo']) ?>">
                  <span>Acessar Documento</span>
                  <i data-lucide="download" class="w-4 h-4" aria-hidden="true"></i>
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

      </div>
    </section>

    <!-- Conselheiros -->
    <section class="py-16 bg-card border-t border-border">
      <div class="container mx-auto px-4 max-w-5xl">
        <div class="mb-8 fade-in-up">
          <h2 class="text-2xl font-extrabold text-foreground flex items-center gap-2">
            <i data-lucide="users" class="w-6 h-6 text-primary" aria-hidden="true"></i> Conselheiros em Exercício
          </h2>
          <p class="text-sm text-muted-foreground mt-1">Composição plenária atual paritária entre Sociedade Civil e Poder Público.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <?php if (!empty($membrosAtuais)): ?>
            <?php foreach ($membrosAtuais as $membro): ?>
              <?php
                $funcoesMap = [
                  'presidente'      => 'Presidente',
                  'vice-presidente' => 'Vice-Presidente',
                  'secretario'      => 'Secretário(a)',
                  'titular'         => 'Conselheiro(a) Titular',
                  'suplente'        => 'Conselheiro(a) Suplente'
                ];
                $funcao = $funcoesMap[$membro['funcao']] ?? ucfirst($membro['funcao']);
                $periodo = date('Y', strtotime($membro['data_inicio']));
                if ($membro['data_fim']) {
                  $periodo .= '-' . date('Y', strtotime($membro['data_fim']));
                } else {
                  $periodo .= '-Atual';
                }
              ?>
              <div class="bg-background rounded-2xl p-5 border border-border fade-in-up hover:border-primary/30 transition-all" style="box-shadow: var(--card-shadow)">
                <h4 class="font-bold text-foreground text-base"><?= htmlspecialchars($membro['nome_completo']) ?></h4>
                <p class="text-sm text-muted-foreground mt-1"><?= $membro['entidade_representada'] ? htmlspecialchars($membro['entidade_representada']) : 'Poder Público' ?></p>
                <div class="mt-3 pt-2 border-t border-border/50 flex items-center justify-between text-xs">
                  <span class="text-primary font-semibold"><?= e($funcao) ?></span>
                  <span class="text-muted-foreground">Mandato: <?= e($periodo) ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <!-- Fallback estático de conselheiros -->
            <div class="bg-background rounded-2xl p-5 border border-border fade-in-up" style="box-shadow: var(--card-shadow)">
              <h4 class="font-bold text-foreground text-base">Dra. Helena Souza</h4>
              <p class="text-sm text-muted-foreground mt-1">Sociedade Civil - Saúde e Reabilitação</p>
              <div class="mt-3 pt-2 border-t border-border/50 flex items-center justify-between text-xs">
                <span class="text-primary font-semibold">Presidente</span>
                <span class="text-muted-foreground">Mandato: 2025-Atual</span>
              </div>
            </div>
            <div class="bg-background rounded-2xl p-5 border border-border fade-in-up" style="box-shadow: var(--card-shadow)">
              <h4 class="font-bold text-foreground text-base">Marcos Silva Junior</h4>
              <p class="text-sm text-muted-foreground mt-1">Poder Público - Secretaria de Trânsito</p>
              <div class="mt-3 pt-2 border-t border-border/50 flex items-center justify-between text-xs">
                <span class="text-primary font-semibold">Vice-Presidente</span>
                <span class="text-muted-foreground">Mandato: 2025-Atual</span>
              </div>
            </div>
            <div class="bg-background rounded-2xl p-5 border border-border fade-in-up" style="box-shadow: var(--card-shadow)">
              <h4 class="font-bold text-foreground text-base">Fernanda Alves Lima</h4>
              <p class="text-sm text-muted-foreground mt-1">Sociedade Civil - APAE Jaú</p>
              <div class="mt-3 pt-2 border-t border-border/50 flex items-center justify-between text-xs">
                <span class="text-primary font-semibold">Secretária</span>
                <span class="text-muted-foreground">Mandato: 2025-Atual</span>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Informações Institucionais e Endereço Oficial -->
    <section class="py-16 bg-background">
      <div class="container mx-auto px-4 max-w-3xl">
        <h2 class="text-2xl font-extrabold text-foreground mb-6 text-center fade-in-up">Sede do Conselho</h2>
        <div class="bg-card rounded-2xl p-8 border border-border space-y-4 fade-in-up shadow-sm" style="box-shadow: var(--card-shadow)">
          <div class="flex items-center gap-3">
            <i data-lucide="map-pin" class="w-5 h-5 text-primary shrink-0" aria-hidden="true"></i>
            <span class="text-foreground text-sm md:text-base">Rua Aristides Lobo Sobrinho, nº 174, Chácara Braz Miraglia - Jahu/SP, CEP 17210-580</span>
          </div>
          <div class="flex items-center gap-3">
            <i data-lucide="phone" class="w-5 h-5 text-primary shrink-0" aria-hidden="true"></i>
            <span class="text-foreground text-sm md:text-base">(14) 3624-5077</span>
          </div>
          <div class="flex items-center gap-3">
            <i data-lucide="mail" class="w-5 h-5 text-primary shrink-0" aria-hidden="true"></i>
            <span class="text-foreground text-sm md:text-base">cmpcdjau@gmail.com</span>
          </div>
        </div>
      </div>
    </section>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const inputBusca = document.getElementById('filtro-doc-busca');
      const selectAno = document.getElementById('filtro-doc-ano');
      const botoesTipo = document.querySelectorAll('.btn-filtro-doc');
      const cards = document.querySelectorAll('.doc-card');
      const noResults = document.getElementById('no-docs-results');
      const lista = document.getElementById('documentos-lista');
      const contador = document.getElementById('contador-docs');

      let tipoAtual = 'todos';
      let anoAtual = 'todos';
      let termoBusca = '';

      function filtrarDocumentos() {
        let visiveis = 0;

        cards.forEach(card => {
          const cardTipo = card.getAttribute('data-tipo') || '';
          const cardAno = card.getAttribute('data-ano') || '';
          const cardTitulo = card.getAttribute('data-titulo') || '';
          const cardDesc = card.getAttribute('data-desc') || '';

          const matchTipo = (tipoAtual === 'todos' || cardTipo === tipoAtual);
          const matchAno = (anoAtual === 'todos' || cardAno === anoAtual);
          const matchBusca = (!termoBusca || cardTitulo.includes(termoBusca) || cardDesc.includes(termoBusca));

          if (matchTipo && matchAno && matchBusca) {
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
          lista.classList.add('hidden');
        } else {
          noResults.classList.add('hidden');
          lista.classList.remove('hidden');
        }
      }

      // Evento de busca por texto
      if (inputBusca) {
        inputBusca.addEventListener('input', (e) => {
          termoBusca = e.target.value.toLowerCase().trim();
          filtrarDocumentos();
        });
      }

      // Evento de filtro por ano
      if (selectAno) {
        selectAno.addEventListener('change', (e) => {
          anoAtual = e.target.value;
          filtrarDocumentos();
        });
      }

      // Evento de clique nos chips de categoria de documento
      botoesTipo.forEach(btn => {
        btn.addEventListener('click', () => {
          botoesTipo.forEach(b => {
            b.classList.remove('active', 'bg-primary', 'text-primary-foreground', 'shadow-sm');
            b.classList.add('bg-muted', 'text-muted-foreground');
          });

          btn.classList.add('active', 'bg-primary', 'text-primary-foreground', 'shadow-sm');
          btn.classList.remove('bg-muted', 'text-muted-foreground');

          tipoAtual = btn.getAttribute('data-tipo');
          filtrarDocumentos();
        });
      });

      if (window.lucide) {
        lucide.createIcons();
      }
    });
  </script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
