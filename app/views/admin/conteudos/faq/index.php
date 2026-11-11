<?php require_once __DIR__ . '/../../../partials/admin_header.php'; ?>

<div class="space-y-8">
  <nav class="flex items-center gap-2 text-sm text-muted-foreground" aria-label="Caminho">
    <a href="<?= BASE_URL ?>/admin/conteudos" class="hover:text-primary transition-colors">Conteúdos</a>
    <i data-lucide="chevron-right" class="w-4 h-4"></i>
    <span class="text-foreground font-semibold" aria-current="page">Perguntas Frequentes (Chat)</span>
  </nav>

  <?php require __DIR__ . '/../../../partials/admin_alertas.php'; ?>

  <!-- Números gerais (aggregation no MongoDB) -->
  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-card rounded-2xl border border-border p-5" style="box-shadow: var(--card-shadow)">
      <p class="text-sm text-muted-foreground">Perguntas ativas</p>
      <p class="text-3xl font-black"><?= (int)$stats['ativas'] ?> <span class="text-sm font-semibold text-muted-foreground">de <?= (int)$stats['total'] ?></span></p>
    </div>
    <div class="bg-card rounded-2xl border border-border p-5" style="box-shadow: var(--card-shadow)">
      <p class="text-sm text-muted-foreground">Respostas abertas no chat</p>
      <p class="text-3xl font-black"><?= (int)$stats['visualizacoes'] ?></p>
    </div>
    <div class="bg-card rounded-2xl border border-border p-5" style="box-shadow: var(--card-shadow)">
      <p class="text-sm text-muted-foreground">Avaliações "ajudou" / "não ajudou"</p>
      <p class="text-3xl font-black"><span class="text-green-600"><?= (int)$stats['util_sim'] ?></span> / <span class="text-destructive"><?= (int)$stats['util_nao'] ?></span></p>
    </div>
    <a href="<?= BASE_URL ?>/admin/conteudos/faq/sem-resposta" class="bg-card rounded-2xl border border-border p-5 hover:border-primary/30 transition-colors block" style="box-shadow: var(--card-shadow)">
      <p class="text-sm text-muted-foreground">Dúvidas sem resposta (90 dias)</p>
      <p class="text-3xl font-black"><?= (int)$semRespostaTotal ?></p>
      <p class="text-xs text-primary font-bold mt-1">Ver e transformar em pergunta →</p>
    </a>
  </div>

  <div class="bg-card rounded-2xl border border-border overflow-hidden" style="box-shadow: var(--card-shadow)">
    <div class="p-6 border-b border-border flex items-center justify-between flex-wrap gap-4">
      <div>
        <h2 class="text-xl font-bold">Perguntas Frequentes do Chat</h2>
        <p class="text-sm text-muted-foreground">Estas perguntas aparecem no botão "Posso ajudar?" do site. Armazenadas no MongoDB.</p>
      </div>
      <a href="<?= BASE_URL ?>/admin/conteudos/faq/create" class="bg-primary text-primary-foreground px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 hover:bg-primary/90 transition-colors">
        <i data-lucide="plus" class="w-4 h-4"></i> Nova Pergunta
      </a>
    </div>

    <form method="GET" action="<?= BASE_URL ?>/admin/conteudos/faq" class="p-6 border-b border-border grid sm:grid-cols-3 gap-4 items-end" role="search">
      <div>
        <label for="filtro-categoria" class="block text-sm font-medium mb-1">Categoria</label>
        <select id="filtro-categoria" name="categoria" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
          <option value="">Todas</option>
          <?php foreach (Faq::CATEGORIAS as $k => $label): ?>
            <option value="<?= e($k) ?>" <?= $categoria === $k ? 'selected' : '' ?>><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="filtro-busca" class="block text-sm font-medium mb-1">Buscar</label>
        <input id="filtro-busca" type="text" name="q" value="<?= e($busca) ?>" placeholder="Texto da pergunta ou palavra-chave" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
      </div>
      <div class="flex gap-2">
        <button type="submit" class="bg-primary text-primary-foreground px-4 py-2 rounded-xl text-sm font-bold hover:bg-primary/90 transition-colors">Filtrar</button>
        <a href="<?= BASE_URL ?>/admin/conteudos/faq" class="bg-card border border-border px-4 py-2 rounded-xl text-sm font-semibold hover:bg-muted/50 transition-colors">Limpar</a>
      </div>
    </form>

    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <caption class="sr-only">Lista de perguntas frequentes</caption>
        <thead class="text-xs text-muted-foreground uppercase bg-muted/50">
          <tr>
            <th class="px-6 py-4 font-bold">Pergunta</th>
            <th class="px-6 py-4 font-bold">Categoria</th>
            <th class="px-6 py-4 font-bold">Situação</th>
            <th class="px-6 py-4 font-bold">Uso</th>
            <th class="px-6 py-4 font-bold text-center">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border">
          <?php if (!$perguntas): ?>
            <tr><td colspan="5" class="px-6 py-8 text-center text-muted-foreground">Nenhuma pergunta encontrada.</td></tr>
          <?php endif; ?>
          <?php foreach ($perguntas as $p): $id = (string)$p['_id']; ?>
            <tr class="hover:bg-muted/30 transition-colors">
              <td class="px-6 py-4">
                <p class="font-semibold text-foreground"><?= e($p['pergunta']) ?></p>
                <?php if (!empty($p['palavras_chave'])): ?>
                  <p class="text-xs text-muted-foreground mt-1">Palavras-chave: <?= e(implode(', ', $p['palavras_chave'])) ?></p>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap"><?= e(Faq::CATEGORIAS[$p['categoria']] ?? $p['categoria']) ?></td>
              <td class="px-6 py-4">
                <?php if (!empty($p['ativo'])): ?>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/10 text-green-600">Ativa</span>
                <?php else: ?>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-muted text-muted-foreground">Desativada</span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 text-xs text-muted-foreground whitespace-nowrap">
                <?= (int)($p['visualizacoes'] ?? 0) ?> visualizações<br>
                <span class="text-green-600">👍 <?= (int)($p['util']['sim'] ?? 0) ?></span> ·
                <span class="text-destructive">👎 <?= (int)($p['util']['nao'] ?? 0) ?></span>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2">
                  <a href="<?= BASE_URL ?>/admin/conteudos/faq/edit?id=<?= e($id) ?>" class="p-2 rounded-lg bg-primary/10 text-primary hover:bg-primary/20 transition-colors" title="Editar">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                  </a>
                  <form method="POST" action="<?= BASE_URL ?>/admin/conteudos/faq/toggle" class="inline m-0 p-0">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= e($id) ?>">
                    <button type="submit" class="p-2 rounded-lg bg-muted text-foreground hover:bg-muted/50 transition-colors cursor-pointer" title="<?= !empty($p['ativo']) ? 'Desativar (tirar do chat)' : 'Ativar (mostrar no chat)' ?>">
                      <i data-lucide="<?= !empty($p['ativo']) ? 'eye-off' : 'eye' ?>" class="w-4 h-4"></i>
                    </button>
                  </form>
                  <form method="POST" action="<?= BASE_URL ?>/admin/conteudos/faq/delete" class="inline m-0 p-0">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= e($id) ?>">
                    <button type="submit" data-confirm="Excluir definitivamente a pergunta: <?= e($p['pergunta']) ?>?" class="p-2 rounded-lg bg-destructive/10 text-destructive hover:bg-destructive/20 transition-colors cursor-pointer" title="Excluir">
                      <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../../partials/admin_footer.php'; ?>
