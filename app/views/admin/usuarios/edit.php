<?php require_once __DIR__ . '/../../partials/admin_header.php'; ?>

<div class="bg-card rounded-2xl border border-border overflow-hidden max-w-2xl mx-auto" style="box-shadow: var(--card-shadow)">
  <div class="p-6 border-b border-border">
    <h2 class="text-xl font-bold">Editar Usuário: <?= htmlspecialchars($usuario['nome_completo']) ?></h2>
    <p class="text-sm text-muted-foreground">Atualize as informações do membro administrativo.</p>
  </div>

  <?php if (isAdminParcial()): ?>
    <div class="bg-primary/5 text-primary text-sm p-4 mx-6 mt-6 rounded-xl border border-primary/20 flex items-center gap-3">
      <i data-lucide="eye" class="w-5 h-5 shrink-0"></i>
      <div>
        <strong class="font-bold">Modo de Apenas Leitura</strong>
        <p class="text-xs text-muted-foreground mt-0.5">Como Administrador Parcial, você possui permissão para visualizar todos os dados cadastrados, mas as alterações e ações de escrita estão desabilitadas.</p>
      </div>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="bg-destructive/10 text-destructive text-sm p-4 mx-6 mt-6 rounded-xl border border-destructive/20 flex items-center gap-2">
      <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
      <span><?= htmlspecialchars($error) ?></span>
    </div>
  <?php endif; ?>

  <form action="<?= BASE_URL ?>/admin/usuarios/update" method="POST" class="p-6 space-y-4">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

    <div class="grid sm:grid-cols-2 gap-4">
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">Nome Completo *</label>
        <input type="text" name="nome_completo" required value="<?= htmlspecialchars($usuario['nome_completo']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
      </div>
      
      <div>
        <label class="block text-sm font-medium mb-1">CPF *</label>
        <input type="text" id="cpf" name="cpf" required value="<?= htmlspecialchars($usuario['cpf']) ?>" placeholder="000.000.000-00" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
      </div>
      
      <div>
        <label class="block text-sm font-medium mb-1">Celular *</label>
        <input type="text" id="celular" name="celular" required value="<?= htmlspecialchars($usuario['celular']) ?>" placeholder="(00) 00000-0000" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
      </div>
      
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">E-mail *</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($usuario['email']) ?>" placeholder="exemplo@email.com" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
      </div>
      
      <div class="sm:col-span-2 bg-muted/30 p-4 rounded-xl space-y-3 border border-border">
        <h4 class="text-xs font-bold text-muted-foreground uppercase">Alterar Senha (Opcional)</h4>
        <p class="text-xs text-muted-foreground">Deixe os campos de senha em branco caso não queira alterar a senha atual do usuário.</p>
        
        <div class="grid sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold mb-1">Nova Senha</label>
            <input type="password" name="senha" class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm">
          </div>
          <div>
            <label class="block text-xs font-semibold mb-1">Confirmar Nova Senha</label>
            <input type="password" name="senha_confirma" class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm">
          </div>
        </div>
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
            <option value="<?= $p['id'] ?>" <?= ($usuario['perfil_id'] == $p['id']) ? 'selected' : '' ?>>
              <?= $roleName ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Status *</label>
        <select name="status" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
          <option value="ativo" <?= ($usuario['status'] === 'ativo') ? 'selected' : '' ?>>Ativo</option>
          <option value="inativo" <?= ($usuario['status'] === 'inativo') ? 'selected' : '' ?>>Inativo</option>
          <option value="bloqueado" <?= ($usuario['status'] === 'bloqueado') ? 'selected' : '' ?>>Bloqueado</option>
        </select>
      </div>
    </div>

    <div class="flex justify-end gap-3 pt-4 border-t border-border">
      <a href="<?= BASE_URL ?>/admin/usuarios" class="px-6 py-2 border border-border text-foreground rounded-full hover:bg-muted transition-colors">
        Cancelar
      </a>
      <button type="submit" class="px-6 py-2 bg-primary text-primary-foreground font-bold rounded-full hover:bg-primary/90 transition-colors">
        Salvar Alterações
      </button>
    </div>
  </form>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Máscaras simples
    const cpfInput = document.getElementById('cpf');
    if (cpfInput) {
      const maskCpf = (v) => {
        v = v.replace(/\D/g, "");
        v = v.replace(/(\d{3})(\d)/, "$1.$2");
        v = v.replace(/(\d{3})(\d)/, "$1.$2");
        v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        return v.substring(0, 14);
      };
      // Aplica logo no carregamento
      cpfInput.value = maskCpf(cpfInput.value);
      cpfInput.addEventListener('input', function(e) {
        e.target.value = maskCpf(e.target.value);
      });
    }

    const celInput = document.getElementById('celular');
    if (celInput) {
      const maskCel = (v) => {
        v = v.replace(/\D/g, "");
        v = v.replace(/(\d{2})(\d)/, "($1) $2");
        v = v.replace(/(\d{5})(\d)/, "$1-$2");
        return v.substring(0, 15);
      };
      // Aplica no carregamento
      celInput.value = maskCel(celInput.value);
      celInput.addEventListener('input', function(e) {
        e.target.value = maskCel(e.target.value);
      });
    }

    <?php if (isAdminParcial()): ?>
      const form = document.querySelector('form');
      if (form) {
        const inputs = form.querySelectorAll('input, select, textarea, button');
        inputs.forEach(el => {
          if (el.type !== 'hidden' && el.getAttribute('href') === null && !el.classList.contains('bg-card')) {
            el.disabled = true;
          }
        });
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.remove();
      }
    <?php endif; ?>
  });
</script>

<?php require_once __DIR__ . '/../../partials/admin_footer.php'; ?>
