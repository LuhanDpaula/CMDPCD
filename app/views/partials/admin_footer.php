          <!-- Fim do conteúdo da página específica -->
        </div>
      </section>
    </div>
  </main>

  <footer class="py-6 mt-auto" style="background: var(--hero-gradient)">
    <div class="container mx-auto px-4 text-center">
      <p class="text-primary-foreground/60 text-xs">
        © <span id="current-year-admin"></span> CMPCD Jahu
      </p>
    </div>
  </footer>

  <?php require __DIR__ . '/a11y_widget.php'; ?>

  <script src="<?= BASE_URL ?>/js/main.js"></script>
  <script src="<?= BASE_URL ?>/js/a11y.js"></script>
  <script>
    // Inicializa ano atual na administração
    const yearAdmin = document.getElementById('current-year-admin');
    if (yearAdmin) {
      yearAdmin.textContent = new Date().getFullYear();
    }
  </script>
</body>
</html>
