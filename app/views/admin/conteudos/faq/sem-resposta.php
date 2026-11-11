<?php require_once __DIR__ . '/../../../partials/admin_header.php'; ?>

<div class="space-y-8">
  <nav class="flex items-center gap-2 text-sm text-muted-foreground" aria-label="Caminho">
    <a href="<?= BASE_URL ?>/admin/conteudos" class="hover:text-primary transition-colors">Conteúdos</a>
    <i data-lucide="chevron-right" class="w-4 h-4"></i>
    <a href="<?= BASE_URL ?>/admin/conteudos/faq" class="hover:text-primary transition-colors">Perguntas Frequentes</a>
    <i data-lucide="chevron-right" class="w-4 h-4"></i>
    <span class="text-foreground font-semibold" aria-current="page">Dúvidas sem resposta</span>
  </nav>

  <?php require __DIR__ . '/../../../partials/admin_alertas.php'; ?>

  <div class="bg-card rounded-2xl border border-border overflow-hidden" style="box-shadow: var(--card-shadow)">
    <div class="p-6 border-b border-border">
      <h2 class="text-xl font-bold">O que as pessoas perguntaram e o chat não soube responder</h2>
      <p class="text-sm text-muted-foreground">Agrupado pelo texto digitado, do mais repetido para o menos. Nenhum dado pessoal é guardado e os registros são apagados automaticamente após 90 dias.</p>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <caption class="sr-only">Dúvidas sem resposta</caption>
        <thead class="text-xs text-muted-foreground uppercase bg-muted/50">
          <tr>
            <th class="px-6 py-4 font-bold">Texto digitado</th>
            <th class="px-6 py-4 font-bold">Vezes</th>
            <th class="px-6 py-4 font-bold">Última vez</th>
            <th class="px-6 py-4 font-bold text-center">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border">
          <?php if (!$itens): ?>
            <tr><td colspan="4" class="px-6 py-8 text-center text-muted-foreground">Nenhuma dúvida sem resposta. O chat está dando conta! 🎉</td></tr>
          <?php endif; ?>
          <?php foreach ($itens as $i): ?>
            <tr class="hover:bg-muted/30 transition-colors">
              <td class="px-6 py-4 font-semibold"><?= e($i['exemplo']) ?></td>
              <td class="px-6 py-4"><?= (int)$i['vezes'] ?></td>
              <td class="px-6 py-4 text-muted-foreground whitespace-nowrap"><?= e(Mongo::date($i['ultima'] ?? null)) ?></td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2 flex-wrap">
                  <a href="<?= BASE_URL ?>/admin/conteudos/faq/create?pergunta=<?= urlencode($i['exemplo']) ?>&amp;origem=<?= urlencode($i['_id']) ?>" class="px-3 py-1.5 rounded-lg bg-primary/10 text-primary text-xs font-bold hover:bg-primary/20 transition-colors">
                    Criar pergunta
                  </a>
                  <form method="POST" action="<?= BASE_URL ?>/admin/conteudos/faq/sem-resposta/descartar" class="inline m-0 p-0">
                    <?= csrf_field() ?>
                    <input type="hidden" name="texto" value="<?= e($i['_id']) ?>">
                    <button type="submit" data-confirm="Descartar esta dúvida da lista?" class="px-3 py-1.5 rounded-lg bg-muted text-foreground text-xs font-bold hover:bg-muted/50 transition-colors cursor-pointer">
                      Descartar
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
