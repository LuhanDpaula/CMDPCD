<?php require_once __DIR__ . '/../../../partials/admin_header.php'; ?>

<div role="alert" class="bg-card rounded-2xl border border-border p-8 max-w-2xl mx-auto text-center" style="box-shadow: var(--card-shadow)">
  <div class="w-16 h-16 rounded-full bg-destructive/10 flex items-center justify-center mx-auto mb-4">
    <i data-lucide="database" class="w-8 h-8 text-destructive"></i>
  </div>
  <h2 class="text-xl font-bold mb-2">MongoDB indisponível</h2>
  <p class="text-muted-foreground text-sm mb-4">
    Não foi possível conectar ao MongoDB. O restante do sistema (MySQL) continua funcionando normalmente;
    apenas o chat, as perguntas frequentes e os pedidos de ajuda ficam fora do ar.
  </p>
  <p class="text-muted-foreground text-xs">
    Verifique se o serviço do MongoDB está rodando e se a extensão <strong>mongodb</strong> do PHP está ativa
    (veja o README, seção "MongoDB").
  </p>
</div>

<?php require_once __DIR__ . '/../../../partials/admin_footer.php'; ?>
