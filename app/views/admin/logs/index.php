<?php require_once __DIR__ . '/../../partials/admin_header.php'; ?>

<!-- ABA: Logs de Auditoria -->
<div id="tab-logs" class="admin-tab-content space-y-8">

  <div class="flex flex-wrap items-center justify-between gap-4 border-b border-border pb-4">
    <div>
      <h2 class="text-2xl font-black text-foreground flex items-center gap-2">
        <i data-lucide="shield-alert" class="w-7 h-7 text-primary"></i>
        Trilha de Auditoria e Logs do Sistema
      </h2>
      <p class="text-sm text-muted-foreground mt-1">
        Rastreabilidade de todas as ações de usuários, acessos a dados médicos (LGPD) e alterações no portal.
      </p>
    </div>
    
    <!-- Badge do Motor Ativo -->
    <div class="flex items-center gap-2">
      <span class="text-xs text-muted-foreground font-semibold">Motor de Armazenamento:</span>
      <?php if ($stats['motor_ativo'] === 'MongoDB'): ?>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-green-500/10 text-green-600 border border-green-500/20 shadow-sm">
          <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
          🍃 MongoDB (Nativo)
        </span>
      <?php else: ?>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-500/10 text-amber-600 border border-amber-500/20 shadow-sm" title="MongoDB offline nesta máquina. O sistema ativou o modo de contingência no MySQL automaticamente.">
          <span class="w-2 h-2 rounded-full bg-amber-500"></span>
          🐬 MySQL (Modo Contingência / Fallback)
        </span>
      <?php endif; ?>
    </div>
  </div>

  <!-- Cards de Indicadores -->
  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="bg-card rounded-2xl p-6 border border-border" style="box-shadow: var(--card-shadow)">
      <div class="flex items-center justify-between mb-2">
        <h3 class="text-sm font-bold text-muted-foreground">Total de Logs</h3>
        <i data-lucide="database" class="w-5 h-5 text-primary"></i>
      </div>
      <p class="text-3xl font-black"><?= number_format($stats['total_logs'], 0, ',', '.') ?></p>
      <p class="text-xs text-muted-foreground mt-1">Eventos rastreados</p>
    </div>

    <div class="bg-card rounded-2xl p-6 border border-border" style="box-shadow: var(--card-shadow)">
      <div class="flex items-center justify-between mb-2">
        <h3 class="text-sm font-bold text-muted-foreground">Atividades Hoje</h3>
        <i data-lucide="clock" class="w-5 h-5 text-primary"></i>
      </div>
      <p class="text-3xl font-black text-primary"><?= number_format($stats['total_hoje'], 0, ',', '.') ?></p>
      <p class="text-xs text-muted-foreground mt-1">Registros nas últimas 24h</p>
    </div>

    <div class="bg-card rounded-2xl p-6 border border-border" style="box-shadow: var(--card-shadow)">
      <div class="flex items-center justify-between mb-2">
        <h3 class="text-sm font-bold text-muted-foreground">Privacidade LGPD</h3>
        <i data-lucide="lock" class="w-5 h-5 text-green-600"></i>
      </div>
      <p class="text-3xl font-black text-green-600">100%</p>
      <p class="text-xs text-muted-foreground mt-1">Acessos a laudos auditados</p>
    </div>

    <div class="bg-card rounded-2xl p-6 border border-border" style="box-shadow: var(--card-shadow)">
      <div class="flex items-center justify-between mb-2">
        <h3 class="text-sm font-bold text-muted-foreground">Resiliência</h3>
        <i data-lucide="check-circle-2" class="w-5 h-5 text-blue-600"></i>
      </div>
      <p class="text-2xl font-black text-blue-600">Duplo Banco</p>
      <p class="text-xs text-muted-foreground mt-1">Mongo + MySQL Fallback</p>
    </div>
  </div>

  <!-- Formulário de Filtros -->
  <form method="GET" action="<?= BASE_URL ?>/admin/logs" class="bg-card rounded-2xl border border-border p-6 grid sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end" style="box-shadow: var(--card-shadow)" role="search">
    
    <div>
      <label for="filtro-modulo" class="block text-xs font-bold text-muted-foreground uppercase mb-1">Módulo</label>
      <select id="filtro-modulo" name="modulo" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        <option value="">Todos os Módulos</option>
        <?php foreach ($modulos as $k => $label): ?>
          <option value="<?= e($k) ?>" <?= ($filtros['modulo'] === $k) ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label for="filtro-acao" class="block text-xs font-bold text-muted-foreground uppercase mb-1">Tipo de Ação</label>
      <select id="filtro-acao" name="acao" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        <option value="">Todas as Ações</option>
        <?php foreach ($acoes as $k => $label): ?>
          <option value="<?= e($k) ?>" <?= ($filtros['acao'] === $k) ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label for="filtro-usuario" class="block text-xs font-bold text-muted-foreground uppercase mb-1">Usuário</label>
      <select id="filtro-usuario" name="usuario_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        <option value="">Todos os Usuários</option>
        <?php foreach ($usuarios as $u): ?>
          <option value="<?= (int)$u['id'] ?>" <?= ($filtros['usuario_id'] == $u['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($u['nome_completo']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label for="filtro-busca" class="block text-xs font-bold text-muted-foreground uppercase mb-1">Busca por Termo / IP</label>
      <input type="text" id="filtro-busca" name="busca" value="<?= htmlspecialchars($filtros['busca']) ?>" placeholder="Ex: laudo, 192.168..." class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
    </div>

    <div class="flex items-center gap-2">
      <button type="submit" class="flex-1 bg-primary text-primary-foreground font-bold px-4 py-2.5 rounded-xl hover:bg-primary/90 transition-colors flex items-center justify-center gap-2 text-sm">
        <i data-lucide="filter" class="w-4 h-4"></i> Filtrar
      </button>
      <?php if (!empty($filtros['modulo']) || !empty($filtros['acao']) || !empty($filtros['usuario_id']) || !empty($filtros['busca'])): ?>
        <a href="<?= BASE_URL ?>/admin/logs" class="p-2.5 rounded-xl bg-muted text-muted-foreground hover:text-foreground transition-colors" title="Limpar Filtros">
          <i data-lucide="x" class="w-4 h-4"></i>
        </a>
      <?php endif; ?>
    </div>
  </form>

  <!-- Tabela de Logs -->
  <div class="bg-card rounded-2xl border border-border overflow-hidden" style="box-shadow: var(--card-shadow)">
    <div class="p-6 border-b border-border flex items-center justify-between">
      <div>
        <h3 class="font-bold text-lg">Registros Recentes (<?= count($logs) ?>)</h3>
        <p class="text-xs text-muted-foreground">Ordem cronológica reversa (ações mais recentes primeiro).</p>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead class="text-xs text-muted-foreground uppercase bg-muted/50">
          <tr>
            <th class="px-6 py-4 font-bold">Data / Hora</th>
            <th class="px-6 py-4 font-bold">Usuário Responsável</th>
            <th class="px-6 py-4 font-bold">Módulo</th>
            <th class="px-6 py-4 font-bold">Ação Realizada</th>
            <th class="px-6 py-4 font-bold">Detalhes / Metadados</th>
            <th class="px-6 py-4 font-bold">Origem IP</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border font-mono text-xs">
          <?php if (empty($logs)): ?>
            <tr>
              <td colspan="6" class="px-6 py-12 text-center text-muted-foreground font-sans">
                <i data-lucide="shield-check" class="w-12 h-12 text-muted-foreground/30 mx-auto mb-3"></i>
                <p class="font-semibold text-base">Nenhum registro de log encontrado para os filtros atuais.</p>
                <p class="text-xs mt-1">Os logs são gerados automaticamente quando qualquer usuário interage com o sistema.</p>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($logs as $log): ?>
              <?php
                // Cores de badge conforme o módulo
                $modColors = [
                  'auth'      => 'bg-purple-500/10 text-purple-600 border-purple-500/20',
                  'pcds'      => 'bg-blue-500/10 text-blue-600 border-blue-500/20',
                  'conteudos' => 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20',
                  'conselho'  => 'bg-indigo-500/10 text-indigo-600 border-indigo-500/20',
                  'usuarios'  => 'bg-orange-500/10 text-orange-600 border-orange-500/20',
                  'pedidos'   => 'bg-yellow-500/10 text-yellow-600 border-yellow-500/20',
                  'mensagens' => 'bg-teal-500/10 text-teal-600 border-teal-500/20',
                  'faq'       => 'bg-pink-500/10 text-pink-600 border-pink-500/20',
                ];
                $modBadge = $modColors[$log['modulo']] ?? 'bg-muted text-muted-foreground border-border';

                // Destaque para visualização de documentos sensíveis (LGPD) ou falha de login
                $isSensivel = ($log['acao'] === 'pcd_documento_visualizado');
                $isAlerta = ($log['acao'] === 'login_falha' || $log['acao'] === 'pcd_indeferido' || $log['acao'] === 'pcd_excluido');
              ?>
              <tr class="hover:bg-muted/30 transition-colors font-sans <?= $isSensivel ? 'bg-blue-500/5' : ($isAlerta ? 'bg-destructive/5' : '') ?>">
                
                <!-- Data e Hora -->
                <td class="px-6 py-4 whitespace-nowrap text-xs text-muted-foreground font-mono">
                  <div class="flex items-center gap-1.5 font-semibold text-foreground">
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-muted-foreground shrink-0"></i>
                    <span><?= e($log['data_hora_str']) ?></span>
                  </div>
                </td>

                <!-- Usuário -->
                <td class="px-6 py-4">
                  <div>
                    <strong class="font-bold text-foreground text-xs block"><?= htmlspecialchars($log['usuario_nome']) ?></strong>
                    <?php if (!empty($log['usuario_email'])): ?>
                      <span class="text-xs text-muted-foreground font-mono block"><?= htmlspecialchars($log['usuario_email']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($log['usuario_perfil'])): ?>
                      <span class="inline-block mt-0.5 text-[10px] font-bold uppercase px-1.5 py-0.5 rounded bg-muted text-muted-foreground">
                        <?= e($log['usuario_perfil']) ?>
                      </span>
                    <?php endif; ?>
                  </div>
                </td>

                <!-- Módulo -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border <?= $modBadge ?>">
                    <?= e($log['modulo_label']) ?>
                  </span>
                </td>

                <!-- Ação / Descrição -->
                <td class="px-6 py-4">
                  <div class="max-w-xs">
                    <span class="font-bold text-xs <?= $isSensivel ? 'text-blue-600' : ($isAlerta ? 'text-destructive' : 'text-foreground') ?>">
                      <?= e($log['descricao']) ?>
                    </span>
                    <span class="block text-[11px] font-mono text-muted-foreground mt-0.5">
                      <?= e($log['acao']) ?>
                    </span>
                  </div>
                </td>

                <!-- Detalhes do Payload -->
                <td class="px-6 py-4 text-xs font-mono">
                  <?php if (!empty($log['detalhes'])): ?>
                    <details class="cursor-pointer group">
                      <summary class="text-primary hover:underline text-xs font-sans list-none flex items-center gap-1">
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 group-open:rotate-90 transition-transform"></i>
                        Ver metadados (<?= count($log['detalhes']) ?>)
                      </summary>
                      <div class="mt-2 p-2.5 rounded-lg bg-muted/60 border border-border text-[11px] overflow-x-auto max-w-sm whitespace-pre-wrap">
<?= htmlspecialchars(json_encode($log['detalhes'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?>
                      </div>
                    </details>
                  <?php else: ?>
                    <span class="text-muted-foreground italic font-sans text-xs">—</span>
                  <?php endif; ?>
                </td>

                <!-- IP / User Agent -->
                <td class="px-6 py-4 whitespace-nowrap font-mono text-xs">
                  <span class="font-semibold text-foreground"><?= e($log['ip']) ?></span>
                  <?php if (!empty($log['origem'])): ?>
                    <span class="block text-[10px] text-muted-foreground font-sans mt-0.5">
                      Gravado via <?= e($log['origem']) ?>
                    </span>
                  <?php endif; ?>
                </td>

              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/../../partials/admin_footer.php'; ?>
