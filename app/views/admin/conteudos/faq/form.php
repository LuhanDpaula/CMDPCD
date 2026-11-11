<?php require_once __DIR__ . '/../../../partials/admin_header.php'; ?>
<?php $editando = ($modo === 'edit'); ?>

<div class="bg-card rounded-2xl border border-border overflow-hidden max-w-4xl mx-auto mb-10" style="box-shadow: var(--card-shadow)">
  <div class="p-6 border-b border-border">
    <nav class="flex items-center gap-2 text-sm text-muted-foreground mb-2" aria-label="Caminho">
      <a href="<?= BASE_URL ?>/admin/conteudos" class="hover:text-primary transition-colors">Conteúdos</a>
      <i data-lucide="chevron-right" class="w-4 h-4"></i>
      <a href="<?= BASE_URL ?>/admin/conteudos/faq" class="hover:text-primary transition-colors">Perguntas Frequentes</a>
      <i data-lucide="chevron-right" class="w-4 h-4"></i>
      <span class="text-foreground font-semibold" aria-current="page"><?= $editando ? 'Editar' : 'Nova pergunta' ?></span>
    </nav>
    <h2 class="text-xl font-bold"><?= $editando ? 'Editar Pergunta Frequente' : 'Cadastrar Pergunta Frequente' ?></h2>
    <p class="text-sm text-muted-foreground">Escreva em linguagem simples, como se estivesse explicando por telefone.</p>
  </div>

  <div class="px-6 pt-6"><?php require __DIR__ . '/../../../partials/admin_alertas.php'; ?></div>

  <form action="<?= BASE_URL ?>/admin/conteudos/faq/<?= $editando ? 'update' : 'store' ?>" method="POST" class="p-6 space-y-6">
    <?= csrf_field() ?>
    <?php if ($editando): ?>
      <input type="hidden" name="id" value="<?= e($faq['id']) ?>">
    <?php endif; ?>
    <?php if (!empty($faq['origem'])): ?>
      <input type="hidden" name="origem" value="<?= e($faq['origem']) ?>">
      <p class="text-sm bg-primary/10 text-primary p-3 rounded-xl">Esta pergunta veio da lista de dúvidas sem resposta. Ao salvar, ela sai daquela lista.</p>
    <?php endif; ?>

    <div>
      <label for="faq-pergunta" class="block text-sm font-medium mb-1">Pergunta *</label>
      <input id="faq-pergunta" type="text" name="pergunta" required maxlength="300" value="<?= e($faq['pergunta'] ?? '') ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Ex.: Como consigo a credencial de estacionamento?">
    </div>

    <div>
      <label for="faq-resposta" class="block text-sm font-medium mb-1">Resposta *</label>
      <textarea id="faq-resposta" name="resposta" required maxlength="3000" rows="8" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" aria-describedby="faq-resposta-dica"><?= e($faq['resposta'] ?? '') ?></textarea>
      <p id="faq-resposta-dica" class="text-xs text-muted-foreground mt-1">Texto simples. As quebras de linha são mantidas no chat. Máximo de 3.000 caracteres.</p>
    </div>

    <div class="grid sm:grid-cols-3 gap-6">
      <div>
        <label for="faq-categoria" class="block text-sm font-medium mb-1">Categoria *</label>
        <select id="faq-categoria" name="categoria" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
          <option value="">Selecione</option>
          <?php foreach (Faq::CATEGORIAS as $k => $label): ?>
            <option value="<?= e($k) ?>" <?= ($faq['categoria'] ?? '') === $k ? 'selected' : '' ?>><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="faq-ordem" class="block text-sm font-medium mb-1">Ordem na lista</label>
        <input id="faq-ordem" type="number" name="ordem" min="0" max="9999" value="<?= (int)($faq['ordem'] ?? 0) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" aria-describedby="faq-ordem-dica">
        <p id="faq-ordem-dica" class="text-xs text-muted-foreground mt-1">Números menores aparecem primeiro.</p>
      </div>
      <div class="flex items-center gap-2 pt-6">
        <input id="faq-ativo" type="checkbox" name="ativo" value="1" <?= !empty($faq['ativo']) ? 'checked' : '' ?> class="w-4 h-4 accent-primary">
        <label for="faq-ativo" class="text-sm font-medium">Mostrar no chat</label>
      </div>
    </div>

    <div>
      <label for="faq-palavras" class="block text-sm font-medium mb-1">Palavras-chave</label>
      <input id="faq-palavras" type="text" name="palavras_chave" value="<?= e($faq['palavras_chave'] ?? '') ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="estacionamento, vaga, credencial, cartão" aria-describedby="faq-palavras-dica">
      <p id="faq-palavras-dica" class="text-xs text-muted-foreground mt-1">Separe por vírgula. Use palavras que as pessoas digitariam, inclusive sinônimos e jeitos informais de perguntar.</p>
    </div>

    <?php if ($editando): ?>
      <p class="text-xs text-muted-foreground">
        Criada em <?= e(Mongo::date($faq['criado_em'] ?? null)) ?> · Atualizada em <?= e(Mongo::date($faq['atualizado_em'] ?? null)) ?> ·
        <?= (int)($faq['visualizacoes'] ?? 0) ?> visualizações · 👍 <?= (int)($faq['util']['sim'] ?? 0) ?> · 👎 <?= (int)($faq['util']['nao'] ?? 0) ?>
      </p>
    <?php endif; ?>

    <div class="flex items-center justify-end gap-3 pt-4 border-t border-border">
      <a href="<?= BASE_URL ?>/admin/conteudos/faq" class="px-4 py-2 rounded-xl text-sm font-semibold border border-border hover:bg-muted/50 transition-colors">Cancelar</a>
      <button type="submit" class="bg-primary text-primary-foreground px-6 py-2 rounded-xl text-sm font-bold hover:bg-primary/90 transition-colors">Salvar pergunta</button>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../../../partials/admin_footer.php'; ?>
