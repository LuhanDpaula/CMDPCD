<?php require_once __DIR__ . '/../../partials/admin_header.php'; ?>

<!-- ABA: Cadastros (PCDs) -->
<div id="tab-cadastros" class="admin-tab-content">
  
  <?php if (!empty($success)): ?>
    <div class="bg-green-500/10 text-green-600 text-sm p-4 rounded-xl border border-green-500/20 mb-6 flex items-center gap-2">
      <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
      <span><?= htmlspecialchars($success) ?></span>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="bg-destructive/10 text-destructive text-sm p-4 rounded-xl border border-destructive/20 mb-6 flex items-center gap-2">
      <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
      <span><?= htmlspecialchars($error) ?></span>
    </div>
  <?php endif; ?>

  <div class="bg-card rounded-2xl border border-border overflow-hidden" style="box-shadow: var(--card-shadow)">
    <div class="p-6 border-b border-border flex flex-wrap items-center justify-between gap-4">
      <div>
        <h2 class="text-xl font-bold">PCDs Cadastrados</h2>
        <p class="text-sm text-muted-foreground">Gerenciamento de pessoas com deficiência registradas no portal.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap">
        <button onclick="alert('Importação de CSV será integrada no próximo semestre conforme cronograma acadêmico.');" class="bg-card text-foreground/50 border border-border/50 px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 opacity-50 cursor-not-allowed hover:bg-card">
          <i data-lucide="upload" class="w-4 h-4 text-foreground/30"></i> Importar CSV
        </button>
        <?php if (!isAdminParcial()): ?>
          <a href="<?= BASE_URL ?>/admin/pcds/create" class="bg-primary text-primary-foreground px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 hover:bg-primary/90 transition-colors">
            <i data-lucide="plus" class="w-4 h-4"></i> Novo Cadastro
          </a>
        <?php endif; ?>
      </div>
    </div>
    
    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead class="text-xs text-muted-foreground uppercase bg-muted/50">
          <tr>
            <th class="px-6 py-4 font-bold">Nome</th>
            <th class="px-6 py-4 font-bold">CPF</th>
            <th class="px-6 py-4 font-bold">Deficiências</th>
            <th class="px-6 py-4 font-bold">Cidade/Bairro</th>
            <th class="px-6 py-4 font-bold">Contato</th>
            <th class="px-6 py-4 font-bold">Data Cad.</th>
            <th class="px-6 py-4 font-bold">Status</th>
            <th class="px-6 py-4 font-bold text-center">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border">
          <?php if (count($pcds) === 0): ?>
            <tr>
              <td colspan="8" class="px-6 py-8 text-center text-muted-foreground">
                Nenhum cadastro de PCD encontrado no banco de dados.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($pcds as $pcd): ?>
              <?php 
                // Formata CPF
                $cpf = $pcd['cpf'];
                if (strlen($cpf) === 11) {
                  $cpf = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9);
                }

                // Formata Celular
                $fone = $pcd['telefone_principal'];
                if (strlen($fone) === 11) {
                  $fone = '(' . substr($fone, 0, 2) . ') ' . substr($fone, 2, 5) . '-' . substr($fone, 7);
                }

                $dataCad = date('d/m/Y', strtotime($pcd['criado_em']));
              ?>
              <tr class="hover:bg-muted/50 transition-colors">
                <td class="px-6 py-4 font-semibold text-foreground">
                  <div>
                    <?= htmlspecialchars($pcd['nome_completo']) ?>
                    <?php if (!empty($pcd['nome_social'])): ?>
                      <span class="text-xs text-muted-foreground block font-normal">(Nome social: <?= htmlspecialchars($pcd['nome_social']) ?>)</span>
                    <?php endif; ?>
                  </div>
                </td>
                <td class="px-6 py-4 text-muted-foreground"><?= htmlspecialchars($cpf) ?></td>
                <td class="px-6 py-4 text-muted-foreground font-medium">
                  <?= !empty($pcd['deficiencias']) ? htmlspecialchars($pcd['deficiencias']) : '<span class="text-xs italic text-muted-foreground/70">Nenhuma</span>' ?>
                </td>
                <td class="px-6 py-4 text-muted-foreground">
                  <?= htmlspecialchars($pcd['cidade']) ?> / <?= htmlspecialchars($pcd['bairro']) ?>
                </td>
                <td class="px-6 py-4 text-muted-foreground">
                  <div>
                    <span><?= htmlspecialchars($fone) ?></span>
                    <?php if (!empty($pcd['email'])): ?>
                      <span class="text-xs text-muted-foreground block"><?= htmlspecialchars($pcd['email']) ?></span>
                    <?php endif; ?>
                  </div>
                </td>
                <td class="px-6 py-4 text-muted-foreground"><?= e($dataCad) ?></td>
                <td class="px-6 py-4">
                  <?php if (($pcd['status'] ?? 'deferido') === 'deferido'): ?>
                    <span class="inline-flex items-center gap-1 bg-green-500/10 text-green-600 text-xs font-bold px-2.5 py-1 rounded-full">
                      <i data-lucide="check" class="w-3 h-3"></i> Deferido
                    </span>
                  <?php else: ?>
                    <span class="inline-flex items-center gap-1 bg-yellow-500/10 text-yellow-600 text-xs font-bold px-2.5 py-1 rounded-full animate-pulse">
                      <i data-lucide="clock" class="w-3 h-3"></i> Pendente
                    </span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center gap-2">
                    <?php if (($pcd['status'] ?? 'deferido') === 'pendente' && !isAdminParcial()): ?>
                      <form method="POST" action="<?= BASE_URL ?>/admin/pcds/deferir" class="inline m-0 p-0">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$pcd['id'] ?>">
                        <button type="submit" class="p-2 rounded-lg bg-green-500/10 text-green-600 hover:bg-green-500/20 transition-colors cursor-pointer" title="Deferir (Aprovar)">
                          <i data-lucide="check-circle" class="w-4 h-4"></i>
                        </button>
                      </form>
                      <form method="POST" action="<?= BASE_URL ?>/admin/pcds/indeferir" class="inline m-0 p-0">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$pcd['id'] ?>">
                        <button type="submit" data-confirm="Deseja realmente INDEFERIR e excluir permanentemente o cadastro de <?= e($pcd['nome_completo']) ?>?" class="p-2 rounded-lg bg-destructive/10 text-destructive hover:bg-destructive/20 transition-colors cursor-pointer" title="Indeferir (Rejeitar e Excluir)">
                          <i data-lucide="x-circle" class="w-4 h-4"></i>
                        </button>
                      </form>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/admin/pcds/edit?id=<?= $pcd['id'] ?>" class="p-2 rounded-lg bg-primary/10 text-primary hover:bg-primary/20 transition-colors" title="<?= isAdminParcial() ? 'Visualizar' : 'Visualizar/Editar' ?>">
                      <i data-lucide="<?= isAdminParcial() ? 'eye' : 'edit' ?>" class="w-4 h-4"></i>
                    </a>
                    <?php if (($pcd['status'] ?? 'deferido') === 'deferido' && !isAdminParcial()): ?>
                      <form method="POST" action="<?= BASE_URL ?>/admin/pcds/delete" class="inline m-0 p-0">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$pcd['id'] ?>">
                        <button type="submit" data-confirm="Deseja realmente excluir o cadastro de <?= e($pcd['nome_completo']) ?>?" class="p-2 rounded-lg bg-destructive/10 text-destructive hover:bg-destructive/20 transition-colors cursor-pointer" title="Excluir">
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

<?php require_once __DIR__ . '/../../partials/admin_footer.php'; ?>
