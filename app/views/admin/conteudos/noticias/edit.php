<?php require_once __DIR__ . '/../../../partials/admin_header.php'; ?>

<div class="bg-card rounded-2xl border border-border overflow-hidden max-w-4xl mx-auto mb-10" style="box-shadow: var(--card-shadow)">
  <div class="p-6 border-b border-border">
    <div class="flex items-center gap-2 text-sm text-muted-foreground mb-2">
      <a href="<?= BASE_URL ?>/admin/conteudos" class="hover:text-primary transition-colors">Conteúdos</a>
      <i data-lucide="chevron-right" class="w-4 h-4"></i>
      <a href="<?= BASE_URL ?>/admin/conteudos/noticias" class="hover:text-primary transition-colors">Notícias</a>
      <i data-lucide="chevron-right" class="w-4 h-4"></i>
      <span class="text-foreground font-semibold">Editar Notícia</span>
    </div>
    <h2 class="text-xl font-bold">Editar Notícia</h2>
    <p class="text-sm text-muted-foreground">Atualize as informações da publicação.</p>
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

  <form action="<?= BASE_URL ?>/admin/conteudos/noticias/update" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $noticia['id'] ?>">
    
    <div class="grid sm:grid-cols-2 gap-6">
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">Título da Notícia *</label>
        <input type="text" name="titulo" required value="<?= htmlspecialchars($noticia['titulo']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Tema / Categoria *</label>
        <select name="tema" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
          <option value="">Selecione</option>
          <option value="eventos" <?= ($noticia['tema'] === 'eventos') ? 'selected' : '' ?>>Eventos</option>
          <option value="direitos" <?= ($noticia['tema'] === 'direitos') ? 'selected' : '' ?>>Direitos</option>
          <option value="inclusao" <?= ($noticia['tema'] === 'inclusao') ? 'selected' : '' ?>>Inclusão</option>
          <option value="saude" <?= ($noticia['tema'] === 'saude') ? 'selected' : '' ?>>Saúde</option>
          <option value="educacao" <?= ($noticia['tema'] === 'educacao') ? 'selected' : '' ?>>Educação</option>
          <option value="acessibilidade" <?= ($noticia['tema'] === 'acessibilidade') ? 'selected' : '' ?>>Acessibilidade</option>
          <option value="emprego" <?= ($noticia['tema'] === 'emprego') ? 'selected' : '' ?>>Emprego</option>
          <option value="informativos" <?= ($noticia['tema'] === 'informativos') ? 'selected' : '' ?>>Informativos</option>
          <option value="outros" <?= ($noticia['tema'] === 'outros') ? 'selected' : '' ?>>Outros</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Imagem de Capa</label>
        <?php if ($noticia['imagem_capa']): ?>
          <div class="mb-2 p-2 bg-muted rounded-xl flex items-center justify-between text-xs border border-border max-w-sm">
            <div class="flex items-center gap-2 truncate">
              <img src="<?= BASE_URL . '/' . htmlspecialchars($noticia['imagem_capa']) ?>" alt="Capa" class="w-10 h-7 object-cover rounded">
              <span class="truncate font-medium text-muted-foreground">Imagem atual</span>
            </div>
            <a href="<?= BASE_URL . '/' . htmlspecialchars($noticia['imagem_capa']) ?>" target="_blank" class="text-primary font-bold hover:underline inline-flex items-center gap-1">
              Visualizar
            </a>
          </div>
        <?php endif; ?>
        <input type="file" name="imagem_capa" accept=".jpg,.jpeg,.png,.gif" class="text-sm block w-full text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
        <p class="text-xs text-muted-foreground mt-1">Deixe em branco para manter a imagem atual.</p>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Status de Publicação *</label>
        <select name="status" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
          <option value="rascunho" <?= ($noticia['status'] === 'rascunho') ? 'selected' : '' ?>>Rascunho</option>
          <option value="publicado" <?= ($noticia['status'] === 'publicado') ? 'selected' : '' ?>>Publicado</option>
          <option value="arquivado" <?= ($noticia['status'] === 'arquivado') ? 'selected' : '' ?>>Arquivado</option>
        </select>
      </div>

      <div>
        <?php 
          // Formata data_publicacao para o input datetime-local
          $dataPubVal = '';
          if ($noticia['data_publicacao']) {
            $dataPubVal = date('Y-m-d\TH:i', strtotime($noticia['data_publicacao']));
          }
        ?>
        <label class="block text-sm font-medium mb-1">Agendar Publicação (Opcional)</label>
        <input type="datetime-local" name="data_publicacao" value="<?= htmlspecialchars($dataPubVal) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
      </div>

      <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">Conteúdo da Notícia *</label>
        <textarea name="conteudo" required rows="10" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Escreva o texto completo da notícia aqui..."><?= htmlspecialchars($noticia['conteudo']) ?></textarea>
      </div>
    </div>

    <!-- SUBMIT -->
    <div class="flex justify-end gap-3 pt-6 border-t border-border">
      <a href="<?= BASE_URL ?>/admin/conteudos/noticias" class="px-6 py-2 border border-border text-foreground rounded-full hover:bg-muted transition-colors">
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
