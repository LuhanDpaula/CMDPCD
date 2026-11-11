<?php require_once __DIR__ . '/../../../partials/admin_header.php'; ?>

<div class="bg-card rounded-2xl border border-border overflow-hidden max-w-4xl mx-auto mb-10" style="box-shadow: var(--card-shadow)">
  <div class="p-6 border-b border-border">
    <div class="flex items-center gap-2 text-sm text-muted-foreground mb-2">
      <a href="<?= BASE_URL ?>/admin/conteudos" class="hover:text-primary transition-colors">Conteúdos</a>
      <i data-lucide="chevron-right" class="w-4 h-4"></i>
      <a href="<?= BASE_URL ?>/admin/conteudos/noticias" class="hover:text-primary transition-colors">Notícias</a>
      <i data-lucide="chevron-right" class="w-4 h-4"></i>
      <span class="text-foreground font-semibold">Nova Notícia</span>
    </div>
    <h2 class="text-xl font-bold">Cadastrar Nova Notícia</h2>
    <p class="text-sm text-muted-foreground">Publique informativos, comunicados ou novidades no site.</p>
  </div>

  <?php if (!empty($error)): ?>
    <div class="bg-destructive/10 text-destructive text-sm p-4 mx-6 mt-6 rounded-xl border border-destructive/20 flex items-center gap-2">
      <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
      <span><?= htmlspecialchars($error) ?></span>
    </div>
  <?php endif; ?>

  <form action="<?= BASE_URL ?>/admin/conteudos/noticias/store" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
    <?= csrf_field() ?>
    
    <div class="grid sm:grid-cols-2 gap-6">
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">Título da Notícia *</label>
        <input type="text" name="titulo" required value="<?= htmlspecialchars($old['titulo'] ?? '') ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Tema / Categoria *</label>
        <select name="tema" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
          <option value="">Selecione</option>
          <option value="eventos" <?= (isset($old['tema']) && $old['tema'] === 'eventos') ? 'selected' : '' ?>>Eventos</option>
          <option value="direitos" <?= (isset($old['tema']) && $old['tema'] === 'direitos') ? 'selected' : '' ?>>Direitos</option>
          <option value="inclusao" <?= (isset($old['tema']) && $old['tema'] === 'inclusao') ? 'selected' : '' ?>>Inclusão</option>
          <option value="saude" <?= (isset($old['tema']) && $old['tema'] === 'saude') ? 'selected' : '' ?>>Saúde</option>
          <option value="educacao" <?= (isset($old['tema']) && $old['tema'] === 'educacao') ? 'selected' : '' ?>>Educação</option>
          <option value="acessibilidade" <?= (isset($old['tema']) && $old['tema'] === 'acessibilidade') ? 'selected' : '' ?>>Acessibilidade</option>
          <option value="emprego" <?= (isset($old['tema']) && $old['tema'] === 'emprego') ? 'selected' : '' ?>>Emprego</option>
          <option value="informativos" <?= (isset($old['tema']) && $old['tema'] === 'informativos') ? 'selected' : '' ?>>Informativos</option>
          <option value="outros" <?= (isset($old['tema']) && $old['tema'] === 'outros') ? 'selected' : '' ?>>Outros</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Imagem de Capa</label>
        <input type="file" name="imagem_capa" accept=".jpg,.jpeg,.png,.gif" class="text-sm block w-full text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
        <p class="text-xs text-muted-foreground mt-1">Formatos aceitos: JPG, JPEG, PNG, GIF. Máx 5MB.</p>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Status de Publicação *</label>
        <select name="status" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
          <option value="rascunho" <?= (isset($old['status']) && $old['status'] === 'rascunho') ? 'selected' : '' ?>>Rascunho</option>
          <option value="publicado" <?= (isset($old['status']) && $old['status'] === 'publicado') ? 'selected' : '' ?>>Publicado</option>
          <option value="arquivado" <?= (isset($old['status']) && $old['status'] === 'arquivado') ? 'selected' : '' ?>>Arquivado</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Agendar Publicação (Opcional)</label>
        <input type="datetime-local" name="data_publicacao" value="<?= htmlspecialchars($old['data_publicacao'] ?? '') ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        <p class="text-xs text-muted-foreground mt-1">Se deixado em branco, será publicado imediatamente.</p>
      </div>

      <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">Conteúdo da Notícia *</label>
        <textarea name="conteudo" required rows="10" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Escreva o texto completo da notícia aqui..."><?= htmlspecialchars($old['conteudo'] ?? '') ?></textarea>
      </div>
    </div>

    <!-- SUBMIT -->
    <div class="flex justify-end gap-3 pt-6 border-t border-border">
      <a href="<?= BASE_URL ?>/admin/conteudos/noticias" class="px-6 py-2 border border-border text-foreground rounded-full hover:bg-muted transition-colors">
        Cancelar
      </a>
      <button type="submit" class="px-6 py-2 bg-primary text-primary-foreground font-bold rounded-full hover:bg-primary/90 transition-colors">
        Publicar / Salvar
      </button>
    </div>

  </form>
</div>

<?php require_once __DIR__ . '/../../../partials/admin_footer.php'; ?>
