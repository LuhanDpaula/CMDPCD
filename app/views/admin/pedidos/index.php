<?php require_once __DIR__ . '/../../partials/admin_header.php'; ?>
<?php
$badgeStatus = [
    'aguardando'     => 'bg-yellow-500/10 text-yellow-600',
    'em_atendimento' => 'bg-blue-500/10 text-blue-600',
    'concluido'      => 'bg-green-500/10 text-green-600',
    'sem_retorno'    => 'bg-muted text-muted-foreground',
];
$ps = $stats['por_status'];
?>

<div class="space-y-8">
  <div>
    <h2 class="text-2xl font-black text-foreground">Pedidos de Ajuda</h2>
    <p class="text-sm text-muted-foreground">Pessoas que pediram, pelo chat do site, que o Conselho entre em contato. Atenda conforme a demanda. Dados armazenados no MongoDB.</p>
  </div>

  <?php require __DIR__ . '/../../partials/admin_alertas.php'; ?>

  <!-- Indicadores (Aggregation Pipeline) -->
  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-card rounded-2xl border border-border p-5" style="box-shadow: var(--card-shadow)">
      <p class="text-sm text-muted-foreground">Aguardando contato</p>
      <p class="text-3xl font-black text-yellow-600"><?= (int)($ps['aguardando'] ?? 0) ?></p>
      <?php if ($stats['dias_pedido_mais_antigo'] !== null): ?>
        <p class="text-xs text-muted-foreground mt-1">Mais antigo: há <?= (int)$stats['dias_pedido_mais_antigo'] ?> dia(s)</p>
      <?php endif; ?>
    </div>
    <div class="bg-card rounded-2xl border border-border p-5" style="box-shadow: var(--card-shadow)">
      <p class="text-sm text-muted-foreground">Em atendimento</p>
      <p class="text-3xl font-black text-blue-600"><?= (int)($ps['em_atendimento'] ?? 0) ?></p>
    </div>
    <div class="bg-card rounded-2xl border border-border p-5" style="box-shadow: var(--card-shadow)">
      <p class="text-sm text-muted-foreground">Encerrados (concluído / sem retorno)</p>
      <p class="text-3xl font-black"><?= (int)($ps['concluido'] ?? 0) ?> <span class="text-muted-foreground text-xl">/ <?= (int)($ps['sem_retorno'] ?? 0) ?></span></p>
    </div>
    <div class="bg-card rounded-2xl border border-border p-5" style="box-shadow: var(--card-shadow)">
      <p class="text-sm text-muted-foreground">Tempo médio até o 1º contato</p>
      <p class="text-3xl font-black"><?= $stats['media_dias_primeiro_contato'] !== null ? e(number_format($stats['media_dias_primeiro_contato'], 1, ',', '')) . ' <span class="text-sm font-semibold text-muted-foreground">dias</span>' : '—' ?></p>
    </div>
  </div>

  <!-- Filtros -->
  <form method="GET" action="<?= BASE_URL ?>/admin/pedidos" class="bg-card rounded-2xl border border-border p-6 grid sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end" style="box-shadow: var(--card-shadow)" role="search">
    <div>
      <label for="f-status" class="block text-sm font-medium mb-1">Situação</label>
      <select id="f-status" name="status" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        <option value="abertos" <?= $filtros['status'] === 'abertos' ? 'selected' : '' ?>>Em aberto (fila)</option>
        <?php foreach (PedidoAjuda::STATUS as $k => $label): ?>
          <option value="<?= e($k) ?>" <?= $filtros['status'] === $k ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
        <option value="todos" <?= $filtros['status'] === 'todos' ? 'selected' : '' ?>>Todos</option>
      </select>
    </div>
    <div>
      <label for="f-assunto" class="block text-sm font-medium mb-1">Assunto</label>
      <select id="f-assunto" name="assunto" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        <option value="">Todos</option>
        <?php foreach (PedidoAjuda::ASSUNTOS as $k => $label): ?>
          <option value="<?= e($k) ?>" <?= $filtros['assunto'] === $k ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label for="f-bairro" class="block text-sm font-medium mb-1">Bairro</label>
      <input id="f-bairro" type="text" name="bairro" value="<?= e($filtros['bairro']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
    </div>
    <div>
      <label for="f-q" class="block text-sm font-medium mb-1">Nome, telefone ou protocolo</label>
      <div class="flex gap-2">
        <input id="f-q" type="text" name="q" value="<?= e($filtros['q']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        <button type="submit" class="bg-primary text-primary-foreground px-4 py-2 rounded-xl text-sm font-bold hover:bg-primary/90 transition-colors">Filtrar</button>
      </div>
    </div>
  </form>

  <!-- Fila -->
  <div class="bg-card rounded-2xl border border-border overflow-hidden" style="box-shadow: var(--card-shadow)">
    <div class="p-6 border-b border-border">
      <h3 class="text-lg font-bold"><?= count($pedidos) ?> pedido(s)</h3>
      <?php if (in_array($filtros['status'], ['abertos', 'aguardando', 'em_atendimento'], true)): ?>
        <p class="text-sm text-muted-foreground">Ordem de chegada (mais antigos primeiro). Pedidos marcados como prioritários pelo Conselho aparecem no topo.</p>
      <?php endif; ?>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <caption class="sr-only">Fila de pedidos de ajuda</caption>
        <thead class="text-xs text-muted-foreground uppercase bg-muted/50">
          <tr>
            <th class="px-6 py-4 font-bold">Protocolo</th>
            <th class="px-6 py-4 font-bold">Pessoa</th>
            <th class="px-6 py-4 font-bold">Assunto</th>
            <th class="px-6 py-4 font-bold">Contato</th>
            <th class="px-6 py-4 font-bold">Situação</th>
            <th class="px-6 py-4 font-bold">Recebido</th>
            <th class="px-6 py-4 font-bold text-center">Abrir</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border">
          <?php if (!$pedidos): ?>
            <tr><td colspan="7" class="px-6 py-8 text-center text-muted-foreground">Nenhum pedido encontrado com esses filtros.</td></tr>
          <?php endif; ?>
          <?php foreach ($pedidos as $p): ?>
            <tr class="hover:bg-muted/30 transition-colors <?= !empty($p['prioritario']) ? 'bg-destructive/10' : '' ?>">
              <td class="px-6 py-4 font-bold whitespace-nowrap">
                <?= e($p['protocolo']) ?>
                <?php if (!empty($p['prioritario'])): ?>
                  <span class="block text-xs text-destructive font-bold">★ Prioritário</span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4">
                <p class="font-semibold"><?= e($p['nome']) ?></p>
                <p class="text-xs text-muted-foreground"><?= e($p['bairro']) ?></p>
              </td>
              <td class="px-6 py-4"><?= e(PedidoAjuda::ASSUNTOS[$p['assunto']] ?? $p['assunto']) ?></td>
              <td class="px-6 py-4 text-xs whitespace-nowrap">
                <?= e(PedidoAjuda::PREFERENCIAS[$p['preferencia_contato']] ?? '') ?><br>
                <span class="text-muted-foreground"><?= e(PedidoAjuda::HORARIOS[$p['melhor_horario']] ?? '') ?></span>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $badgeStatus[$p['status']] ?? '' ?>">
                  <?= e(PedidoAjuda::STATUS[$p['status']] ?? $p['status']) ?>
                </span>
              </td>
              <td class="px-6 py-4 text-xs text-muted-foreground whitespace-nowrap"><?= e(Mongo::date($p['criado_em'])) ?></td>
              <td class="px-6 py-4 text-center">
                <a href="<?= BASE_URL ?>/admin/pedidos/ver?id=<?= e((string)$p['_id']) ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-primary/10 text-primary text-xs font-bold hover:bg-primary/20 transition-colors" aria-label="Abrir pedido <?= e($p['protocolo']) ?> de <?= e($p['nome']) ?>">
                  Abrir <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Estatísticas -->
  <div class="grid md:grid-cols-2 gap-6">
    <div class="bg-card rounded-2xl border border-border p-6" style="box-shadow: var(--card-shadow)">
      <h3 class="text-lg font-bold mb-4">Pedidos por assunto</h3>
      <?php $maxA = max(array_column($stats['por_assunto'], 'total') ?: [1]); ?>
      <?php if (!$stats['por_assunto']): ?><p class="text-sm text-muted-foreground">Sem dados ainda.</p><?php endif; ?>
      <ul class="space-y-3">
        <?php foreach ($stats['por_assunto'] as $a): ?>
          <li>
            <div class="flex justify-between text-sm mb-1"><span><?= e(PedidoAjuda::ASSUNTOS[$a['_id']] ?? $a['_id']) ?></span><strong><?= (int)$a['total'] ?></strong></div>
            <div class="h-2 bg-muted rounded-full overflow-hidden" aria-hidden="true"><div class="h-2 bg-primary rounded-full" style="width: <?= round($a['total'] / $maxA * 100) ?>%"></div></div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="bg-card rounded-2xl border border-border p-6" style="box-shadow: var(--card-shadow)">
      <h3 class="text-lg font-bold mb-1">Histórico mensal</h3>
      <p class="text-xs text-muted-foreground mb-4">Resumo sem dados pessoais (coleção <code>resumo_mensal_pedidos</code>, gerada com <code>$merge</code>). Continua existindo mesmo depois que os pedidos são apagados pela LGPD.</p>
      <?php if (!$resumo): ?><p class="text-sm text-muted-foreground">Sem dados ainda.</p><?php endif; ?>
      <?php $maxM = max(array_column($resumo, 'total') ?: [1]); ?>
      <ul class="space-y-3">
        <?php foreach ($resumo as $m): ?>
          <?php [$ano, $mes] = explode('-', $m['_id']); ?>
          <li>
            <div class="flex justify-between text-sm mb-1">
              <span><?= e($mes . '/' . $ano) ?></span>
              <span><strong><?= (int)$m['total'] ?></strong> <span class="text-muted-foreground text-xs">(<?= (int)$m['concluidos'] ?> concluídos)</span></span>
            </div>
            <div class="h-2 bg-muted rounded-full overflow-hidden" aria-hidden="true"><div class="h-2 bg-primary rounded-full" style="width: <?= round($m['total'] / $maxM * 100) ?>%"></div></div>
          </li>
        <?php endforeach; ?>
      </ul>
      <?php if ($stats['por_bairro']): ?>
        <h4 class="text-sm font-bold mt-6 mb-2">Bairros com mais pedidos</h4>
        <p class="text-sm text-muted-foreground">
          <?= e(implode(' · ', array_map(fn($b) => mb_convert_case($b['_id'], MB_CASE_TITLE, 'UTF-8') . ' (' . $b['total'] . ')', $stats['por_bairro']))) ?>
        </p>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../partials/admin_footer.php'; ?>
