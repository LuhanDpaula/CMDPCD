<?php require_once __DIR__ . '/../../../partials/admin_header.php'; ?>

<div class="bg-card rounded-2xl border border-border overflow-hidden max-w-4xl mx-auto mb-10" style="box-shadow: var(--card-shadow)">
  <div class="p-6 border-b border-border">
    <div class="flex items-center gap-2 text-sm text-muted-foreground mb-2">
      <a href="<?= BASE_URL ?>/admin/conteudos" class="hover:text-primary transition-colors">Conteúdos</a>
      <i data-lucide="chevron-right" class="w-4 h-4"></i>
      <a href="<?= BASE_URL ?>/admin/conteudos/membros" class="hover:text-primary transition-colors">Membros e Presidentes</a>
      <i data-lucide="chevron-right" class="w-4 h-4"></i>
      <span class="text-foreground font-semibold">Editar Membro / Presidente</span>
    </div>
    <h2 class="text-xl font-bold">Editar Membro / Presidente</h2>
    <p class="text-sm text-muted-foreground">Atualize as informações de cadastro.</p>
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

  <form action="<?= BASE_URL ?>/admin/conteudos/membros/update" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $membro['id'] ?>">
    
    <div class="grid sm:grid-cols-2 gap-6">
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">Nome Completo *</label>
        <input type="text" name="nome_completo" required value="<?= htmlspecialchars($membro['nome_completo']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Função no Conselho *</label>
        <select name="funcao" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
          <option value="">Selecione</option>
          <option value="presidente" <?= ($membro['funcao'] === 'presidente') ? 'selected' : '' ?>>Presidente</option>
          <option value="vice-presidente" <?= ($membro['funcao'] === 'vice-presidente') ? 'selected' : '' ?>>Vice-Presidente</option>
          <option value="secretario" <?= ($membro['funcao'] === 'secretario') ? 'selected' : '' ?>>Secretário(a)</option>
          <option value="titular" <?= ($membro['funcao'] === 'titular') ? 'selected' : '' ?>>Conselheiro(a) Titular</option>
          <option value="suplente" <?= ($membro['funcao'] === 'suplente') ? 'selected' : '' ?>>Conselheiro(a) Suplente</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Entidade / Órgão Representado</label>
        <input type="text" name="entidade_representada" value="<?= htmlspecialchars($membro['entidade_representada'] ?? '') ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Ex: Poder Público, Sociedade Civil, APAE, etc.">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Foto do Integrante</label>
        <?php if ($membro['foto']): ?>
          <div class="mb-2 p-2 bg-muted rounded-xl flex items-center justify-between text-xs border border-border max-w-sm">
            <div class="flex items-center gap-2 truncate">
              <img src="<?= BASE_URL . '/' . htmlspecialchars($membro['foto']) ?>" alt="Foto" class="w-8 h-8 object-cover rounded-full">
              <span class="truncate font-medium text-muted-foreground">Foto atual</span>
            </div>
            <a href="<?= BASE_URL . '/' . htmlspecialchars($membro['foto']) ?>" target="_blank" class="text-primary font-bold hover:underline inline-flex items-center gap-1">
              Visualizar
            </a>
          </div>
        <?php endif; ?>
        <input type="file" name="foto" accept=".jpg,.jpeg,.png,.gif" class="text-sm block w-full text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Início do Mandato *</label>
        <input type="date" name="data_inicio" required value="<?= htmlspecialchars($membro['data_inicio']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Fim do Mandato (Deixe em branco se atual)</label>
        <input type="date" name="data_fim" value="<?= htmlspecialchars($membro['data_fim'] ?? '') ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
      </div>

      <div class="flex items-center gap-2 pt-2">
        <input type="checkbox" id="ativo" name="ativo" value="1" <?= $membro['ativo'] ? 'checked' : '' ?> class="w-4 h-4 accent-primary">
        <label for="ativo" class="text-sm font-medium text-foreground cursor-pointer">Membro Ativo Atual (Exibir na aba de composição atual)</label>
      </div>

      <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">Biografia / Resumo de Contribuições (Exibido na história dos presidentes)</label>
        <textarea name="biografia" rows="6" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Escreva a biografia ou resumo das conquistas deste mandato..."><?= htmlspecialchars($membro['biografia'] ?? '') ?></textarea>
      </div>
    </div>

    <!-- SUBMIT -->
    <div class="flex justify-end gap-3 pt-6 border-t border-border">
      <a href="<?= BASE_URL ?>/admin/conteudos/membros" class="px-6 py-2 border border-border text-foreground rounded-full hover:bg-muted transition-colors">
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

<?php require_once __DIR__ . '/../../../partials/admin_footer.php'; ?>
