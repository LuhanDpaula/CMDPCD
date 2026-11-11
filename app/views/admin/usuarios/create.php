<?php require_once __DIR__ . '/../../partials/admin_header.php'; ?>

<div class="bg-card rounded-2xl border border-border overflow-hidden max-w-2xl mx-auto" style="box-shadow: var(--card-shadow)">
  <div class="p-6 border-b border-border">
    <h2 class="text-xl font-bold">Cadastrar Novo Usuário</h2>
    <p class="text-sm text-muted-foreground">Adicione um novo membro do conselho com acesso administrativo.</p>
  </div>

  <?php if (!empty($error)): ?>
    <div class="bg-destructive/10 text-destructive text-sm p-4 mx-6 mt-6 rounded-xl border border-destructive/20 flex items-center gap-2">
      <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
      <span><?= htmlspecialchars($error) ?></span>
    </div>
  <?php endif; ?>

  <form action="<?= BASE_URL ?>/admin/usuarios/store" method="POST" class="p-6 space-y-4">
    <?= csrf_field() ?>
    <div class="grid sm:grid-cols-2 gap-4">
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">Nome Completo *</label>
        <input type="text" name="nome_completo" required value="<?= htmlspecialchars($old['nome_completo'] ?? '') ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
      </div>
      
      <div>
        <label class="block text-sm font-medium mb-1">CPF *</label>
        <input type="text" id="cpf" name="cpf" required value="<?= htmlspecialchars($old['cpf'] ?? '') ?>" placeholder="000.000.000-00" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
      </div>
      
      <div>
        <label class="block text-sm font-medium mb-1">Celular *</label>
        <input type="text" id="celular" name="celular" required value="<?= htmlspecialchars($old['celular'] ?? '') ?>" placeholder="(00) 00000-0000" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
      </div>
      
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">E-mail *</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="exemplo@email.com" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
      </div>
      
      <div>
        <label class="block text-sm font-medium mb-1">Senha *</label>
        <input type="password" name="senha" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
      </div>
      
      <div>
        <label class="block text-sm font-medium mb-1">Confirmar Senha *</label>
        <input type="password" name="senha_confirma" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
      </div>
      
      <div>
        <label class="block text-sm font-medium mb-1">Perfil de Acesso *</label>
        <select name="perfil_id" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
          <option value="">Selecione</option>
          <?php foreach ($perfis as $p): ?>
            <?php 
              $roleName = 'Editor';
              if ($p['nome'] === 'admin_total') $roleName = 'Admin Total';
              elseif ($p['nome'] === 'admin_parcial') $roleName = 'Admin Parcial';
            ?>
            <option value="<?= $p['id'] ?>" <?= (isset($old['perfil_id']) && $old['perfil_id'] == $p['id']) ? 'selected' : '' ?>>
              <?= $roleName ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Status *</label>
        <select name="status" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
          <option value="ativo" <?= (isset($old['status']) && $old['status'] === 'ativo') ? 'selected' : '' ?>>Ativo</option>
          <option value="inativo" <?= (isset($old['status']) && $old['status'] === 'inativo') ? 'selected' : '' ?>>Inativo</option>
          <option value="bloqueado" <?= (isset($old['status']) && $old['status'] === 'bloqueado') ? 'selected' : '' ?>>Bloqueado</option>
        </select>
      </div>
    </div>

    <div class="flex justify-end gap-3 pt-4 border-t border-border">
      <a href="<?= BASE_URL ?>/admin/usuarios" class="px-6 py-2 border border-border text-foreground rounded-full hover:bg-muted transition-colors">
        Cancelar
      </a>
      <button type="submit" class="px-6 py-2 bg-primary text-primary-foreground font-bold rounded-full hover:bg-primary/90 transition-colors">
        Salvar Usuário
      </button>
    </div>
  </form>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Máscaras simples
    const cpfInput = document.getElementById('cpf');
    if (cpfInput) {
      cpfInput.addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, "");
        v = v.replace(/(\d{3})(\d)/, "$1.$2");
        v = v.replace(/(\d{3})(\d)/, "$1.$2");
        v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        e.target.value = v.substring(0, 14);
      });
    }

    const celInput = document.getElementById('celular');
    if (celInput) {
      celInput.addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, "");
        v = v.replace(/(\d{2})(\d)/, "($1) $2");
        v = v.replace(/(\d{5})(\d)/, "$1-$2");
        e.target.value = v.substring(0, 15);
      });
    }
  });
</script>

<?php require_once __DIR__ . '/../../partials/admin_footer.php'; ?>
