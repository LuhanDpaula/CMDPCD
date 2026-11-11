<?php require_once __DIR__ . '/../partials/admin_header.php'; ?>

<div class="space-y-12">
  <div class="flex items-center justify-between border-b border-border pb-4">
    <div>
      <h2 class="text-2xl font-black text-foreground">Gerenciador de Conteúdos</h2>
      <p class="text-sm text-muted-foreground">Escolha qual tipo de conteúdo deseja cadastrar, editar ou excluir no site institucional.</p>
    </div>
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

  <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
    <!-- Card 1: Notícias -->
    <div class="bg-card rounded-2xl border border-border p-6 flex flex-col justify-between hover:border-primary/30 transition-all duration-300" style="box-shadow: var(--card-shadow)">
      <div class="space-y-4">
        <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
          <i data-lucide="newspaper" class="w-6 h-6 text-primary"></i>
        </div>
        <div>
          <h3 class="text-xl font-bold text-foreground">Notícias do Portal</h3>
          <p class="text-sm text-muted-foreground mt-1">
            Publique novidades, comunicados, campanhas de conscientização e informativos de utilidade pública no feed de notícias do site.
          </p>
        </div>
        <div class="bg-muted/40 p-4 rounded-xl flex items-center justify-between text-sm">
          <span class="text-muted-foreground">Notícias cadastradas:</span>
          <strong class="text-foreground text-lg"><?= $noticiasCount ?></strong>
        </div>
      </div>
      <div class="pt-6">
        <a href="<?= BASE_URL ?>/admin/conteudos/noticias" class="w-full justify-center px-6 py-2.5 bg-primary text-primary-foreground font-bold rounded-full hover:bg-primary/90 transition-colors flex items-center gap-2 text-sm">
          Gerenciar Notícias <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
      </div>
    </div>

    <!-- Card: Perguntas Frequentes do Chat (MongoDB) -->
    <?php if (isAdminTotal() || isEditor()): ?>
    <div class="bg-card rounded-2xl border border-border p-6 flex flex-col justify-between hover:border-primary/30 transition-all duration-300" style="box-shadow: var(--card-shadow)">
      <div class="space-y-4">
        <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
          <i data-lucide="message-circle-question" class="w-6 h-6 text-primary"></i>
        </div>
        <div>
          <h3 class="text-xl font-bold text-foreground">Perguntas Frequentes (Chat)</h3>
          <p class="text-sm text-muted-foreground mt-1">
            Cadastre as perguntas e respostas do botão "Posso ajudar?" do site e veja o que as pessoas perguntaram e o chat não soube responder.
          </p>
        </div>
        <div class="bg-muted/40 p-4 rounded-xl flex items-center justify-between text-sm">
          <span class="text-muted-foreground">Armazenamento:</span>
          <strong class="text-foreground">MongoDB</strong>
        </div>
      </div>
      <div class="pt-6">
        <a href="<?= BASE_URL ?>/admin/conteudos/faq" class="w-full justify-center px-6 py-2.5 bg-primary text-primary-foreground font-bold rounded-full hover:bg-primary/90 transition-colors flex items-center gap-2 text-sm">
          Gerenciar Perguntas <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
      </div>
    </div>
    <?php endif; ?>

    <!-- Card 2: Membros e Presidentes -->
    <div class="bg-card rounded-2xl border border-border p-6 flex flex-col justify-between hover:border-primary/30 transition-all duration-300" style="box-shadow: var(--card-shadow)">
      <div class="space-y-4">
        <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
          <i data-lucide="users" class="w-6 h-6 text-primary"></i>
        </div>
        <div>
          <h3 class="text-xl font-bold text-foreground">Membros e Presidentes</h3>
          <p class="text-sm text-muted-foreground mt-1">
            Cadastre os membros atuais do conselho municipal e também mantenha atualizado o histórico cronológico de todos os presidentes.
          </p>
        </div>
        <div class="grid grid-cols-2 gap-4 bg-muted/40 p-4 rounded-xl text-sm">
          <div>
            <span class="text-xs text-muted-foreground block">Membros Ativos:</span>
            <strong class="text-foreground text-base"><?= $membrosCount ?></strong>
          </div>
          <div>
            <span class="text-xs text-muted-foreground block">Galeria de Presidentes:</span>
            <strong class="text-foreground text-base"><?= $presidentesCount ?></strong>
          </div>
        </div>
      </div>
      <div class="pt-6">
        <a href="<?= BASE_URL ?>/admin/conteudos/membros" class="w-full justify-center px-6 py-2.5 bg-primary text-primary-foreground font-bold rounded-full hover:bg-primary/90 transition-colors flex items-center gap-2 text-sm">
          Gerenciar Membros <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
      </div>
    </div>

    <!-- Card 3: Documentos Públicos (Transparência) -->
    <div class="bg-card rounded-2xl border border-border p-6 flex flex-col justify-between hover:border-primary/30 transition-all duration-300" style="box-shadow: var(--card-shadow)">
      <div class="space-y-4">
        <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
          <i data-lucide="file-text" class="w-6 h-6 text-primary"></i>
        </div>
        <div>
          <h3 class="text-xl font-bold text-foreground">Documentos da Transparência</h3>
          <p class="text-sm text-muted-foreground mt-1">
            Publique atas de reuniões, resoluções, editais, ofícios e relatórios anuais em PDF para consulta pública.
          </p>
        </div>
        <div class="bg-muted/40 p-4 rounded-xl flex items-center justify-between text-sm">
          <span class="text-muted-foreground">Documentos cadastrados:</span>
          <strong class="text-foreground text-lg"><?= $documentosCount ?></strong>
        </div>
      </div>
      <div class="pt-6">
        <a href="<?= BASE_URL ?>/admin/conteudos/documentos" class="w-full justify-center px-6 py-2.5 bg-primary text-primary-foreground font-bold rounded-full hover:bg-primary/90 transition-colors flex items-center gap-2 text-sm">
          Gerenciar Documentos <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../partials/admin_footer.php'; ?>
