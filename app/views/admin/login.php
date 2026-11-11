<?php require_once __DIR__ . '/../partials/header.php'; ?>

  <main class="pt-16 min-h-screen flex flex-col">
    <!-- VIEW DE LOGIN -->
    <div id="login-view" class="flex-1 flex flex-col">
      <section class="py-16 bg-card border-b border-border">
        <div class="container mx-auto px-4 text-center">
          <h1 class="text-3xl md:text-5xl font-black text-foreground mb-4">Área Administrativa</h1>
          <p class="text-lg md:text-xl text-muted-foreground max-w-2xl mx-auto">Acesso restrito aos membros do conselho</p>
        </div>
      </section>
      <section class="py-20 bg-background flex-1 flex items-center justify-center">
        <div class="container mx-auto px-4 w-full max-w-md">
          <div class="bg-card rounded-2xl p-8 border border-border" style="box-shadow: var(--card-shadow)">
            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-6">
              <i data-lucide="lock" class="w-8 h-8 text-primary"></i>
            </div>
            <h2 class="text-2xl font-bold text-center mb-6">Login Administrativo</h2>
            
            <form id="admin-login-form" action="<?= BASE_URL ?>/admin/login" method="POST" class="space-y-4">
              <?= csrf_field() ?>
              
              <?php if (!empty($error)): ?>
                <div class="bg-destructive/10 text-destructive text-sm p-3 rounded-xl border border-destructive/20 mb-4">
                  <?= htmlspecialchars($error) ?>
                </div>
              <?php endif; ?>

              <div>
                <label class="block text-sm font-medium mb-1">E-mail ou CPF</label>
                <div class="relative">
                  <i data-lucide="user" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                  <input type="text" name="login" required class="w-full pl-10 pr-4 py-2 rounded-xl border border-border bg-background focus:outline-none focus:ring-2 focus:ring-primary" placeholder="admin@admin.com ou CPF">
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Senha</label>
                <div class="relative">
                  <i data-lucide="key" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                  <input type="password" name="senha" required class="w-full pl-10 pr-4 py-2 rounded-xl border border-border bg-background focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Sua senha">
                </div>
              </div>
              <button type="submit" class="w-full bg-primary text-primary-foreground font-bold py-3 rounded-xl hover:bg-primary/90 transition-colors mt-6">
                Entrar no Sistema
              </button>
            </form>
          </div>
        </div>
      </section>
    </div>
  </main>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
