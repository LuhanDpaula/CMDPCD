  <!-- RODAPÉ -->
  <footer class="py-12" style="background: var(--hero-gradient)">
    <div class="container mx-auto px-4">
      <div class="grid md:grid-cols-3 gap-10 mb-10">
        <!-- About -->
        <div>
          <div class="flex items-center gap-2 mb-4">
            <i data-lucide="accessibility" class="w-6 h-6 text-primary-foreground" aria-hidden="true"></i>
            <span class="font-extrabold text-lg text-primary-foreground">CMDPCD Jahu</span>
          </div>
          <p class="text-primary-foreground/80 text-sm leading-relaxed">
            Conselho Municipal dos Direitos da Pessoa com Deficiência de Jahu. Parceria com a Secretaria de Assistência e Desenvolvimento Social.
          </p>
        </div>

        <!-- Links -->
        <div>
          <h3 class="font-bold text-primary-foreground mb-4">Links Úteis</h3>
          <nav class="flex flex-col gap-2" aria-label="Links do rodapé">
            <a href="<?= BASE_URL ?>/quem-somos" class="text-sm text-primary-foreground/80 hover:text-primary-foreground transition-colors">Quem Somos</a>
            <a href="<?= BASE_URL ?>/transparencia" class="text-sm text-primary-foreground/80 hover:text-primary-foreground transition-colors">Transparência</a>
            <a href="<?= BASE_URL ?>/noticias" class="text-sm text-primary-foreground/80 hover:text-primary-foreground transition-colors">Notícias</a>
            <a href="<?= BASE_URL ?>/cadastro-pcd" class="text-sm text-primary-foreground/80 hover:text-primary-foreground transition-colors">Registrar PCD</a>
            <a href="<?= BASE_URL ?>/contato" class="text-sm text-primary-foreground/80 hover:text-primary-foreground transition-colors">Contato</a>
          </nav>
        </div>

        <!-- Contact -->
        <div>
          <h3 class="font-bold text-primary-foreground mb-4">Contato</h3>
          <div class="flex flex-col gap-3">
            <div class="flex items-center gap-2 text-sm text-primary-foreground/80">
              <i data-lucide="map-pin" class="w-4 h-4 shrink-0" aria-hidden="true"></i>
              <span>Rua Aristides Lobo Sobrinho, nº 174, Chácara Braz Miraglia - Jahu/SP</span>
            </div>
            <div class="flex items-center gap-2 text-sm text-primary-foreground/80">
              <i data-lucide="phone" class="w-4 h-4 shrink-0" aria-hidden="true"></i>
              <span>(14) 3624-5077</span>
            </div>
            <div class="flex items-center gap-2 text-sm text-primary-foreground/80">
              <i data-lucide="mail" class="w-4 h-4 shrink-0" aria-hidden="true"></i>
              <span>cmpcdjau@gmail.com</span>
            </div>
            <div class="flex items-center gap-3 mt-2">
              <a href="https://www.facebook.com/cmpcdjau?locale=pt_BR" target="_blank" rel="noopener noreferrer" class="text-primary-foreground/80 hover:text-primary-foreground transition-colors" aria-label="Facebook">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c4.56-.93 8-4.96 8-9.75z"/>
                </svg>
              </a>
              <a href="https://www.instagram.com/cmpcd_jau/" target="_blank" rel="noopener noreferrer" class="text-primary-foreground/80 hover:text-primary-foreground transition-colors" aria-label="Instagram">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/>
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="border-t border-primary-foreground/20 pt-6 text-center">
        <p class="text-primary-foreground/60 text-xs">
          © <span id="current-year"></span> CMDPCD Jahu — Conselho Municipal dos Direitos da Pessoa com Deficiência. Todos os direitos reservados.
        </p>
      </div>
    </div>
  </footer>

  <?php require __DIR__ . '/chat_widget.php'; ?>
  <?php require __DIR__ . '/a11y_widget.php'; ?>

  <script src="<?= BASE_URL ?>/js/main.js"></script>
  <script src="<?= BASE_URL ?>/js/a11y.js"></script>
  <script src="<?= BASE_URL ?>/js/chat.js"></script>
</body>
</html>
