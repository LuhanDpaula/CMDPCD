<?php require_once __DIR__ . '/../../../partials/admin_header.php'; ?>

<div class="space-y-10">
  <!-- Breadcrumb / Voltar -->
  <div class="flex items-center gap-2 text-sm text-muted-foreground">
    <a href="<?= BASE_URL ?>/admin/conteudos" class="hover:text-primary transition-colors">Conteúdos</a>
    <i data-lucide="chevron-right" class="w-4 h-4"></i>
    <span class="text-foreground font-semibold">Membros e Presidentes</span>
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
        <h2 class="text-xl font-bold">Membros do Conselho & Galeria de Presidentes</h2>
        <p class="text-sm text-muted-foreground">Gerencie a composição atual e o histórico de gestões passadas.</p>
      </div>
      <?php if (!isAdminParcial()): ?>
        <a href="<?= BASE_URL ?>/admin/conteudos/membros/create" class="bg-primary text-primary-foreground px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 hover:bg-primary/90 transition-colors">
          <i data-lucide="plus" class="w-4 h-4"></i> Novo Membro / Presidente
        </a>
      <?php endif; ?>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead class="text-xs text-muted-foreground uppercase bg-muted/50">
          <tr>
            <th class="px-6 py-4 font-bold">Foto</th>
            <th class="px-6 py-4 font-bold">Nome</th>
            <th class="px-6 py-4 font-bold">Função</th>
            <th class="px-6 py-4 font-bold">Entidade Representada</th>
            <th class="px-6 py-4 font-bold">Gestão (Período)</th>
            <th class="px-6 py-4 font-bold">Status</th>
            <th class="px-6 py-4 font-bold text-center">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border">
          <?php if (count($membros) === 0): ?>
            <tr>
              <td colspan="7" class="px-6 py-8 text-center text-muted-foreground">
                Nenhum membro ou presidente cadastrado.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($membros as $membro): ?>
              <?php 
                $funcoesTraducao = [
                  'presidente' => 'Presidente',
                  'vice-presidente' => 'Vice-Presidente',
                  'secretario' => 'Secretário(a)',
                  'titular' => 'Conselheiro(a) Titular',
                  'suplente' => 'Conselheiro(a) Suplente'
                ];
                $funcao = $funcoesTraducao[$membro['funcao']] ?? ucfirst($membro['funcao']);
                
                $periodo = date('d/m/Y', strtotime($membro['data_inicio']));
                if ($membro['data_fim']) {
                  $periodo .= ' até ' . date('d/m/Y', strtotime($membro['data_fim']));
                } else {
                  $periodo .= ' - Atual';
                }
              ?>
              <tr class="hover:bg-muted/50 transition-colors">
                <td class="px-6 py-4 shrink-0">
                  <div class="w-10 h-10 rounded-full overflow-hidden border border-border bg-muted flex items-center justify-center">
                    <?php if ($membro['foto']): ?>
                      <img src="<?= BASE_URL . '/' . htmlspecialchars($membro['foto']) ?>" alt="Foto" class="w-full h-full object-cover">
                    <?php else: ?>
                      <i data-lucide="user" class="w-5 h-5 text-muted-foreground"></i>
                    <?php endif; ?>
                  </div>
                </td>
                <td class="px-6 py-4 font-semibold text-foreground">
                  <?= htmlspecialchars($membro['nome_completo']) ?>
                </td>
                <td class="px-6 py-4">
                  <span class="inline-block bg-primary/10 text-primary text-xs font-bold px-2.5 py-1 rounded-full">
                    <?= e($funcao) ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-muted-foreground">
                  <?= htmlspecialchars($membro['entidade_representada'] ?? 'N/A') ?>
                </td>
                <td class="px-6 py-4 text-muted-foreground text-xs">
                  <?= e($periodo) ?>
                </td>
                <td class="px-6 py-4">
                  <?php if ($membro['ativo']): ?>
                    <span class="inline-flex items-center gap-1 bg-green-500/10 text-green-600 text-xs font-bold px-2.5 py-1 rounded-full">
                      <i data-lucide="check" class="w-3 h-3"></i> Membro Atual
                    </span>
                  <?php else: ?>
                    <span class="inline-flex items-center gap-1 bg-muted text-muted-foreground text-xs font-bold px-2.5 py-1 rounded-full">
                      <i data-lucide="history" class="w-3 h-3"></i> Histórico
                    </span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center gap-2">
                    <a href="<?= BASE_URL ?>/admin/conteudos/membros/edit?id=<?= $membro['id'] ?>" class="p-2 rounded-lg bg-primary/10 text-primary hover:bg-primary/20 transition-colors" title="<?= isAdminParcial() ? 'Visualizar' : 'Editar' ?>">
                      <i data-lucide="<?= isAdminParcial() ? 'eye' : 'edit' ?>" class="w-4 h-4"></i>
                    </a>
                    <?php if (!isAdminParcial()): ?>
                      <form method="POST" action="<?= BASE_URL ?>/admin/conteudos/membros/delete" class="inline m-0 p-0">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$membro['id'] ?>">
                        <button type="submit" data-confirm="Deseja realmente excluir <?= e($membro['nome_completo']) ?>?" class="p-2 rounded-lg bg-destructive/10 text-destructive hover:bg-destructive/20 transition-colors cursor-pointer" title="Excluir">
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
