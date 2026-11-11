<?php require_once __DIR__ . '/../../partials/admin_header.php'; ?>
<?php
$id = (string)$pedido['_id'];
$tel = $pedido['telefone'];
$telFmt = strlen($tel) === 11 ? sprintf('(%s) %s-%s', substr($tel, 0, 2), substr($tel, 2, 5), substr($tel, 7))
                              : sprintf('(%s) %s-%s', substr($tel, 0, 2), substr($tel, 2, 4), substr($tel, 6));
$encerrado = in_array($pedido['status'], PedidoAjuda::STATUS_ENCERRADOS, true);
?>

<div class="space-y-8">
  <nav class="flex items-center gap-2 text-sm text-muted-foreground" aria-label="Caminho">
    <a href="<?= BASE_URL ?>/admin/pedidos" class="hover:text-primary transition-colors">Pedidos de Ajuda</a>
    <i data-lucide="chevron-right" class="w-4 h-4"></i>
    <span class="text-foreground font-semibold" aria-current="page">Protocolo <?= e($pedido['protocolo']) ?></span>
  </nav>

  <?php require __DIR__ . '/../../partials/admin_alertas.php'; ?>

  <div class="grid lg:grid-cols-3 gap-6">
    <!-- Dados do pedido -->
    <div class="lg:col-span-2 space-y-6">
      <section class="bg-card rounded-2xl border border-border p-6" style="box-shadow: var(--card-shadow)" aria-labelledby="titulo-pedido">
        <div class="flex items-start justify-between flex-wrap gap-4 mb-6">
          <div>
            <h2 id="titulo-pedido" class="text-2xl font-black"><?= e($pedido['nome']) ?></h2>
            <p class="text-sm text-muted-foreground">
              Protocolo <?= e($pedido['protocolo']) ?> · recebido em <?= e(Mongo::date($pedido['criado_em'])) ?>
            </p>
          </div>
          <div class="flex items-center gap-2 flex-wrap">
            <?php if (!empty($pedido['prioritario'])): ?>
              <span class="px-3 py-1 rounded-full text-xs font-bold bg-destructive/10 text-destructive">★ Prioritário</span>
            <?php endif; ?>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary"><?= e(PedidoAjuda::STATUS[$pedido['status']] ?? $pedido['status']) ?></span>
          </div>
        </div>

        <dl class="grid sm:grid-cols-2 gap-4 text-sm">
          <div>
            <dt class="text-muted-foreground">Telefone</dt>
            <dd class="font-bold text-lg">
              <a href="tel:+55<?= e($tel) ?>" class="text-primary hover:underline"><?= e($telFmt) ?></a>
              <?php if ($pedido['preferencia_contato'] === 'whatsapp'): ?>
                · <a href="https://wa.me/55<?= e($tel) ?>" target="_blank" rel="noopener noreferrer" class="text-green-600 hover:underline text-sm">Abrir WhatsApp</a>
              <?php endif; ?>
            </dd>
          </div>
          <div>
            <dt class="text-muted-foreground">Prefere ser contatado por</dt>
            <dd class="font-semibold"><?= e(PedidoAjuda::PREFERENCIAS[$pedido['preferencia_contato']] ?? '') ?> · <?= e(PedidoAjuda::HORARIOS[$pedido['melhor_horario']] ?? '') ?></dd>
          </div>
          <div>
            <dt class="text-muted-foreground">Bairro</dt>
            <dd class="font-semibold"><?= e($pedido['bairro']) ?></dd>
          </div>
          <div>
            <dt class="text-muted-foreground">Endereço</dt>
            <dd class="font-semibold"><?= $pedido['endereco'] ? e($pedido['endereco']) : '<span class="text-muted-foreground">Não informado</span>' ?></dd>
          </div>
          <div>
            <dt class="text-muted-foreground">Assunto</dt>
            <dd class="font-semibold"><?= e(PedidoAjuda::ASSUNTOS[$pedido['assunto']] ?? $pedido['assunto']) ?></dd>
          </div>
          <div>
            <dt class="text-muted-foreground">Quem fez o pedido</dt>
            <dd class="font-semibold"><?= e(PedidoAjuda::PARA_QUEM[$pedido['para_quem']] ?? '') ?></dd>
          </div>
          <div class="sm:col-span-2">
            <dt class="text-muted-foreground">Mensagem</dt>
            <dd class="mt-1 p-4 bg-muted/30 rounded-xl" style="white-space: pre-line"><?= e($pedido['mensagem']) ?></dd>
          </div>
        </dl>
      </section>

      <!-- Histórico ($push) -->
      <section class="bg-card rounded-2xl border border-border p-6" style="box-shadow: var(--card-shadow)" aria-labelledby="titulo-historico">
        <h3 id="titulo-historico" class="text-lg font-bold mb-4">Histórico do atendimento</h3>
        <ol class="space-y-4">
          <?php foreach (array_reverse($pedido['historico'] ?? []) as $h): ?>
            <li class="border-l border-border pl-4">
              <p class="text-xs text-muted-foreground"><?= e(Mongo::date($h['data'] ?? null)) ?> · <?= e($h['por'] ?? '') ?></p>
              <p class="font-semibold"><?= e($h['acao']) ?>
                <?php if (!empty($h['status'])): ?>
                  <span class="text-xs font-bold text-primary">→ <?= e(PedidoAjuda::STATUS[$h['status']] ?? $h['status']) ?></span>
                <?php endif; ?>
              </p>
              <?php if (!empty($h['obs'])): ?>
                <p class="text-sm text-muted-foreground" style="white-space: pre-line"><?= e($h['obs']) ?></p>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ol>
      </section>
    </div>

    <!-- Ações -->
    <div class="space-y-6">
      <section class="bg-card rounded-2xl border border-border p-6" style="box-shadow: var(--card-shadow)" aria-labelledby="titulo-registrar">
        <h3 id="titulo-registrar" class="text-lg font-bold mb-4">Registrar atendimento</h3>
        <form action="<?= BASE_URL ?>/admin/pedidos/registrar" method="POST" class="space-y-4">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= e($id) ?>">
          <div>
            <label for="r-acao" class="block text-sm font-medium mb-1">O que foi feito? *</label>
            <input id="r-acao" type="text" name="acao" required maxlength="120" list="acoes-comuns" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Ex.: Ligação realizada">
            <datalist id="acoes-comuns">
              <option value="Ligação realizada">
              <option value="Ligação sem resposta">
              <option value="Mensagem enviada por WhatsApp">
              <option value="Encaminhado ao CRAS">
              <option value="Encaminhado à Secretaria de Saúde">
              <option value="Encaminhado à Secretaria de Educação">
              <option value="Visita realizada">
              <option value="Orientação concluída">
            </datalist>
          </div>
          <div>
            <label for="r-obs" class="block text-sm font-medium mb-1">Observações</label>
            <textarea id="r-obs" name="obs" rows="4" maxlength="1000" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"></textarea>
          </div>
          <div>
            <label for="r-status" class="block text-sm font-medium mb-1">Situação do pedido</label>
            <select id="r-status" name="status" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
              <?php foreach (PedidoAjuda::STATUS as $k => $label): ?>
                <option value="<?= e($k) ?>" <?= $pedido['status'] === $k ? 'selected' : '' ?>><?= e($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button type="submit" class="w-full bg-primary text-primary-foreground px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-primary/90 transition-colors">Salvar no histórico</button>
        </form>
      </section>

      <section class="bg-card rounded-2xl border border-border p-6 space-y-4" style="box-shadow: var(--card-shadow)" aria-labelledby="titulo-outras">
        <h3 id="titulo-outras" class="text-lg font-bold">Outras ações</h3>
        <form action="<?= BASE_URL ?>/admin/pedidos/prioridade" method="POST">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= e($id) ?>">
          <button type="submit" class="w-full px-4 py-2 rounded-xl text-sm font-bold border border-border hover:bg-muted/50 transition-colors">
            <?= !empty($pedido['prioritario']) ? 'Remover prioridade' : '★ Marcar como prioritário' ?>
          </button>
          <p class="text-xs text-muted-foreground mt-1">Marcação interna, só o Conselho vê. Pedidos prioritários sobem para o topo da fila.</p>
        </form>

        <div class="text-xs text-muted-foreground bg-muted/30 p-3 rounded-xl">
          <strong class="text-foreground">LGPD:</strong>
          <?php if ($encerrado && !empty($pedido['encerrado_em'])): ?>
            encerrado em <?= e(Mongo::date($pedido['encerrado_em'], 'd/m/Y')) ?>. Este pedido será <strong>excluído automaticamente</strong>
            por volta de <?= e(date('d/m/Y', $pedido['encerrado_em']->toDateTime()->getTimestamp() + Mongo::TTL_PEDIDOS)) ?> (índice TTL do MongoDB).
          <?php else: ?>
            enquanto o pedido estiver em aberto ele não é apagado. Ao concluir ou marcar "sem retorno", começa a contar 1 ano para a exclusão automática.
          <?php endif; ?>
          <br>Consentimento dado em <?= e(Mongo::date($pedido['consentimento']['data'] ?? null)) ?>.
        </div>

        <form method="POST" action="<?= BASE_URL ?>/admin/pedidos/excluir" class="w-full m-0 p-0">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= e($id) ?>">
          <button type="submit"
                  data-confirm="Excluir AGORA e definitivamente este pedido e todos os dados de <?= e($pedido['nome']) ?>? Use isto quando a pessoa pedir a exclusão dos dados."
                  class="w-full inline-flex justify-center px-4 py-2 rounded-xl text-sm font-bold bg-destructive/10 text-destructive hover:bg-destructive/20 transition-colors cursor-pointer">
            Excluir dados agora
          </button>
        </form>
      </section>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../partials/admin_footer.php'; ?>
