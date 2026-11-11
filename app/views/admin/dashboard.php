<?php require_once __DIR__ . '/../partials/admin_header.php'; ?>

<!-- ABA: Dashboard -->
<div id="tab-dashboard" class="admin-tab-content">
  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
    
    <div class="bg-card rounded-2xl p-6 border border-border" style="box-shadow: var(--card-shadow)">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-muted-foreground">Total PCDs</h3>
        <i data-lucide="clipboard-list" class="w-5 h-5 text-primary"></i>
      </div>
      <p class="text-3xl font-black" id="stat-total-cadastros"><?= $totalPcds ?></p>
      <p class="text-xs text-muted-foreground mt-2">Pessoas com Deficiência</p>
    </div>

    <div class="bg-card rounded-2xl p-6 border border-border" style="box-shadow: var(--card-shadow)">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-muted-foreground">Usuários do Painel</h3>
        <i data-lucide="users" class="w-5 h-5 text-primary"></i>
      </div>
      <p class="text-3xl font-black"><?= $totalUsuarios ?></p>
      <p class="text-xs text-muted-foreground mt-2">Membros do Conselho</p>
    </div>

    <div class="bg-card rounded-2xl p-6 border border-border hover:border-primary/20 transition-all duration-300" style="box-shadow: var(--card-shadow)">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-muted-foreground">Mensagens</h3>
        <a href="<?= BASE_URL ?>/admin/mensagens" class="text-primary hover:underline">
          <i data-lucide="mail" class="w-5 h-5"></i>
        </a>
      </div>
      <p class="text-3xl font-black">
        <?= $totalMensagens ?>
        <?php if ($mensagensNaoLidas > 0): ?>
          <span class="text-xs font-bold text-destructive bg-destructive/10 px-2 py-0.5 rounded-full ml-1">
            <?= $mensagensNaoLidas ?> nova<?= $mensagensNaoLidas > 1 ? 's' : '' ?>
          </span>
        <?php endif; ?>
      </p>
      <p class="text-xs text-muted-foreground mt-2">Contatos pelo site</p>
    </div>

    <div class="bg-card rounded-2xl p-6 border border-border" style="box-shadow: var(--card-shadow)">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-muted-foreground">Status do Sistema</h3>
        <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
      </div>
      <p class="text-3xl font-black">100%</p>
      <p class="text-xs text-muted-foreground mt-2">Operacional (MVC)</p>
    </div>
  </div>

  <div class="grid lg:grid-cols-2 gap-8">
    <div class="bg-card rounded-2xl p-6 border border-border relative overflow-hidden" style="box-shadow: var(--card-shadow)">
      <h3 class="font-bold mb-4">Cadastros por Mês (Amostra)</h3>
      <div class="relative" style="height: 300px;">
        <!-- Container do gráfico com desfoque -->
        <div class="w-full h-full select-none pointer-events-none" style="filter: blur(5px);">
          <canvas id="admin-chart-mes"></canvas>
        </div>
        
        <!-- Overlay "Em Construção" -->
        <div class="absolute inset-0 flex flex-col items-center justify-center bg-card/40 text-center z-10">
          <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-3 shadow-inner">
            <i data-lucide="wrench" class="w-6 h-6 animate-bounce"></i>
          </div>
          <h4 class="font-bold text-foreground text-sm mb-1">Seção em Construção</h4>
          <p class="text-xs text-muted-foreground max-w-[240px]">
            O gráfico histórico de evolução mensal dos cadastros está sendo conectado aos dados dinâmicos.
          </p>
        </div>
      </div>
    </div>
    <div class="bg-card rounded-2xl p-6 border border-border" style="box-shadow: var(--card-shadow)">
      <h3 class="font-bold mb-4">Tipos de Deficiência (Registrados)</h3>
      <div style="position: relative; height: 300px;">
        <canvas id="admin-chart-tipo"></canvas>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    if (window.Chart) {
      // 1. Gráfico de Cadastros por Mês (Amostra Estática + Banco)
      const ctxMes = document.getElementById('admin-chart-mes');
      if (ctxMes) {
        new Chart(ctxMes, {
          type: 'bar',
          data: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
            datasets: [{
              label: 'Cadastros',
              data: [12, 19, 15, 25, 22, <?= $totalPcds ?>],
              backgroundColor: 'hsl(204, 67%, 52%)',
              borderRadius: 8
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } } }
          }
        });
      }

      // 2. Gráfico de Tipos de Deficiência (Dados Reais do Banco)
      const ctxTipo = document.getElementById('admin-chart-tipo');
      if (ctxTipo) {
        const labels = <?= js_json(array_column($deficienciasStats, 'nome')) ?>;
        const counts = <?= js_json(array_map('intval', array_column($deficienciasStats, 'total'))) ?>;

        // Se estiver vazio, exibe fallback
        const chartLabels = labels.length > 0 ? labels : ['Nenhum'];
        const chartCounts = counts.length > 0 ? counts : [0];

        new Chart(ctxTipo, {
          type: 'doughnut',
          data: {
            labels: chartLabels,
            datasets: [{
              data: chartCounts,
              backgroundColor: [
                '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#64748b', '#ec4899'
              ],
              borderWidth: 0
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { position: 'bottom', labels: { usePointStyle: true, padding: 15 } }
            },
            cutout: '70%'
          }
        });
      }
    }
  });
</script>

<?php require_once __DIR__ . '/../partials/admin_footer.php'; ?>
