<?php require_once __DIR__ . '/partials/header.php'; ?>

  <main class="pt-16">
    <!-- PageHero -->
    <section class="py-16 md:py-24 bg-card border-b border-border">
      <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl md:text-5xl font-black text-foreground mb-4 fade-in-up">Apoie o CMPCD Jahu</h1>
        <p class="text-lg md:text-xl text-muted-foreground max-w-2xl mx-auto fade-in-up">Saiba como você pode contribuir para a inclusão e acessibilidade</p>
      </div>
    </section>

    <!-- Por que apoiar -->
    <section class="py-20 bg-background">
      <div class="container mx-auto px-4 max-w-5xl">
        <h2 class="text-2xl md:text-3xl font-extrabold text-foreground text-center mb-12 fade-in-up">Por que nos apoiar?</h2>
        <div class="grid sm:grid-cols-2 gap-6">
          <div class="bg-card rounded-2xl p-6 border border-border hover:border-primary/30 transition-all fade-in-up" style="box-shadow: var(--card-shadow)">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center mb-4">
              <i data-lucide="heart" class="w-6 h-6 text-primary"></i>
            </div>
            <h3 class="font-bold text-foreground mb-2">Impacto Real</h3>
            <p class="text-sm text-muted-foreground leading-relaxed">Seu apoio contribui diretamente para a melhoria da qualidade de vida de mais de 7 mil pessoas com deficiência em Jahu.</p>
          </div>
          <div class="bg-card rounded-2xl p-6 border border-border hover:border-primary/30 transition-all fade-in-up" style="box-shadow: var(--card-shadow)">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center mb-4">
              <i data-lucide="users" class="w-6 h-6 text-primary"></i>
            </div>
            <h3 class="font-bold text-foreground mb-2">Fortalecimento Social</h3>
            <p class="text-sm text-muted-foreground leading-relaxed">Ajude a fortalecer uma rede de proteção e inclusão que beneficia famílias inteiras e a comunidade.</p>
          </div>
          <div class="bg-card rounded-2xl p-6 border border-border hover:border-primary/30 transition-all fade-in-up" style="box-shadow: var(--card-shadow)">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center mb-4">
              <i data-lucide="hand-helping" class="w-6 h-6 text-primary"></i>
            </div>
            <h3 class="font-bold text-foreground mb-2">Cidadania Ativa</h3>
            <p class="text-sm text-muted-foreground leading-relaxed">Ao apoiar o CMPCD, você exerce cidadania e contribui para uma sociedade mais justa e acessível.</p>
          </div>
          <div class="bg-card rounded-2xl p-6 border border-border hover:border-primary/30 transition-all fade-in-up" style="box-shadow: var(--card-shadow)">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center mb-4">
              <i data-lucide="building-2" class="w-6 h-6 text-primary"></i>
            </div>
            <h3 class="font-bold text-foreground mb-2">Transparência</h3>
            <p class="text-sm text-muted-foreground leading-relaxed">O conselho presta contas de todas as ações e recursos, garantindo transparência e responsabilidade.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Como apoiar -->
    <section class="py-20 bg-card">
      <div class="container mx-auto px-4 max-w-4xl">
        <h2 class="text-2xl md:text-3xl font-extrabold text-foreground text-center mb-12 fade-in-up">Como apoiar</h2>
        <div class="grid md:grid-cols-2 gap-8">
          <!-- PIX -->
          <div class="bg-background rounded-2xl p-8 border border-border text-center fade-in-left" style="box-shadow: var(--card-shadow)">
            <i data-lucide="qr-code" class="w-16 h-16 text-primary mx-auto mb-4"></i>
            <h3 class="text-xl font-bold text-foreground mb-2">Doação via PIX</h3>
            <p class="text-muted-foreground mb-4">Contribua com qualquer valor através da chave PIX do conselho.</p>
            <div class="bg-card rounded-xl p-4 border border-border">
              <p class="text-sm text-muted-foreground">Chave PIX (CNPJ):</p>
              <p class="font-bold text-primary text-lg">00.000.000/0001-00</p>
            </div>
          </div>
          <!-- Outros -->
          <div class="bg-background rounded-2xl p-8 border border-border fade-in-right" style="box-shadow: var(--card-shadow)">
            <h3 class="text-xl font-bold text-foreground mb-4">Outras Formas de Apoio</h3>
            <ul class="space-y-4">
              <li class="flex items-start gap-3">
                <div class="w-2 h-2 rounded-full bg-primary mt-2 shrink-0"></div>
                <div>
                  <strong class="text-foreground">Apoio presencial:</strong>
                  <p class="text-sm text-muted-foreground">Visite nossa sede e conheça nossos projetos.</p>
                </div>
              </li>
              <li class="flex items-start gap-3">
                <div class="w-2 h-2 rounded-full bg-primary mt-2 shrink-0"></div>
                <div>
                  <strong class="text-foreground">Parcerias institucionais:</strong>
                  <p class="text-sm text-muted-foreground">Empresas e organizações podem firmar parcerias de apoio.</p>
                </div>
              </li>
              <li class="flex items-start gap-3">
                <div class="w-2 h-2 rounded-full bg-primary mt-2 shrink-0"></div>
                <div>
                  <strong class="text-foreground">Voluntariado:</strong>
                  <p class="text-sm text-muted-foreground">Contribua com seu tempo e habilidades em nossos projetos.</p>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <div class="text-center mt-12 fade-in-up">
          <a href="<?= BASE_URL ?>/contato" class="inline-flex items-center gap-2 bg-primary text-primary-foreground font-bold px-8 py-3 rounded-full hover:bg-primary/90 transition-colors">
            Entre em contato <i data-lucide="arrow-right" class="w-4 h-4"></i>
          </a>
        </div>
      </div>
    </section>

  </main>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
