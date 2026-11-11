<?php require_once __DIR__ . '/../../partials/admin_header.php'; ?>

<!-- ABA: Usuários -->
<div id="tab-users" class="admin-tab-content">
  
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
    <div class="p-6 border-b border-border flex items-center justify-between flex-wrap gap-4">
      <div>
        <h2 class="text-xl font-bold">Usuários Administrativos</h2>
        <p class="text-sm text-muted-foreground">Gerencie quem tem acesso ao painel.</p>
      </div>
      <?php if (!isAdminParcial()): ?>
        <a href="<?= BASE_URL ?>/admin/usuarios/create" class="bg-primary text-primary-foreground px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 hover:bg-primary/90 transition-colors">
          <i data-lucide="plus" class="w-4 h-4"></i> Novo Usuário
        </a>
      <?php endif; ?>
    </div>
    
    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead class="text-xs text-muted-foreground uppercase bg-muted/50">
          <tr>
            <th class="px-6 py-4 font-bold">Nome</th>
            <th class="px-6 py-4 font-bold">E-mail</th>
            <th class="px-6 py-4 font-bold">CPF</th>
            <th class="px-6 py-4 font-bold">Celular</th>
            <th class="px-6 py-4 font-bold">Papel</th>
            <th class="px-6 py-4 font-bold">Status</th>
            <th class="px-6 py-4 font-bold text-center">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border">
          <?php if (count($usuarios) === 0): ?>
            <tr>
              <td colspan="7" class="px-6 py-8 text-center text-muted-foreground">
                Nenhum usuário administrativo encontrado.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($usuarios as $user): ?>
              <?php 
                $roleName = 'Editor';
                if ($user['perfil_nome'] === 'admin_total') $roleName = 'Admin Total';
                elseif ($user['perfil_nome'] === 'admin_parcial') $roleName = 'Admin Parcial';
                
                // Formata CPF
                $cpf = $user['cpf'];
                if (strlen($cpf) === 11) {
                  $cpf = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9);
                }

                // Formata Celular
                $cel = $user['celular'];
                if (strlen($cel) === 11) {
                  $cel = '(' . substr($cel, 0, 2) . ') ' . substr($cel, 2, 5) . '-' . substr($cel, 7);
                }
              ?>
              <tr class="hover:bg-muted/50 transition-colors">
                <td class="px-6 py-4 font-semibold text-foreground flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold shrink-0">
                    <?= strtoupper(substr($user['nome_completo'], 0, 1)) ?>
                  </div>
                  <span><?= htmlspecialchars($user['nome_completo']) ?></span>
                </td>
                <td class="px-6 py-4 text-muted-foreground"><?= htmlspecialchars($user['email']) ?></td>
                <td class="px-6 py-4 text-muted-foreground"><?= htmlspecialchars($cpf) ?></td>
                <td class="px-6 py-4 text-muted-foreground"><?= htmlspecialchars($cel) ?></td>
                <td class="px-6 py-4">
                  <span class="inline-block bg-primary/10 text-primary text-xs font-bold px-3 py-1 rounded-full">
                    <?= $roleName ?>
                  </span>
                </td>
                <td class="px-6 py-4">
                  <?php if ($user['status'] === 'ativo'): ?>
                    <span class="inline-flex items-center gap-1 bg-green-500/10 text-green-600 text-xs font-bold px-2.5 py-1 rounded-full">
                      <i data-lucide="check" class="w-3 h-3"></i> Ativo
                    </span>
                  <?php else: ?>
                    <span class="inline-flex items-center gap-1 bg-destructive/10 text-destructive text-xs font-bold px-2.5 py-1 rounded-full">
                      <i data-lucide="x" class="w-3 h-3"></i> <?= htmlspecialchars(ucfirst($user['status'])) ?>
                    </span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center gap-2">
                    <a href="<?= BASE_URL ?>/admin/usuarios/edit?id=<?= $user['id'] ?>" class="p-2 rounded-lg bg-primary/10 text-primary hover:bg-primary/20 transition-colors" title="<?= isAdminParcial() ? 'Visualizar' : 'Editar' ?>">
                      <i data-lucide="<?= isAdminParcial() ? 'eye' : 'edit' ?>" class="w-4 h-4"></i>
                    </a>
                    
                    <?php if (!isAdminParcial()): ?>
                      <?php if ($user['id'] != $_SESSION['user_id']): ?>
                        <button type="button" 
                                data-id="<?= (int)$user['id'] ?>" 
                                data-nome="<?= htmlspecialchars($user['nome_completo'], ENT_QUOTES, 'UTF-8') ?>" 
                                class="btn-delete-user p-2 rounded-lg bg-destructive/10 text-destructive hover:bg-destructive/20 transition-colors cursor-pointer" 
                                title="Excluir Usuário">
                          <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                      <?php else: ?>
                        <span class="p-2 opacity-30 cursor-not-allowed" title="Você não pode se excluir">
                          <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </span>
                      <?php endif; ?>
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

<!-- Modal Sudo Mode: Confirmação de Exclusão de Usuário -->
<div id="delete-user-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300" style="background-color: rgba(0, 0, 0, 0.45);">
  <div class="bg-card rounded-2xl border border-border max-w-md w-full overflow-hidden shadow-2xl relative flex flex-col transform scale-95 transition-transform duration-300">
    <!-- Header -->
    <div class="p-6 border-b border-border flex items-center justify-between shrink-0 bg-destructive/5">
      <div class="flex items-center gap-3">
        <div class="p-2.5 rounded-xl bg-destructive/10 text-destructive">
          <i data-lucide="shield-alert" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 class="text-base font-bold text-foreground">Confirmar Exclusão</h3>
          <p class="text-xs text-muted-foreground">Confirmação de segurança (Sudo Mode)</p>
        </div>
      </div>
      <button id="modal-delete-close" type="button" class="p-2 rounded-xl bg-muted/50 hover:bg-muted text-foreground transition-all">
        <i data-lucide="x" class="w-4 h-4"></i>
      </button>
    </div>

    <!-- Form -->
    <form method="POST" action="<?= BASE_URL ?>/admin/usuarios/delete" class="p-6 space-y-4">
      <?= csrf_field() ?>
      <input type="hidden" name="id" id="modal-delete-user-id" value="">

      <div class="text-sm text-foreground space-y-2">
        <p>Você está prestes a excluir o usuário <strong id="modal-delete-user-nome" class="text-destructive font-semibold"></strong>.</p>
        <p class="text-xs text-muted-foreground leading-relaxed">
          Esta ação desativará imediatamente o acesso e revogará qualquer sessão ativa do usuário. Por segurança, digite a <strong>sua senha de administrador</strong> para confirmar a exclusão.
        </p>
      </div>

      <div class="space-y-1.5">
        <label for="admin_senha" class="text-xs font-semibold text-foreground uppercase tracking-wider block">Sua Senha Atual</label>
        <input type="password" name="admin_senha" id="admin_senha" required placeholder="Digite sua senha de administrador" autocomplete="current-password" class="w-full bg-background border border-border rounded-xl px-4 py-2.5 text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
      </div>

      <div class="flex items-center justify-end gap-3 pt-3 border-t border-border">
        <button type="button" id="modal-delete-cancel" class="px-4 py-2 text-xs font-semibold rounded-xl bg-muted text-muted-foreground hover:bg-muted/80 transition-colors cursor-pointer">
          Cancelar
        </button>
        <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-xl bg-destructive text-destructive-foreground hover:bg-destructive/90 transition-colors inline-flex items-center gap-1.5 shadow-sm cursor-pointer">
          <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Confirmar Exclusão
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('delete-user-modal');
    const modalClose = document.getElementById('modal-delete-close');
    const modalCancel = document.getElementById('modal-delete-cancel');
    const inputUserId = document.getElementById('modal-delete-user-id');
    const labelUserNome = document.getElementById('modal-delete-user-nome');
    const inputAdminSenha = document.getElementById('admin_senha');

    function openModal(id, nome) {
      if (!modal) return;
      inputUserId.value = id;
      labelUserNome.textContent = nome;
      if (inputAdminSenha) inputAdminSenha.value = '';
      modal.classList.remove('hidden');
      setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.querySelector('.transform').classList.remove('scale-95');
        if (inputAdminSenha) inputAdminSenha.focus();
        if (window.lucide) lucide.createIcons();
      }, 10);
    }

    function closeModal() {
      if (!modal) return;
      modal.classList.add('opacity-0');
      modal.querySelector('.transform').classList.add('scale-95');
      setTimeout(() => {
        modal.classList.add('hidden');
      }, 300);
    }

    document.querySelectorAll('.btn-delete-user').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const id = btn.getAttribute('data-id');
        const nome = btn.getAttribute('data-nome');
        openModal(id, nome);
      });
    });

    if (modalClose) modalClose.addEventListener('click', closeModal);
    if (modalCancel) modalCancel.addEventListener('click', closeModal);
    if (modal) {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
      });
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
          closeModal();
        }
      });
    }
  });
</script>

<?php require_once __DIR__ . '/../../partials/admin_footer.php'; ?>
