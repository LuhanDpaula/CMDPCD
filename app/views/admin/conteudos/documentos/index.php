<?php require_once __DIR__ . '/../../../partials/admin_header.php'; ?>

<div class="space-y-10">
  <!-- Breadcrumb / Voltar -->
  <div class="flex items-center gap-2 text-sm text-muted-foreground">
    <a href="<?= BASE_URL ?>/admin/conteudos" class="hover:text-primary transition-colors">Conteúdos</a>
    <i data-lucide="chevron-right" class="w-4 h-4"></i>
    <span class="text-foreground font-semibold">Documentos Públicos</span>
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
        <h2 class="text-xl font-bold">Documentos Públicos</h2>
        <p class="text-sm text-muted-foreground">Gerencie as atas, resoluções e outros documentos da Transparência.</p>
      </div>
      <?php if (!isAdminParcial()): ?>
        <a href="<?= BASE_URL ?>/admin/conteudos/documentos/create" class="bg-primary text-primary-foreground px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 hover:bg-primary/90 transition-colors">
          <i data-lucide="plus" class="w-4 h-4"></i> Novo Documento
        </a>
      <?php endif; ?>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead class="text-xs text-muted-foreground uppercase bg-muted/50">
          <tr>
            <th class="px-6 py-4 font-bold">Título / Descrição</th>
            <th class="px-6 py-4 font-bold">Tipo</th>
            <th class="px-6 py-4 font-bold">Data de Publicação</th>
            <th class="px-6 py-4 font-bold">Arquivo</th>
            <th class="px-6 py-4 font-bold">Status</th>
            <th class="px-6 py-4 font-bold text-center">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border">
          <?php if (!empty($documentos)): ?>
            <?php foreach ($documentos as $doc): ?>
              <?php
                $tipoMap = [
                  'ata' => 'Ata',
                  'resolucao' => 'Resolução',
                  'edital' => 'Edital',
                  'oficio' => 'Ofício',
                  'relatorio' => 'Relatório',
                  'outros' => 'Outros'
                ];
                $tipoLabel = $tipoMap[$doc['tipo']] ?? ucfirst($doc['tipo']);
                $dataStr = date('d/m/Y', strtotime($doc['data_publicacao']));
                $fileUrl = BASE_URL . '/' . $doc['caminho_arquivo'];
              ?>
              <tr class="hover:bg-muted/50 transition-colors">
                <td class="px-6 py-4 max-w-xs sm:max-w-md">
                  <div class="font-semibold text-foreground truncate" title="<?= htmlspecialchars($doc['titulo']) ?>">
                    <?= htmlspecialchars($doc['titulo']) ?>
                  </div>
                  <?php if ($doc['descricao']): ?>
                    <p class="text-xs text-muted-foreground line-clamp-1 mt-0.5"><?= htmlspecialchars($doc['descricao']) ?></p>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4">
                  <span class="inline-block bg-primary/10 text-primary text-xs font-bold px-2.5 py-1 rounded-full">
                    <?= e($tipoLabel) ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-muted-foreground"><?= e($dataStr) ?></td>
                <td class="px-6 py-4">
                  <a href="<?= e($fileUrl) ?>" target="_blank" class="inline-flex items-center gap-1 text-xs text-primary font-semibold hover:underline">
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i> PDF
                  </a>
                </td>
                <td class="px-6 py-4">
                  <?php if ($doc['status'] === 'publicado'): ?>
                    <span class="inline-flex items-center gap-1 bg-green-500/10 text-green-600 text-xs font-bold px-2.5 py-1 rounded-full">
                      <i data-lucide="check" class="w-3 h-3"></i> Publicado
                    </span>
                  <?php else: ?>
                    <span class="inline-flex items-center gap-1 bg-muted text-muted-foreground text-xs font-bold px-2.5 py-1 rounded-full">
                      <i data-lucide="archive" class="w-3 h-3"></i> Arquivado
                    </span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center gap-2">
                    <a href="<?= BASE_URL ?>/admin/conteudos/documentos/edit?id=<?= $doc['id'] ?>" class="p-2 rounded-lg bg-primary/10 text-primary hover:bg-primary/20 transition-colors" title="<?= isAdminParcial() ? 'Visualizar' : 'Editar' ?>">
                      <i data-lucide="<?= isAdminParcial() ? 'eye' : 'edit' ?>" class="w-4 h-4"></i>
                    </a>
                    <?php if (!isAdminParcial()): ?>
                      <form method="POST" action="<?= BASE_URL ?>/admin/conteudos/documentos/delete" class="inline m-0 p-0">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$doc['id'] ?>">
                        <button type="submit" data-confirm="Deseja realmente excluir este documento?" class="p-2 rounded-lg bg-destructive/10 text-destructive hover:bg-destructive/20 transition-colors cursor-pointer" title="Excluir">
                          <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                      </form>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="px-6 py-8 text-center text-muted-foreground">
                Nenhum documento público cadastrado no momento.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../../partials/admin_footer.php'; ?>
