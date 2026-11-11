<?php require_once __DIR__ . '/../../../partials/admin_header.php'; ?>

<div class="bg-card rounded-2xl border border-border overflow-hidden max-w-4xl mx-auto mb-10" style="box-shadow: var(--card-shadow)">
  <div class="p-6 border-b border-border">
    <div class="flex items-center gap-2 text-sm text-muted-foreground mb-2">
      <a href="<?= BASE_URL ?>/admin/conteudos" class="hover:text-primary transition-colors">Conteúdos</a>
      <i data-lucide="chevron-right" class="w-4 h-4"></i>
      <a href="<?= BASE_URL ?>/admin/conteudos/documentos" class="hover:text-primary transition-colors">Documentos Públicos</a>
      <i data-lucide="chevron-right" class="w-4 h-4"></i>
      <span class="text-foreground font-semibold">Editar Documento</span>
    </div>
    <h2 class="text-xl font-bold">Editar Documento Público</h2>
    <p class="text-sm text-muted-foreground">Atualize as informações do documento ou envie um novo arquivo PDF para substituir o atual.</p>
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

  <form action="<?= BASE_URL ?>/admin/conteudos/documentos/update" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $documento['id'] ?>">

    <div class="grid sm:grid-cols-2 gap-6">
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">Título do Documento *</label>
        <input type="text" name="titulo" required value="<?= htmlspecialchars($documento['titulo']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Ex: Ata da Reunião Ordinária - Março 2026">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Tipo de Documento *</label>
        <select name="tipo" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
          <option value="">Selecione</option>
          <option value="ata" <?= ($documento['tipo'] === 'ata') ? 'selected' : '' ?>>Ata</option>
          <option value="resolucao" <?= ($documento['tipo'] === 'resolucao') ? 'selected' : '' ?>>Resolução</option>
          <option value="edital" <?= ($documento['tipo'] === 'edital') ? 'selected' : '' ?>>Edital</option>
          <option value="oficio" <?= ($documento['tipo'] === 'oficio') ? 'selected' : '' ?>>Ofício</option>
          <option value="relatorio" <?= ($documento['tipo'] === 'relatorio') ? 'selected' : '' ?>>Relatório</option>
          <option value="outros" <?= ($documento['tipo'] === 'outros') ? 'selected' : '' ?>>Outros</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Data de Publicação / Emissão *</label>
        <input type="date" name="data_publicacao" required value="<?= htmlspecialchars($documento['data_publicacao']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Substituir Arquivo PDF (Opcional)</label>
        <input type="file" name="caminho_arquivo" accept=".pdf" class="text-sm block w-full text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
        <p class="text-xs text-muted-foreground mt-1">Deixe em branco para manter o arquivo atual.</p>
        <div class="mt-2 text-xs flex items-center gap-1.5 text-primary">
          <i data-lucide="file-text" class="w-4 h-4"></i>
          <span>Arquivo atual: </span>
          <a href="<?= e(BASE_URL . '/' . $documento['caminho_arquivo']) ?>" target="_blank" class="font-bold hover:underline">
            Visualizar PDF
          </a>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Status de Publicação *</label>
        <select name="status" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
          <option value="publicado" <?= ($documento['status'] === 'publicado') ? 'selected' : '' ?>>Publicado (Visível no site)</option>
          <option value="arquivado" <?= ($documento['status'] === 'arquivado') ? 'selected' : '' ?>>Arquivado (Oculto no site)</option>
        </select>
      </div>

      <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">Descrição / Resumo (Opcional)</label>
        <textarea name="descricao" rows="4" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Uma breve descrição sobre o conteúdo do documento..."><?= htmlspecialchars($documento['descricao'] ?? '') ?></textarea>
      </div>
    </div>

    <!-- SUBMIT -->
    <div class="flex justify-end gap-3 pt-6 border-t border-border">
      <a href="<?= BASE_URL ?>/admin/conteudos/documentos" class="px-6 py-2 border border-border text-foreground rounded-full hover:bg-muted transition-colors">
        Cancelar
      </a>
      <button type="submit" class="px-6 py-2 bg-primary text-primary-foreground font-bold rounded-full hover:bg-primary/90 transition-colors">
        Atualizar Documento
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
