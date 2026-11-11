<?php require_once __DIR__ . '/partials/header.php'; ?>

  <!-- CONTEÚDO PRINCIPAL (INDEX) -->
  <main class="pt-16">
    
    <!-- HeroSection -->
    <section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden" style="background: var(--hero-gradient)">
      <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
        <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-primary-foreground/10"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full bg-primary-foreground/10"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full bg-primary-foreground/5"></div>
      </div>

      <div class="container mx-auto relative z-10 text-center px-4">
        <div class="fade-in-up">
          <div class="inline-flex items-center gap-2 bg-primary-foreground/15 rounded-full px-5 py-2 mb-8">
            <i data-lucide="heart" class="w-4 h-4 text-primary-foreground" aria-hidden="true"></i>
            <span class="text-sm font-semibold text-primary-foreground">Inclusão, Acessibilidade e Cidadania</span>
          </div>

          <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black text-primary-foreground leading-tight max-w-5xl mx-auto">
            Conselho Municipal dos Direitos da Pessoa com Deficiência de Jahu
          </h1>

          <p class="mt-6 text-lg md:text-xl text-primary-foreground/85 max-w-2xl mx-auto font-medium">
            Promovendo inclusão, acessibilidade e cidadania para todas as pessoas
          </p>

          <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
            <a href="<?= BASE_URL ?>/quem-somos" class="inline-flex items-center gap-2 bg-primary-foreground text-primary font-bold px-6 py-3 rounded-full hover:shadow-lg transition-all hover:scale-105">
              <i data-lucide="users" class="w-5 h-5" aria-hidden="true"></i> Conheça o Conselho
            </a>
            <a href="<?= BASE_URL ?>/cadastro-pcd" class="inline-flex items-center gap-2 bg-primary-foreground/20 text-primary-foreground font-bold px-6 py-3 rounded-full hover:bg-primary-foreground/30 transition-all border border-primary-foreground/30">
              <i data-lucide="clipboard-list" class="w-5 h-5" aria-hidden="true"></i> Registrar PCD
            </a>
            <a href="<?= BASE_URL ?>/noticias" class="inline-flex items-center gap-2 bg-primary-foreground/20 text-primary-foreground font-bold px-6 py-3 rounded-full hover:bg-primary-foreground/30 transition-all border border-primary-foreground/30">
              <i data-lucide="file-text" class="w-5 h-5" aria-hidden="true"></i> Notícias
            </a>
            <a href="<?= BASE_URL ?>/transparencia" class="inline-flex items-center gap-2 bg-primary-foreground/20 text-primary-foreground font-bold px-6 py-3 rounded-full hover:bg-primary-foreground/30 transition-all border border-primary-foreground/30">
              <i data-lucide="log-in" class="w-5 h-5" aria-hidden="true"></i> Transparência
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- HomeAbout -->
    <section class="py-20 md:py-28 bg-background">
      <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-12 fade-in-up">
          <h2 class="text-2xl md:text-4xl font-extrabold text-foreground mb-4">Sobre o Conselho</h2>
          <div class="w-16 h-1 bg-primary rounded-full mx-auto mb-6"></div>
        </div>

        <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
          <div class="bg-card rounded-2xl p-8 border border-border fade-in-left" style="box-shadow: var(--card-shadow)">
            <i data-lucide="shield" class="w-10 h-10 text-primary mb-4" aria-hidden="true"></i>
            <h3 class="text-lg font-bold text-foreground mb-3">O que é o CMPCD?</h3>
            <p class="text-muted-foreground leading-relaxed">
              O CMPCD Jahu é um órgão colegiado de caráter consultivo, deliberativo e fiscalizador, responsável pela formulação e acompanhamento de políticas públicas voltadas às pessoas com deficiência. Atua em parceria com a Prefeitura Municipal e a Secretaria de Assistência e Desenvolvimento Social.
            </p>
          </div>
          <div class="bg-card rounded-2xl p-8 border border-border fade-in-right" style="box-shadow: var(--card-shadow)">
            <i data-lucide="users" class="w-10 h-10 text-primary mb-4" aria-hidden="true"></i>
            <h3 class="text-lg font-bold text-foreground mb-3">Nossa Missão</h3>
            <p class="text-muted-foreground leading-relaxed">
              Garantir direitos, promover inclusão social e contribuir para a construção de uma sociedade mais acessível e igualitária. Trabalhamos para que cada pessoa com deficiência tenha voz ativa e participação plena na vida comunitária.
            </p>
          </div>
        </div>

        <div class="text-center mt-10">
          <a href="<?= BASE_URL ?>/quem-somos" class="inline-flex items-center gap-2 text-primary font-bold hover:underline">
            Saiba mais sobre nós <i data-lucide="arrow-right" class="w-4 h-4"></i>
          </a>
        </div>
      </div>
    </section>

    <!-- HomeObjectives -->
    <section class="py-20 md:py-28 bg-card">
      <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-12 fade-in-up">
          <h2 class="text-2xl md:text-4xl font-extrabold text-foreground mb-4">Nossos Objetivos</h2>
          <div class="w-16 h-1 bg-primary rounded-full mx-auto mb-6"></div>
          <p class="text-muted-foreground text-lg">Desenvolver e apoiar políticas públicas que garantam a inclusão e qualidade de vida das pessoas com deficiência.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
          <div class="group bg-background rounded-2xl p-6 border border-border hover:border-primary/30 hover:-translate-y-1 transition-all duration-300 fade-in-up" style="box-shadow: var(--card-shadow)">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center mb-4 group-hover:bg-primary/20 transition-colors">
              <i data-lucide="hand-helping" class="w-6 h-6 text-primary" aria-hidden="true"></i>
            </div>
            <h3 class="font-bold text-foreground mb-2">Promover a inclusão social</h3>
            <p class="text-sm text-muted-foreground leading-relaxed">Desenvolver ações que garantam a participação plena das pessoas com deficiência na vida em comunidade.</p>
          </div>
          <div class="group bg-background rounded-2xl p-6 border border-border hover:border-primary/30 hover:-translate-y-1 transition-all duration-300 fade-in-up" style="box-shadow: var(--card-shadow)">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center mb-4 group-hover:bg-primary/20 transition-colors">
              <i data-lucide="eye" class="w-6 h-6 text-primary" aria-hidden="true"></i>
            </div>
            <h3 class="font-bold text-foreground mb-2">Fiscalizar políticas públicas</h3>
            <p class="text-sm text-muted-foreground leading-relaxed">Acompanhar e monitorar a implementação de políticas voltadas à acessibilidade e inclusão.</p>
          </div>
          <div class="group bg-background rounded-2xl p-6 border border-border hover:border-primary/30 hover:-translate-y-1 transition-all duration-300 fade-in-up" style="box-shadow: var(--card-shadow)">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center mb-4 group-hover:bg-primary/20 transition-colors">
              <i data-lucide="scale" class="w-6 h-6 text-primary" aria-hidden="true"></i>
            </div>
            <h3 class="font-bold text-foreground mb-2">Garantir direitos</h3>
            <p class="text-sm text-muted-foreground leading-relaxed">Assegurar que os direitos das pessoas com deficiência sejam respeitados e cumpridos.</p>
          </div>
          <div class="group bg-background rounded-2xl p-6 border border-border hover:border-primary/30 hover:-translate-y-1 transition-all duration-300 fade-in-up" style="box-shadow: var(--card-shadow)">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center mb-4 group-hover:bg-primary/20 transition-colors">
              <i data-lucide="target" class="w-6 h-6 text-primary" aria-hidden="true"></i>
            </div>
            <h3 class="font-bold text-foreground mb-2">Apoiar a Assistência Social</h3>
            <p class="text-sm text-muted-foreground leading-relaxed">Colaborar com a Secretaria de Assistência Social em ações inclusivas e de desenvolvimento.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- HomeStats -->
    <section class="py-20 md:py-28 bg-background">
      <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-12 fade-in-up">
          <h2 class="text-2xl md:text-4xl font-extrabold text-foreground mb-4">Dados do Município</h2>
          <div class="w-16 h-1 bg-primary rounded-full mx-auto"></div>
        </div>

        <div class="max-w-4xl mx-auto rounded-3xl p-8 md:p-12 text-center zoom-in" style="background: var(--hero-gradient)">
          <div class="grid sm:grid-cols-3 gap-8 mb-10">
            <div class="flex flex-col items-center">
              <i data-lucide="users" class="w-10 h-10 text-primary-foreground/80 mb-3" aria-hidden="true"></i>
              <span class="text-4xl md:text-5xl font-black text-primary-foreground"><?= number_format($stats['total_pcds'], 0, ',', '.') ?></span>
              <span class="text-sm text-primary-foreground/80 mt-1 font-semibold">Pessoas com deficiência</span>
            </div>
            <div class="flex flex-col items-center">
              <i data-lucide="wallet" class="w-10 h-10 text-primary-foreground/80 mb-3" aria-hidden="true"></i>
              <span class="text-4xl md:text-5xl font-black text-primary-foreground"><?= number_format($stats['total_bpc'], 0, ',', '.') ?></span>
              <span class="text-sm text-primary-foreground/80 mt-1 font-semibold">Recebem BPC/LOAS</span>
            </div>
            <div class="flex flex-col items-center">
              <i data-lucide="accessibility" class="w-10 h-10 text-primary-foreground/80 mb-3" aria-hidden="true"></i>
              <span class="text-4xl md:text-5xl font-black text-primary-foreground"><?= number_format($stats['total_tecnologia'], 0, ',', '.') ?></span>
              <span class="text-sm text-primary-foreground/80 mt-1 font-semibold">Tecnologia Assistiva</span>
            </div>
          </div>
          <p class="text-primary-foreground/85 max-w-2xl mx-auto leading-relaxed">
            Esses dados são fundamentais para o planejamento de políticas públicas, distribuição de recursos e desenvolvimento de ações inclusivas.
          </p>
          <a href="<?= BASE_URL ?>/cadastro-pcd" class="inline-flex items-center gap-2 mt-6 bg-primary-foreground text-primary font-bold px-6 py-3 rounded-full hover:shadow-lg transition-all hover:scale-105">
            Quero me cadastrar <i data-lucide="arrow-right" class="w-4 h-4"></i>
          </a>
        </div>
      </div>
    </section>

    <!-- HomeNews -->
    <section class="py-20 md:py-28 bg-card">
      <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-12 fade-in-up">
          <h2 class="text-2xl md:text-4xl font-extrabold text-foreground mb-4">Últimas Notícias</h2>
          <div class="w-16 h-1 bg-primary rounded-full mx-auto"></div>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
          <?php if (!empty($ultimasNoticias)): ?>
            <?php foreach ($ultimasNoticias as $n): ?>
              <?php
                $temaMap = [
                    'eventos' => 'Eventos',
                    'direitos' => 'Direitos',
                    'inclusao' => 'Inclusão',
                    'saude' => 'Saúde',
                    'educacao' => 'Educação',
                    'acessibilidade' => 'Acessibilidade',
                    'emprego' => 'Emprego',
                    'informativos' => 'Informativos',
                    'outros' => 'Outros'
                ];
                $categoria = $temaMap[$n['tema']] ?? ucfirst($n['tema']);
                $resumo = mb_strimwidth(strip_tags($n['conteudo']), 0, 150, '...');
                $imagem = $n['imagem_capa'] ? BASE_URL . '/' . $n['imagem_capa'] : 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop';
                $dataStr = date('d/m/Y', strtotime($n['data_publicacao'] ?? $n['criado_em']));
              ?>
              <article class="bg-background rounded-2xl border border-border overflow-hidden hover:-translate-y-1 transition-all duration-300 flex flex-col fade-in-up group" style="box-shadow: var(--card-shadow)">
                <a href="<?= BASE_URL ?>/noticia?id=<?= (int)$n['id'] ?>" class="block overflow-hidden h-48 bg-muted relative" aria-label="<?= htmlspecialchars($n['titulo']) ?>">
                  <img src="<?= e($imagem) ?>" alt="<?= htmlspecialchars($n['titulo']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                </a>
                <div class="p-6 flex flex-col flex-1">
                  <div class="flex items-center justify-between mb-3">
                    <span class="inline-block text-xs font-bold text-primary bg-primary/10 px-3 py-1 rounded-full uppercase tracking-wider"><?= e($categoria) ?></span>
                    <span class="text-xs text-muted-foreground flex items-center gap-1">
                      <i data-lucide="calendar" class="w-3.5 h-3.5" aria-hidden="true"></i> <?= e($dataStr) ?>
                    </span>
                  </div>
                  <h3 class="font-bold text-foreground mb-2 text-lg line-clamp-2 group-hover:text-primary transition-colors leading-snug">
                    <a href="<?= BASE_URL ?>/noticia?id=<?= (int)$n['id'] ?>">
                      <?= htmlspecialchars($n['titulo']) ?>
                    </a>
                  </h3>
                  <p class="text-sm text-muted-foreground line-clamp-2 mb-4 flex-1 leading-relaxed"><?= e($resumo) ?></p>
                  <div class="flex items-center justify-end mt-auto pt-3 border-t border-border/50">
                    <a href="<?= BASE_URL ?>/noticia?id=<?= (int)$n['id'] ?>" class="text-sm text-primary font-bold hover:underline inline-flex items-center gap-1.5" aria-label="Ver mais sobre: <?= htmlspecialchars($n['titulo']) ?>">
                      Ver mais <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          <?php else: ?>
            <!-- Fallback estático se o banco de dados estiver vazio -->
            <article class="bg-background rounded-2xl border border-border overflow-hidden hover:-translate-y-1 transition-all duration-300 flex flex-col fade-in-up group" style="box-shadow: var(--card-shadow)">
              <a href="<?= BASE_URL ?>/noticia?id=1" class="block overflow-hidden h-48 bg-muted relative" aria-label="Prefeitura anuncia novo programa de acessibilidade">
                <img src="https://images.unsplash.com/photo-1573497620053-ea5300f94f21?w=600&h=400&fit=crop" alt="Prefeitura anuncia novo programa de acessibilidade" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
              </a>
              <div class="p-6 flex flex-col flex-1">
                <div class="flex items-center justify-between mb-3">
                  <span class="inline-block text-xs font-bold text-primary bg-primary/10 px-3 py-1 rounded-full uppercase tracking-wider">Acessibilidade</span>
                  <span class="text-xs text-muted-foreground flex items-center gap-1">
                    <i data-lucide="calendar" class="w-3.5 h-3.5" aria-hidden="true"></i> 10/05/2026
                  </span>
                </div>
                <h3 class="font-bold text-foreground mb-2 text-lg line-clamp-2 group-hover:text-primary transition-colors leading-snug">
                  <a href="<?= BASE_URL ?>/noticia?id=1">Prefeitura anuncia novo programa de acessibilidade</a>
                </h3>
                <p class="text-sm text-muted-foreground line-clamp-2 mb-4 flex-1 leading-relaxed">O projeto prevê a adequação de 100% das calçadas da região central até o final do ano.</p>
                <div class="flex items-center justify-end mt-auto pt-3 border-t border-border/50">
                  <a href="<?= BASE_URL ?>/noticia?id=1" class="text-sm text-primary font-bold hover:underline inline-flex items-center gap-1.5" aria-label="Ver mais sobre o novo programa de acessibilidade">
                    Ver mais <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                  </a>
                </div>
              </div>
            </article>

            <article class="bg-background rounded-2xl border border-border overflow-hidden hover:-translate-y-1 transition-all duration-300 flex flex-col fade-in-up group" style="box-shadow: var(--card-shadow)">
              <a href="<?= BASE_URL ?>/noticia?id=2" class="block overflow-hidden h-48 bg-muted relative" aria-label="CMPCD Jahu lança campanha sobre o mercado de trabalho para PCD">
                <img src="https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=600&h=400&fit=crop" alt="CMPCD Jahu lança campanha sobre o mercado de trabalho para PCD" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
              </a>
              <div class="p-6 flex flex-col flex-1">
                <div class="flex items-center justify-between mb-3">
                  <span class="inline-block text-xs font-bold text-primary bg-primary/10 px-3 py-1 rounded-full uppercase tracking-wider">Emprego</span>
                  <span class="text-xs text-muted-foreground flex items-center gap-1">
                    <i data-lucide="calendar" class="w-3.5 h-3.5" aria-hidden="true"></i> 28/04/2026
                  </span>
                </div>
                <h3 class="font-bold text-foreground mb-2 text-lg line-clamp-2 group-hover:text-primary transition-colors leading-snug">
                  <a href="<?= BASE_URL ?>/noticia?id=2">CMPCD Jahu lança campanha sobre o mercado de trabalho para PCD</a>
                </h3>
                <p class="text-sm text-muted-foreground line-clamp-2 mb-4 flex-1 leading-relaxed">Ação visa conscientizar empresas sobre a importância e os benefícios da contratação de pessoas com deficiência.</p>
                <div class="flex items-center justify-end mt-auto pt-3 border-t border-border/50">
                  <a href="<?= BASE_URL ?>/noticia?id=2" class="text-sm text-primary font-bold hover:underline inline-flex items-center gap-1.5" aria-label="Ver mais sobre a campanha de mercado de trabalho">
                    Ver mais <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                  </a>
                </div>
              </div>
            </article>

            <article class="bg-background rounded-2xl border border-border overflow-hidden hover:-translate-y-1 transition-all duration-300 flex flex-col fade-in-up group" style="box-shadow: var(--card-shadow)">
              <a href="<?= BASE_URL ?>/noticia?id=3" class="block overflow-hidden h-48 bg-muted relative" aria-label="Resolução define diretrizes para atendimento preferencial qualificado">
                <img src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop" alt="Resolução define diretrizes para atendimento preferencial qualificado" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
              </a>
              <div class="p-6 flex flex-col flex-1">
                <div class="flex items-center justify-between mb-3">
                  <span class="inline-block text-xs font-bold text-primary bg-primary/10 px-3 py-1 rounded-full uppercase tracking-wider">Saúde</span>
                  <span class="text-xs text-muted-foreground flex items-center gap-1">
                    <i data-lucide="calendar" class="w-3.5 h-3.5" aria-hidden="true"></i> 15/04/2026
                  </span>
                </div>
                <h3 class="font-bold text-foreground mb-2 text-lg line-clamp-2 group-hover:text-primary transition-colors leading-snug">
                  <a href="<?= BASE_URL ?>/noticia?id=3">Resolução define diretrizes para atendimento preferencial qualificado</a>
                </h3>
                <p class="text-sm text-muted-foreground line-clamp-2 mb-4 flex-1 leading-relaxed">Resolução visa garantir atendimento de qualidade e acolhimento adequado para pessoas com deficiência na rede de saúde.</p>
                <div class="flex items-center justify-end mt-auto pt-3 border-t border-border/50">
                  <a href="<?= BASE_URL ?>/noticia?id=3" class="text-sm text-primary font-bold hover:underline inline-flex items-center gap-1.5" aria-label="Ver mais sobre diretrizes de atendimento preferencial">
                    Ver mais <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                  </a>
                </div>
              </div>
            </article>
          <?php endif; ?>
        </div>

        <div class="text-center mt-10">
          <a href="<?= BASE_URL ?>/noticias" class="inline-flex items-center gap-2 text-primary font-bold hover:underline">
            Ver todas as notícias <i data-lucide="arrow-right" class="w-4 h-4"></i>
          </a>
        </div>
      </div>
    </section>

    <!-- HomeSocial -->
    <section class="py-20 md:py-28 bg-background">
      <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-12 fade-in-up">
          <h2 class="text-2xl md:text-4xl font-extrabold text-foreground mb-4">Redes Sociais</h2>
          <div class="w-16 h-1 bg-primary rounded-full mx-auto mb-6"></div>
          <p class="text-muted-foreground">Acompanhe nossas publicações e fique por dentro das novidades</p>
        </div>

        <div class="max-w-6xl mx-auto flex flex-col items-center justify-center p-6">
          <div class="bg-card border border-border/80 rounded-2xl p-8 max-w-md shadow-2xl flex flex-col items-center text-center gap-4 transform transition-all duration-300 hover:scale-[1.02]" style="box-shadow: var(--card-shadow)">
            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-2 shadow-inner">
              <i data-lucide="wrench" class="w-8 h-8 animate-bounce"></i>
            </div>
            <h3 class="text-xl font-bold text-foreground">Seção em Construção</h3>
            <p class="text-sm text-muted-foreground leading-relaxed">
              Esta seção está em desenvolvimento. Estamos conectando nossa plataforma oficial de forma automática. Clique nos botões abaixo para ver as postagens reais em nossos canais!
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full mt-2">
              <a href="https://www.instagram.com/cmpcd_jau/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 bg-primary text-primary-foreground font-bold px-6 py-3.5 rounded-full hover:shadow-lg transition-all hover:scale-105 w-full sm:w-auto text-sm">
                <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/>
                </svg>
                Instagram Oficial
              </a>
              <a href="https://www.facebook.com/cmpcdjau?locale=pt_BR" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 bg-primary/10 text-primary border border-primary/20 hover:bg-primary/20 font-bold px-6 py-3.5 rounded-full hover:shadow-lg transition-all hover:scale-105 w-full sm:w-auto text-sm">
                <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c4.56-.93 8-4.96 8-9.75z"/>
                </svg>
                Facebook Oficial
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
