<?php 
require_once __DIR__ . '/../partials/admin_header.php'; 

$totalPcds = (int)($stats['total_pcds'] ?? 0);
$totalBpc = (int)($stats['total_bpc'] ?? 0);
$totalTec = (int)($stats['total_tecnologia'] ?? 0);
$totalEnt = (int)($stats['total_entidades'] ?? 0);

$pctBpc = $totalPcds > 0 ? round(($totalBpc / $totalPcds) * 100, 1) : 0;
$pctTec = $totalPcds > 0 ? round(($totalTec / $totalPcds) * 100, 1) : 0;
$pctEnt = $totalPcds > 0 ? round(($totalEnt / $totalPcds) * 100, 1) : 0;

// Prepara arrays para os gráficos
$defLabels = array_column($stats['deficiencias'], 'nome');
$defData = array_map('intval', array_column($stats['deficiencias'], 'total'));

$ageLabels = array_keys($stats['idades']);
$ageData = array_map('intval', array_values($stats['idades']));

$schoolLabels = array_keys($stats['escolaridade']);
$schoolData = array_map('intval', array_values($stats['escolaridade']));

$bairroLabels = array_keys($stats['bairros']);
$bairroData = array_map('intval', array_values($stats['bairros']));
?>

<!-- Estilos específicos para Impressão em Alta Resolução (A4/PDF) -->
<style>
  @media print {
    /* Oculta elementos irrelevantes na impressão */
    header,
    section.py-12.bg-card.border-b.border-border,
    .no-print,
    footer,
    .a11y-fab,
    .a11y-panel,
    .chat-widget,
    #chat-bubble {
      display: none !important;
    }

    body {
      background: #ffffff !important;
      color: #0f172a !important;
      font-size: 10.5pt !important;
      margin: 0 !important;
      padding: 0 !important;
    }

    main {
      padding-top: 0 !important;
      min-height: auto !important;
    }

    section.py-12.bg-background {
      padding: 0 !important;
    }

    .container {
      max-width: 100% !important;
      padding: 0 !important;
      margin: 0 !important;
    }

    /* Exibe cabeçalho e rodapé oficiais na folha de impressão */
    .print-only {
      display: block !important;
    }

    .print-avoid-break {
      page-break-inside: avoid !important;
      break-inside: avoid !important;
    }

    .bg-card {
      background-color: #ffffff !important;
      border: 1px solid #cbd5e1 !important;
      box-shadow: none !important;
    }

    .rounded-2xl {
      border-radius: 0.75rem !important;
    }

    /* Força a renderização das cores de fundo e bordas */
    * {
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }
  }
</style>

<!-- Cabeçalho Oficial exclusivo para Impressão / PDF -->
<div class="print-only hidden mb-6 pb-4 border-b-2 border-slate-800">
  <div class="flex items-center justify-between gap-4 mb-3">
    <div class="w-14 h-14 rounded-xl bg-slate-100 flex items-center justify-center text-slate-800 border border-slate-300 shrink-0">
      <i data-lucide="building-2" class="w-8 h-8"></i>
    </div>
    <div class="flex-1 text-center">
      <h1 class="text-sm font-black tracking-wider uppercase text-slate-900">Prefeitura Municipal de Jahu — Estado de São Paulo</h1>
      <h2 class="text-xs font-extrabold text-slate-800 mt-0.5">Conselho Municipal dos Direitos da Pessoa com Deficiência (CMDPCD)</h2>
      <p class="text-[11px] text-slate-500">Secretaria de Assistência e Desenvolvimento Social • Sistema Integrado de Inteligência Pública</p>
    </div>
    <div class="w-14 h-14 rounded-xl bg-slate-100 flex items-center justify-center text-slate-800 border border-slate-300 shrink-0">
      <i data-lucide="accessibility" class="w-8 h-8"></i>
    </div>
  </div>
  
  <div class="bg-slate-100 py-2 px-3 rounded-lg border border-slate-300 flex items-center justify-between text-xs font-bold text-slate-800">
    <span>RELATÓRIO EXECUTIVO DEMOGRÁFICO DE PESSOAS COM DEFICIÊNCIA</span>
    <span>Emissão: <?= date('d/m/Y \à\s H:i') ?></span>
    <span>Emitido por: <?= htmlspecialchars($_SESSION['user_name'] ?? 'Administrador') ?></span>
  </div>
</div>

<!-- Barra de Título e Ações Superiores (Tela) -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-border pb-6 mb-8 gap-4 no-print">
  <div>
    <h2 class="text-2xl font-black text-foreground flex items-center gap-2">
      <i data-lucide="pie-chart" class="w-6 h-6 text-primary"></i> Estatísticas do Município
    </h2>
    <p class="text-sm text-muted-foreground mt-0.5">Dados demográficos consolidados e inteligência sobre as pessoas com deficiência cadastradas.</p>
  </div>
  
  <div class="flex items-center gap-3 flex-wrap">
    <!-- Botão Exportar CSV -->
    <button type="button" 
            id="btn-exportar-csv" 
            onclick="exportarRelatorioCSV()"
            class="px-4 py-2.5 rounded-xl border border-border bg-card hover:bg-muted text-foreground text-xs font-bold inline-flex items-center gap-2 transition-colors shadow-sm cursor-pointer" 
            title="Exportar dados agregados em planilha CSV (compatível com Excel)">
      <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600"></i>
      <span>Exportar CSV</span>
    </button>

    <!-- Botão Imprimir / Salvar PDF -->
    <button type="button" 
            onclick="window.print()" 
            class="px-4 py-2.5 rounded-xl bg-primary text-primary-foreground text-xs font-bold inline-flex items-center gap-2 hover:bg-primary/90 transition-colors shadow-sm cursor-pointer" 
            title="Imprimir Relatório Executivo Oficial ou Salvar em PDF">
      <i data-lucide="printer" class="w-4 h-4"></i>
      <span>Imprimir Relatório</span>
    </button>
  </div>
</div>

<!-- Indicadores Executivos em Destaque (Cards) -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-10 print-avoid-break">
  <!-- Total de PCDs -->
  <div class="rounded-2xl p-5 sm:p-6 text-center shadow-md bg-card border border-border" style="background: var(--hero-gradient)">
    <span class="text-3xl md:text-4xl font-black text-primary-foreground block">
      <?= number_format($totalPcds, 0, ',', '.') ?>
    </span>
    <p class="text-xs sm:text-sm text-primary-foreground/90 font-bold mt-1">Total de PCDs Deferidos</p>
    <span class="text-[10px] text-primary-foreground/70 block mt-0.5">Base municipal ativa</span>
  </div>

  <!-- BPC/LOAS -->
  <div class="rounded-2xl p-5 sm:p-6 text-center shadow-md bg-card border border-border" style="background: var(--hero-gradient)">
    <span class="text-3xl md:text-4xl font-black text-primary-foreground block">
      <?= number_format($totalBpc, 0, ',', '.') ?>
    </span>
    <p class="text-xs sm:text-sm text-primary-foreground/90 font-bold mt-1">Recebem BPC/LOAS</p>
    <span class="text-[10px] text-primary-foreground/70 block mt-0.5"><?= $pctBpc ?>% dos cadastrados</span>
  </div>

  <!-- Tecnologia Assistiva -->
  <div class="rounded-2xl p-5 sm:p-6 text-center shadow-md bg-card border border-border" style="background: var(--hero-gradient)">
    <span class="text-3xl md:text-4xl font-black text-primary-foreground block">
      <?= number_format($totalTec, 0, ',', '.') ?>
    </span>
    <p class="text-xs sm:text-sm text-primary-foreground/90 font-bold mt-1">Usam Tec. Assistiva</p>
    <span class="text-[10px] text-primary-foreground/70 block mt-0.5"><?= $pctTec ?>% dos cadastrados</span>
  </div>

  <!-- Vinculados a Entidades -->
  <div class="rounded-2xl p-5 sm:p-6 text-center shadow-md bg-card border border-border" style="background: var(--hero-gradient)">
    <span class="text-3xl md:text-4xl font-black text-primary-foreground block">
      <?= number_format($totalEnt, 0, ',', '.') ?>
    </span>
    <p class="text-xs sm:text-sm text-primary-foreground/90 font-bold mt-1">Vinculados a Entidades</p>
    <span class="text-[10px] text-primary-foreground/70 block mt-0.5"><?= $pctEnt ?>% dos cadastrados</span>
  </div>
</div>

<!-- Gráficos Principais (Chart.js) -->
<div class="grid lg:grid-cols-2 gap-6 sm:gap-8 mb-10 print-avoid-break">
  <!-- Gráfico 1: Tipo de Deficiência -->
  <div class="bg-card rounded-2xl p-6 border border-border shadow-sm print-avoid-break">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-base font-bold text-foreground flex items-center gap-2">
        <i data-lucide="activity" class="w-4 h-4 text-primary"></i> Tipo de Deficiência
      </h3>
      <span class="text-xs text-muted-foreground">Distribuição</span>
    </div>
    <div style="position: relative; height:280px; width:100%">
      <canvas id="chartTipo"></canvas>
    </div>
  </div>

  <!-- Gráfico 2: Faixa Etária -->
  <div class="bg-card rounded-2xl p-6 border border-border shadow-sm print-avoid-break">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-base font-bold text-foreground flex items-center gap-2">
        <i data-lucide="users" class="w-4 h-4 text-primary"></i> Faixa Etária
      </h3>
      <span class="text-xs text-muted-foreground">Em anos</span>
    </div>
    <div style="position: relative; height:280px; width:100%">
      <canvas id="chartIdade"></canvas>
    </div>
  </div>

  <!-- Gráfico 3: Escolaridade -->
  <div class="bg-card rounded-2xl p-6 border border-border shadow-sm print-avoid-break">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-base font-bold text-foreground flex items-center gap-2">
        <i data-lucide="graduation-cap" class="w-4 h-4 text-primary"></i> Escolaridade
      </h3>
      <span class="text-xs text-muted-foreground">Grau de instrução</span>
    </div>
    <div style="position: relative; height:280px; width:100%">
      <canvas id="chartEscolaridade"></canvas>
    </div>
  </div>

  <!-- Gráfico 4: Por Bairro/Região -->
  <div class="bg-card rounded-2xl p-6 border border-border shadow-sm print-avoid-break">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-base font-bold text-foreground flex items-center gap-2">
        <i data-lucide="map-pin" class="w-4 h-4 text-primary"></i> Por Bairro / Região
      </h3>
      <span class="text-xs text-muted-foreground">Maiores concentrações</span>
    </div>
    <div style="position: relative; height:280px; width:100%">
      <canvas id="chartBairro"></canvas>
    </div>
  </div>
</div>

<!-- Detalhamento Tabular Executivo (Ideal para análise em plenária e relatórios físicos) -->
<div class="mb-10 print-avoid-break">
  <div class="flex items-center justify-between border-b border-border pb-3 mb-6">
    <h3 class="text-lg font-bold text-foreground flex items-center gap-2">
      <i data-lucide="table" class="w-5 h-5 text-primary"></i> Detalhamento Numérico dos Indicadores
    </h3>
    <span class="text-xs text-muted-foreground">Percentuais consolidados</span>
  </div>

  <div class="grid md:grid-cols-2 gap-6">
    <!-- Tabela 1: Deficiências -->
    <div class="bg-card rounded-2xl p-5 border border-border shadow-sm print-avoid-break">
      <h4 class="text-sm font-bold text-foreground mb-3 flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-primary"></span> Tipos de Deficiência
      </h4>
      <div class="overflow-x-auto">
        <table class="w-full text-xs text-left">
          <thead class="text-muted-foreground uppercase bg-muted/40 border-b border-border">
            <tr>
              <th class="py-2 px-3 font-semibold">Tipo</th>
              <th class="py-2 px-3 font-semibold text-right">Qtd</th>
              <th class="py-2 px-3 font-semibold text-right">%</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border">
            <?php foreach ($stats['deficiencias'] as $def): ?>
              <?php $pct = $totalPcds > 0 ? round(($def['total'] / $totalPcds) * 100, 1) : 0; ?>
              <tr class="hover:bg-muted/30">
                <td class="py-2 px-3 font-medium text-foreground"><?= htmlspecialchars($def['nome']) ?></td>
                <td class="py-2 px-3 text-right text-foreground font-semibold"><?= (int)$def['total'] ?></td>
                <td class="py-2 px-3 text-right text-muted-foreground font-mono"><?= $pct ?>%</td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($stats['deficiencias'])): ?>
              <tr><td colspan="3" class="py-3 px-3 text-center text-muted-foreground">Nenhum registro deferido.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tabela 2: Faixas Etárias -->
    <div class="bg-card rounded-2xl p-5 border border-border shadow-sm print-avoid-break">
      <h4 class="text-sm font-bold text-foreground mb-3 flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-primary"></span> Faixas Etárias
      </h4>
      <div class="overflow-x-auto">
        <table class="w-full text-xs text-left">
          <thead class="text-muted-foreground uppercase bg-muted/40 border-b border-border">
            <tr>
              <th class="py-2 px-3 font-semibold">Faixa</th>
              <th class="py-2 px-3 font-semibold text-right">Qtd</th>
              <th class="py-2 px-3 font-semibold text-right">%</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border">
            <?php foreach ($stats['idades'] as $faixa => $qtd): ?>
              <?php $pct = $totalPcds > 0 ? round(($qtd / $totalPcds) * 100, 1) : 0; ?>
              <tr class="hover:bg-muted/30">
                <td class="py-2 px-3 font-medium text-foreground"><?= htmlspecialchars($faixa) ?> anos</td>
                <td class="py-2 px-3 text-right text-foreground font-semibold"><?= (int)$qtd ?></td>
                <td class="py-2 px-3 text-right text-muted-foreground font-mono"><?= $pct ?>%</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tabela 3: Escolaridade -->
    <div class="bg-card rounded-2xl p-5 border border-border shadow-sm print-avoid-break">
      <h4 class="text-sm font-bold text-foreground mb-3 flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-primary"></span> Grau de Escolaridade
      </h4>
      <div class="overflow-x-auto">
        <table class="w-full text-xs text-left">
          <thead class="text-muted-foreground uppercase bg-muted/40 border-b border-border">
            <tr>
              <th class="py-2 px-3 font-semibold">Nível</th>
              <th class="py-2 px-3 font-semibold text-right">Qtd</th>
              <th class="py-2 px-3 font-semibold text-right">%</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border">
            <?php foreach ($stats['escolaridade'] as $esc => $qtd): ?>
              <?php $pct = $totalPcds > 0 ? round(($qtd / $totalPcds) * 100, 1) : 0; ?>
              <tr class="hover:bg-muted/30">
                <td class="py-2 px-3 font-medium text-foreground"><?= htmlspecialchars($esc) ?></td>
                <td class="py-2 px-3 text-right text-foreground font-semibold"><?= (int)$qtd ?></td>
                <td class="py-2 px-3 text-right text-muted-foreground font-mono"><?= $pct ?>%</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tabela 4: Bairros -->
    <div class="bg-card rounded-2xl p-5 border border-border shadow-sm print-avoid-break">
      <h4 class="text-sm font-bold text-foreground mb-3 flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-primary"></span> Bairros com Maior Concentração
      </h4>
      <div class="overflow-x-auto">
        <table class="w-full text-xs text-left">
          <thead class="text-muted-foreground uppercase bg-muted/40 border-b border-border">
            <tr>
              <th class="py-2 px-3 font-semibold">Bairro</th>
              <th class="py-2 px-3 font-semibold text-right">Qtd</th>
              <th class="py-2 px-3 font-semibold text-right">%</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border">
            <?php foreach ($stats['bairros'] as $bairro => $qtd): ?>
              <?php $pct = $totalPcds > 0 ? round(($qtd / $totalPcds) * 100, 1) : 0; ?>
              <tr class="hover:bg-muted/30">
                <td class="py-2 px-3 font-medium text-foreground"><?= htmlspecialchars($bairro) ?></td>
                <td class="py-2 px-3 text-right text-foreground font-semibold"><?= (int)$qtd ?></td>
                <td class="py-2 px-3 text-right text-muted-foreground font-mono"><?= $pct ?>%</td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($stats['bairros'])): ?>
              <tr><td colspan="3" class="py-3 px-3 text-center text-muted-foreground">Nenhum registro com bairro preenchido.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Disclaimer LGPD e Conformidade Legal -->
<div class="bg-card rounded-2xl p-5 border border-border text-center shadow-sm print-avoid-break mb-8">
  <p class="text-xs md:text-sm text-muted-foreground leading-relaxed">
    <strong class="text-foreground">Conformidade LGPD (Lei nº 13.709/2018):</strong> Todos os dados estatísticos acima são anonimizados e apresentados estritamente de forma agregada para fins de formulação, execução e monitoramento de políticas públicas municipais. Nenhuma informação de identificação individual é exposta neste painel.
  </p>
</div>

<!-- Rodapé Oficial exclusivo para Impressão / Visto Institucional -->
<div class="print-only hidden mt-10 pt-4 border-t-2 border-slate-800 text-xs text-slate-700 print-avoid-break">
  <div class="flex items-center justify-between mb-6">
    <div class="text-left space-y-0.5">
      <p><strong>Base Legal:</strong> Lei Federal nº 13.146/2015 (LBI) e Lei Municipal nº 3.421.</p>
      <p class="text-[11px] text-slate-500">Relatório emitido para instrução de planejamento público, requerimentos e prestação de contas.</p>
    </div>
    <div class="text-right text-[11px] text-slate-500">
      <p>CMDPCD Jahu • Documento Administrativo</p>
    </div>
  </div>

  <div class="grid grid-cols-2 gap-8 pt-6 border-t border-slate-300 text-center">
    <div>
      <div class="border-b border-slate-400 w-3/4 mx-auto mb-1.5"></div>
      <p class="font-bold text-slate-900">Presidência do CMDPCD Jahu</p>
      <p class="text-[10px] text-slate-500">Conselho Municipal dos Direitos da Pessoa com Deficiência</p>
    </div>
    <div>
      <div class="border-b border-slate-400 w-3/4 mx-auto mb-1.5"></div>
      <p class="font-bold text-slate-900"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Administrador Responsável') ?></p>
      <p class="text-[10px] text-slate-500">Responsável pela Emissão do Relatório</p>
    </div>
  </div>
</div>

<!-- Lógica dos Gráficos com Chart.js e Exportação CSV -->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // 1. Gráfico: Tipo de Deficiência (Doughnut)
    const defLabels = <?= js_json($defLabels) ?>;
    const defData = <?= js_json($defData) ?>;
    
    new Chart(document.getElementById('chartTipo'), {
      type: 'doughnut',
      data: {
        labels: defLabels.length > 0 ? defLabels : ['Nenhum cadastro'],
        datasets: [{
          data: defData.length > 0 ? defData : [0],
          backgroundColor: ['#2D9CDB', '#56CCF2', '#F2994A', '#6FCF97', '#BB6BD9', '#EB5757', '#64748b'],
          borderWidth: 0
        }]
      },
      options: { 
        responsive: true, 
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              boxWidth: 12,
              padding: 12,
              font: { size: 11 }
            }
          }
        }
      }
    });

    // 2. Gráfico: Faixa Etária (Bar)
    const ageLabels = <?= js_json($ageLabels) ?>;
    const ageData = <?= js_json($ageData) ?>;

    new Chart(document.getElementById('chartIdade'), {
      type: 'bar',
      data: {
        labels: ageLabels.map(l => l + ' anos'),
        datasets: [{
          label: 'Pessoas',
          data: ageData,
          backgroundColor: 'hsl(204, 67%, 52%)',
          borderRadius: 6
        }]
      },
      options: { 
        responsive: true, 
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: { precision: 0 }
          }
        }
      }
    });

    // 3. Gráfico: Escolaridade (Horizontal Bar)
    const schoolLabels = <?= js_json($schoolLabels) ?>;
    const schoolData = <?= js_json($schoolData) ?>;

    new Chart(document.getElementById('chartEscolaridade'), {
      type: 'bar',
      data: {
        labels: schoolLabels,
        datasets: [{
          label: 'Pessoas',
          data: schoolData,
          backgroundColor: 'hsl(195, 84%, 64%)',
          borderRadius: 6
        }]
      },
      options: { 
        indexAxis: 'y',
        responsive: true, 
        maintainAspectRatio: false,
        scales: {
          x: {
            beginAtZero: true,
            ticks: { precision: 0 }
          }
        }
      }
    });

    // 4. Gráfico: Por Bairro (Bar)
    const bairroLabels = <?= js_json($bairroLabels) ?>;
    const bairroData = <?= js_json($bairroData) ?>;

    new Chart(document.getElementById('chartBairro'), {
      type: 'bar',
      data: {
        labels: bairroLabels.length > 0 ? bairroLabels : ['Nenhum cadastro'],
        datasets: [{
          label: 'Pessoas',
          data: bairroData.length > 0 ? bairroData : [0],
          backgroundColor: 'hsl(204, 67%, 52%)',
          borderRadius: 6
        }]
      },
      options: { 
        responsive: true, 
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: { precision: 0 }
          }
        }
      }
    });
  });

  // Função para exportar os dados em formato CSV para Excel
  function exportarRelatorioCSV() {
    const dataAtual = new Date();
    const dataFormatada = dataAtual.toLocaleDateString('pt-BR');
    const horaFormatada = dataAtual.toLocaleTimeString('pt-BR');

    const linhas = [
      ["PREFEITURA MUNICIPAL DE JAHU - ESTADO DE SÃO PAULO"],
      ["CONSELHO MUNICIPAL DOS DIREITOS DA PESSOA COM DEFICIÊNCIA (CMDPCD)"],
      ["RELATÓRIO EXECUTIVO DEMOGRÁFICO DE PESSOAS COM DEFICIÊNCIA"],
      ["Data de Emissão:", dataFormatada + " às " + horaFormatada],
      ["Emitido por:", <?= json_encode($_SESSION['user_name'] ?? 'Administrador') ?>],
      [],
      ["1. INDICADORES GERAIS CONSOLIDADOS"],
      ["Indicador", "Quantidade", "Percentual da Base"],
      ["Total de PCDs Cadastrados e Deferidos", <?= (int)$totalPcds ?>, "100%"],
      ["Recebem BPC/LOAS", <?= (int)$totalBpc ?>, "<?= $pctBpc ?>%"],
      ["Utilizam Tecnologia Assistiva", <?= (int)$totalTec ?>, "<?= $pctTec ?>%"],
      ["Vinculados a Entidades Assistenciais", <?= (int)$totalEnt ?>, "<?= $pctEnt ?>%"],
      [],
      ["2. DISTRIBUIÇÃO POR TIPO DE DEFICIÊNCIA"],
      ["Tipo de Deficiência", "Quantidade", "Percentual"]
    ];

    <?php foreach ($stats['deficiencias'] as $def): ?>
      linhas.push([
        <?= json_encode($def['nome']) ?>, 
        <?= (int)$def['total'] ?>, 
        "<?= $totalPcds > 0 ? round(($def['total'] / $totalPcds) * 100, 1) : 0 ?>%"
      ]);
    <?php endforeach; ?>

    linhas.push([]);
    linhas.push(["3. DISTRIBUIÇÃO POR FAIXA ETÁRIA"]);
    linhas.push(["Faixa Etária", "Quantidade", "Percentual"]);

    <?php foreach ($stats['idades'] as $faixa => $qtd): ?>
      linhas.push([
        <?= json_encode($faixa . ' anos') ?>, 
        <?= (int)$qtd ?>, 
        "<?= $totalPcds > 0 ? round(($qtd / $totalPcds) * 100, 1) : 0 ?>%"
      ]);
    <?php endforeach; ?>

    linhas.push([]);
    linhas.push(["4. DISTRIBUIÇÃO POR GRAU DE ESCOLARIDADE"]);
    linhas.push(["Nível de Instrução", "Quantidade", "Percentual"]);

    <?php foreach ($stats['escolaridade'] as $esc => $qtd): ?>
      linhas.push([
        <?= json_encode($esc) ?>, 
        <?= (int)$qtd ?>, 
        "<?= $totalPcds > 0 ? round(($qtd / $totalPcds) * 100, 1) : 0 ?>%"
      ]);
    <?php endforeach; ?>

    linhas.push([]);
    linhas.push(["5. DISTRIBUIÇÃO POR BAIRRO / REGIÃO"]);
    linhas.push(["Bairro", "Quantidade", "Percentual"]);

    <?php foreach ($stats['bairros'] as $bairro => $qtd): ?>
      linhas.push([
        <?= json_encode($bairro) ?>, 
        <?= (int)$qtd ?>, 
        "<?= $totalPcds > 0 ? round(($qtd / $totalPcds) * 100, 1) : 0 ?>%"
      ]);
    <?php endforeach; ?>

    linhas.push([]);
    linhas.push(["NOTA DE CONFORMIDADE LGPD:", "Dados estatísticos agregados e anonimizados nos termos da Lei Federal nº 13.709/2018."]);

    // Monta o arquivo CSV com delimitador ';' (padrão Brasil/Excel) e prefixo BOM UTF-8
    let csvConteudo = "\uFEFF";
    linhas.forEach(row => {
      let linhaFormatada = row.map(item => {
        let texto = String(item ?? '').replace(/"/g, '""');
        return `"${texto}"`;
      }).join(';');
      csvConteudo += linhaFormatada + "\r\n";
    });

    const blob = new Blob([csvConteudo], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    const dataIso = dataAtual.toISOString().slice(0, 10);
    link.setAttribute('href', url);
    link.setAttribute('download', `relatorio_demografico_cmdpcd_jahu_${dataIso}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }
</script>

<?php require_once __DIR__ . '/../partials/admin_footer.php'; ?>
