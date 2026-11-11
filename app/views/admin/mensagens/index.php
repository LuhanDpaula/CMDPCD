<?php require_once __DIR__ . '/../../partials/admin_header.php'; ?>

<div class="bg-card rounded-2xl border border-border overflow-hidden" style="box-shadow: var(--card-shadow)">
  <div class="p-6 border-b border-border flex items-center justify-between">
    <div>
      <h2 class="text-xl font-bold text-foreground">Mensagens Recebidas</h2>
      <p class="text-sm text-muted-foreground mt-1">Veja e gerencie os contatos enviados pelo formulário do portal público</p>
    </div>
  </div>

  <?php if (!empty($success)): ?>
    <div class="m-6 p-4 bg-emerald-500/10 text-emerald-500 text-sm rounded-xl border border-emerald-500/20">
      <?= htmlspecialchars($success) ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="m-6 p-4 bg-destructive/10 text-destructive text-sm rounded-xl border border-destructive/20">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="border-b border-border text-muted-foreground text-xs font-semibold uppercase bg-muted/30">
          <th class="px-6 py-4">Status</th>
          <th class="px-6 py-4">Remetente</th>
          <th class="px-6 py-4">E-mail / Telefone</th>
          <th class="px-6 py-4">Assunto</th>
          <th class="px-6 py-4">Data</th>
          <th class="px-6 py-4 text-right">Ações</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-border/50 text-sm">
        <?php if (empty($mensagens)): ?>
          <tr>
            <td colspan="6" class="px-6 py-12 text-center text-muted-foreground">
              Nenhuma mensagem recebida ainda.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($mensagens as $m): ?>
            <tr id="msg-row-<?= $m['id'] ?>" class="hover:bg-muted/10 transition-colors <?= $m['lido'] ? 'text-muted-foreground' : 'font-semibold text-foreground bg-primary/5' ?>">
              <td class="px-6 py-4 shrink-0">
                <span id="status-badge-<?= $m['id'] ?>" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium <?= $m['lido'] ? 'bg-muted text-muted-foreground' : 'bg-primary/20 text-primary' ?>">
                  <span class="w-1.5 h-1.5 rounded-full <?= $m['lido'] ? 'bg-muted-foreground' : 'bg-primary' ?>"></span>
                  <?= $m['lido'] ? 'Lida' : 'Nova' ?>
                </span>
              </td>
              <td class="px-6 py-4 max-w-[150px] truncate">
                <?= htmlspecialchars($m['nome']) ?>
              </td>
              <td class="px-6 py-4">
                <div class="text-xs font-medium"><?= htmlspecialchars($m['email']) ?></div>
                <?php if (!empty($m['telefone'])): ?>
                  <div class="text-[10px] text-muted-foreground mt-0.5"><?= htmlspecialchars($m['telefone']) ?></div>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 max-w-[200px] truncate">
                <?= htmlspecialchars($m['assunto']) ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-xs text-muted-foreground">
                <?= date('d/m/Y H:i', strtotime($m['criado_em'])) ?>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="inline-flex items-center gap-2">
                  <button 
                    class="p-2 text-primary hover:bg-primary/10 rounded-xl transition-colors btn-visualizar-mensagem"
                    data-id="<?= $m['id'] ?>"
                    data-nome="<?= htmlspecialchars($m['nome']) ?>"
                    data-email="<?= htmlspecialchars($m['email']) ?>"
                    data-telefone="<?= htmlspecialchars($m['telefone'] ?? '') ?>"
                    data-assunto="<?= htmlspecialchars($m['assunto']) ?>"
                    data-mensagem="<?= htmlspecialchars($m['mensagem']) ?>"
                    data-data="<?= date('d/m/Y H:i', strtotime($m['criado_em'])) ?>"
                    data-lido="<?= $m['lido'] ?>"
                    title="Visualizar Mensagem"
                  >
                    <i data-lucide="eye" class="w-4 h-4"></i>
                  </button>
                  <?php if (!isAdminParcial()): ?>
                    <form method="POST" action="<?= BASE_URL ?>/admin/mensagens/delete" class="inline m-0 p-0">
                      <?= csrf_field() ?>
                      <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                      <button 
                        type="submit"
                        class="p-2 text-destructive hover:bg-destructive/10 rounded-xl transition-colors cursor-pointer"
                        data-confirm="Tem certeza que deseja apagar permanentemente esta mensagem?"
                        title="Apagar"
                      >
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                      </button>
                    </form>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Detalhes Mensagem -->
<div id="mensagem-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300" style="background-color: rgba(0, 0, 0, 0.45);">
  <div class="bg-card rounded-2xl border border-border max-w-lg w-full overflow-hidden shadow-2xl relative flex flex-col max-h-[80vh] transform scale-95 transition-transform duration-300">
    <!-- Header -->
    <div class="p-6 border-b border-border flex items-center justify-between shrink-0">
      <div>
        <h3 class="text-lg font-bold text-foreground">Visualizar Mensagem</h3>
        <span id="modal-msg-data" class="text-xs text-muted-foreground"></span>
      </div>
      <button id="modal-close" class="p-2 rounded-xl bg-muted/50 hover:bg-muted text-foreground transition-all">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <!-- Conteúdo -->
    <div class="p-6 overflow-y-auto space-y-4">
      <div class="grid grid-cols-2 gap-4 text-xs bg-muted/30 p-4 rounded-xl border border-border">
        <div>
          <span class="text-muted-foreground block">Remetente:</span>
          <strong id="modal-msg-nome" class="text-foreground"></strong>
        </div>
        <div>
          <span class="text-muted-foreground block">Assunto:</span>
          <strong id="modal-msg-assunto" class="text-foreground"></strong>
        </div>
        <div class="col-span-2 border-t border-border/50 pt-2 mt-2">
          <span class="text-muted-foreground block">E-mail / Telefone:</span>
          <span id="modal-msg-contato" class="text-foreground font-medium"></span>
        </div>
      </div>

      <div class="space-y-2">
        <span class="text-xs text-muted-foreground font-semibold uppercase tracking-wider block">Mensagem:</span>
        <div id="modal-msg-texto" class="text-sm text-foreground bg-background rounded-xl p-4 border border-border leading-relaxed whitespace-pre-wrap min-h-[100px]">
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('mensagem-modal');
    const modalClose = document.getElementById('modal-close');
    const modalNome = document.getElementById('modal-msg-nome');
    const modalAssunto = document.getElementById('modal-msg-assunto');
    const modalContato = document.getElementById('modal-msg-contato');
    const modalData = document.getElementById('modal-msg-data');
    const modalTexto = document.getElementById('modal-msg-texto');

    function openModal(data) {
      modalNome.textContent = data.nome;
      modalAssunto.textContent = data.assunto;
      modalContato.textContent = data.telefone ? `${data.email} | ${data.telefone}` : data.email;
      modalData.textContent = data.data;
      modalTexto.textContent = data.mensagem;

      modal.classList.remove('hidden');
      setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.querySelector('.transform').classList.remove('scale-95');
      }, 10);

      // Se a mensagem ainda não for lida, marca como lida via AJAX
      if (data.lido == 0) {
        const formData = new FormData();
        formData.append('id', data.id);
        formData.append('ajax', '1');
        formData.append('_token', <?= js_json(csrf_token()) ?>);

        fetch('<?= BASE_URL ?>/admin/mensagens/lido', {
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(resData => {
          if (resData.status === 'success') {
            // Atualiza linha visualmente
            const row = document.getElementById(`msg-row-${data.id}`);
            if (row) {
              row.classList.remove('font-semibold', 'text-foreground', 'bg-primary/5');
              row.classList.add('text-muted-foreground');
            }
            
            // Atualiza o badge de status
            const badge = document.getElementById(`status-badge-${data.id}`);
            if (badge) {
              badge.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-muted text-muted-foreground';
              badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-muted-foreground"></span>Lida';
            }

            // Atualiza o botão da linha para que não dispare o AJAX novamente
            const btn = document.querySelector(`.btn-visualizar-mensagem[data-id="${data.id}"]`);
            if (btn) {
              btn.setAttribute('data-lido', '1');
            }

            // Decrementa o indicador do header (se houver)
            const badgeHeader = document.querySelector('a[href*="/admin/mensagens"] span');
            if (badgeHeader) {
              let count = parseInt(badgeHeader.textContent.trim());
              count = Math.max(0, count - 1);
              if (count === 0) {
                badgeHeader.remove();
              } else {
                badgeHeader.textContent = count;
              }
            }
          }
        });
      }
    }

    function closeModal() {
      modal.classList.add('opacity-0');
      modal.querySelector('.transform').classList.add('scale-95');
      setTimeout(() => {
        modal.classList.add('hidden');
      }, 300);
    }

    // Handlers
    document.querySelectorAll('.btn-visualizar-mensagem').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const id = btn.getAttribute('data-id');
        const nome = btn.getAttribute('data-nome');
        const email = btn.getAttribute('data-email');
        const telefone = btn.getAttribute('data-telefone');
        const assunto = btn.getAttribute('data-assunto');
        const mensagem = btn.getAttribute('data-mensagem');
        const data = btn.getAttribute('data-data');
        const lido = btn.getAttribute('data-lido');

        openModal({ id, nome, email, telefone, assunto, mensagem, data, lido });
      });
    });

    if (modalClose) modalClose.addEventListener('click', closeModal);
    if (modal) {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
      });
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
          closeModal();
        }
      });
    }
  });
</script>

<?php require_once __DIR__ . '/../../partials/admin_footer.php'; ?>
