<?php require_once __DIR__ . '/partials/header.php'; ?>

  <main class="pt-16 min-h-[70vh] flex items-center justify-center bg-background">
    <div class="container px-4 text-center zoom-in">
      <div class="w-24 h-24 rounded-full bg-destructive/10 flex items-center justify-center mx-auto mb-6">
        <i data-lucide="alert-triangle" class="w-12 h-12 text-destructive"></i>
      </div>
      <h1 class="text-6xl font-black text-foreground mb-4">404</h1>
      <h2 class="text-2xl font-extrabold text-foreground mb-4">Página Não Encontrada</h2>
      <p class="text-muted-foreground max-w-md mx-auto mb-8">
        Desculpe, a página que você está procurando não existe ou foi movida.
      </p>
      <a href="<?= BASE_URL ?>/" class="inline-flex items-center gap-2 bg-primary text-primary-foreground font-bold px-6 py-3 rounded-full hover:bg-primary/90 transition-colors">
        <i data-lucide="arrow-left" class="w-5 h-5"></i> Voltar ao Início
      </a>
    </div>
  </main>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
