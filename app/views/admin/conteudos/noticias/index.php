<?php require_once __DIR__ . '/../../../partials/admin_header.php'; ?>

<div class="space-y-10">
  <!-- Breadcrumb / Voltar -->
  <div class="flex items-center gap-2 text-sm text-muted-foreground">
    <a href="<?= BASE_URL ?>/admin/conteudos" class="hover:text-primary transition-colors">Conteúdos</a>
    <i data-lucide="chevron-right" class="w-4 h-4"></i>
    <span class="text-foreground font-semibold">Notícias</span>
  </div>

  <?php if (!empty($success)): ?>
    <div class="bg-primary/10 text-primary text-sm p-4 rounded-xl border border-primary/20 flex items-center gap-2">
      <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
      <span><?= htmlspecialchars($success) ?></span>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="bg-destructive/10 text-destructive text-sm p-4 rounded-xl border border-destructive/20 flex items-center gap-2">
      <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
      <span><?= htmlspecialchars($error) ?></span>
    </div>
  <?php endif; ?>

  <div class="bg-card rounded-2xl border border-border overflow-hidden" style="box-shadow: var(--card-shadow)">
    <div class="p-6 border-b border-border flex items-center justify-between flex-wrap gap-4">
      <div>
        <h2 class="text-xl font-bold">Notícias Cadastradas</h2>
        <p class="text-sm text-muted-foreground">Gerencie as publicações do site institucional.</p>
      </div>
      <?php if (!isAdminParcial()): ?>
        <a href="<?= BASE_URL ?>/admin/conteudos/noticias/create" class="bg-primary text-primary-foreground px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 hover:bg-primary/90 transition-colors">
          <i data-lucide="plus" class="w-4 h-4"></i> Nova Notícia
        </a>
      <?php endif; ?>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead class="text-xs text-muted-foreground uppercase bg-muted/50">
          <tr>
            <th class="px-6 py-4 font-bold">Capa</th>
            <th class="px-6 py-4 font-bold">Título</th>
            <th class="px-6 py-4 font-bold">Tema</th>
            <th class="px-6 py-4 font-bold">Status</th>
            <th class="px-6 py-4 font-bold">Autor</th>
            <th class="px-6 py-4 font-bold">Data Publicação</th>
            <th class="px-6 py-4 font-bold text-center">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border">
          <?php if (count($noticias) === 0): ?>
            <tr>
              <td colspan="7" class="px-6 py-8 text-center text-muted-foreground">
                Nenhuma notícia cadastrada até o momento.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($noticias as $noticia): ?>
              <?php 
                $dataPub = $noticia['data_publicacao'] ? date('d/m/Y H:i', strtotime($noticia['data_publicacao'])) : 'Imediata';
              ?>
              <tr class="hover:bg-muted/50 transition-colors">
                <td class="px-6 py-4 shrink-0">
                  <?php if ($noticia['imagem_capa']): ?>
                    <img src="<?= BASE_URL . '/' . htmlspecialchars($noticia['imagem_capa']) ?>" alt="Capa" class="w-16 h-10 object-cover rounded-lg border border-border">
                  <?php else: ?>
                    <div class="w-16 h-10 bg-muted rounded-lg flex items-center justify-center border border-border text-[10px] text-muted-foreground font-semibold">
                      Sem Capa
                    </div>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 font-semibold text-foreground max-w-xs truncate" title="<?= htmlspecialchars($noticia['titulo']) ?>">
                  <?= htmlspecialchars($noticia['titulo']) ?>
                </td>
                <td class="px-6 py-4">
                  <span class="inline-block bg-primary/10 text-primary text-xs font-bold px-2.5 py-1 rounded-full">
                    <?= htmlspecialchars(ucfirst($noticia['tema'])) ?>
                  </span>
                </td>
                <td class="px-6 py-4">
                  <?php if ($noticia['status'] === 'publicado'): ?>
                    <span class="inline-flex items-center gap-1 bg-green-500/10 text-green-600 text-xs font-bold px-2.5 py-1 rounded-full">
                      <i data-lucide="check" class="w-3 h-3"></i> Publicado
                    </span>
                  <?php elseif ($noticia['status'] === 'rascunho'): ?>
                    <span class="inline-flex items-center gap-1 bg-yellow-500/10 text-yellow-600 text-xs font-bold px-2.5 py-1 rounded-full">
                      <i data-lucide="file" class="w-3 h-3"></i> Rascunho
                    </span>
                  <?php else: ?>
                    <span class="inline-flex items-center gap-1 bg-muted text-muted-foreground text-xs font-bold px-2.5 py-1 rounded-full">
                      <i data-lucide="archive" class="w-3 h-3"></i> Arquivado
                    </span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 text-muted-foreground">
                  <?= htmlspecialchars($noticia['autor_nome']) ?>
                </td>
                <td class="px-6 py-4 text-muted-foreground">
                  <?= e($dataPub) ?>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center gap-2">
                    <a href="<?= BASE_URL ?>/admin/conteudos/noticias/edit?id=<?= $noticia['id'] ?>" class="p-2 rounded-lg bg-primary/10 text-primary hover:bg-primary/20 transition-colors" title="<?= isAdminParcial() ? 'Visualizar' : 'Editar' ?>">
                      <i data-lucide="<?= isAdminParcial() ? 'eye' : 'edit' ?>" class="w-4 h-4"></i>
                    </a>
                    <?php if (!isAdminParcial()): ?>
                      <form method="POST" action="<?= BASE_URL ?>/admin/conteudos/noticias/delete" class="inline m-0 p-0">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$noticia['id'] ?>">
                        <button type="submit" data-confirm="Deseja realmente excluir a notícia: <?= e($noticia['titulo']) ?>?" class="p-2 rounded-lg bg-destructive/10 text-destructive hover:bg-destructive/20 transition-colors cursor-pointer" title="Excluir">
                          <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                      </form>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../../partials/admin_footer.php'; ?>
