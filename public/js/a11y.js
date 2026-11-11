/* ============================================================
   ACESSIBILIDADE - CMDPCD Jahu
   1) Widget flutuante (fonte, contraste, links, animações)
   2) Ajustes automáticos para leitores de tela (NVDA, JAWS,
      VoiceOver, TalkBack) em todas as páginas
   ============================================================ */
(function () {
  'use strict';

  var html = document.documentElement;
  var STORAGE_KEY = 'cmdpcd_a11y';
  var MIN = 80, MAX = 200, STEP = 10;

  function load() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || {}; } catch (e) { return {}; }
  }
  function save(s) {
    try { localStorage.setItem(STORAGE_KEY, JSON.stringify(s)); } catch (e) { /* modo privado */ }
  }

  var state = Object.assign({ font: 100, contraste: false, links: false, legivel: false, animacao: false }, load());

  var TOGGLES = {
    contraste: { cls: 'high-contrast',      on: 'Alto contraste ativado',   off: 'Alto contraste desativado' },
    links:     { cls: 'a11y-links',         on: 'Links destacados',         off: 'Destaque de links desativado' },
    legivel:   { cls: 'a11y-legivel',       on: 'Fonte legível ativada',    off: 'Fonte legível desativada' },
    animacao:  { cls: 'a11y-sem-animacao',  on: 'Animações pausadas',       off: 'Animações reativadas' }
  };

  function apply() {
    html.style.fontSize = state.font === 100 ? '' : state.font + '%';
    Object.keys(TOGGLES).forEach(function (k) {
      html.classList.toggle(TOGGLES[k].cls, !!state[k]);
      var btn = document.querySelector('[data-a11y="' + k + '"]');
      if (btn) btn.setAttribute('aria-pressed', state[k] ? 'true' : 'false');
    });
    var pct = document.getElementById('a11y-font-pct');
    if (pct) pct.textContent = state.font + '%';
  }

  // Aplica imediatamente (antes do DOM terminar) para não "piscar"
  apply();

  function announce(msg) {
    var live = document.getElementById('a11y-live');
    if (!live) return;
    live.textContent = '';
    setTimeout(function () { live.textContent = msg; }, 60);
  }

  // ------------------------------------------------------------
  // 1) WIDGET
  // ------------------------------------------------------------
  function initWidget() {
    var toggle = document.getElementById('a11y-toggle');
    var panel = document.getElementById('a11y-panel');
    if (!toggle || !panel) return;

    function open() {
      panel.hidden = false;
      toggle.setAttribute('aria-expanded', 'true');
      toggle.setAttribute('aria-label', 'Fechar menu de acessibilidade');
      var first = panel.querySelector('.a11y-option');
      if (first) first.focus();
    }
    function close(returnFocus) {
      panel.hidden = true;
      toggle.setAttribute('aria-expanded', 'false');
      toggle.setAttribute('aria-label', 'Abrir menu de acessibilidade');
      if (returnFocus) toggle.focus();
    }

    toggle.addEventListener('click', function () { panel.hidden ? open() : close(true); });

    panel.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-a11y]');
      if (!btn) return;
      var action = btn.getAttribute('data-a11y');

      if (action === 'fechar') { close(true); return; }
      if (action === 'aumentar') {
        state.font = Math.min(MAX, state.font + STEP);
        announce(state.font === MAX ? 'Tamanho máximo da fonte: ' + MAX + '%' : 'Fonte aumentada para ' + state.font + '%');
      } else if (action === 'diminuir') {
        state.font = Math.max(MIN, state.font - STEP);
        announce(state.font === MIN ? 'Tamanho mínimo da fonte: ' + MIN + '%' : 'Fonte diminuída para ' + state.font + '%');
      } else if (action === 'padrao') {
        state.font = 100;
        announce('Fonte restaurada para o tamanho padrão');
      } else if (action === 'resetar') {
        state = { font: 100, contraste: false, links: false, legivel: false, animacao: false };
        announce('Todas as configurações de acessibilidade foram restauradas');
      } else if (TOGGLES[action]) {
        state[action] = !state[action];
        announce(state[action] ? TOGGLES[action].on : TOGGLES[action].off);
      }
      save(state);
      apply();
    });

    // ESC fecha; TAB fica preso dentro do painel enquanto aberto
    panel.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { e.preventDefault(); close(true); return; }
      if (e.key === 'Tab') {
        var items = panel.querySelectorAll('button');
        var first = items[0], last = items[items.length - 1];
        if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
      }
    });

    // Clicar fora fecha
    document.addEventListener('click', function (e) {
      if (!panel.hidden && !panel.contains(e.target) && !toggle.contains(e.target)) close(false);
    });

    // Atalho de teclado: Alt + A
    document.addEventListener('keydown', function (e) {
      if (e.altKey && (e.key === 'a' || e.key === 'A')) {
        e.preventDefault();
        panel.hidden ? open() : close(true);
      }
    });

    apply();
  }

  // ------------------------------------------------------------
  // 2) MELHORIAS AUTOMÁTICAS PARA LEITORES DE TELA
  // ------------------------------------------------------------
  var uid = 0;
  function ensureId(el, prefix) {
    if (!el.id) el.id = (prefix || 'a11y') + '-' + (++uid);
    return el.id;
  }

  function enhance(root) {
    root = root || document;

    // Ícones decorativos (Lucide e SVGs soltos) não devem ser lidos
    root.querySelectorAll('svg:not([aria-label]):not([role="img"]), i[data-lucide]').forEach(function (el) {
      el.setAttribute('aria-hidden', 'true');
      if (el.tagName.toLowerCase() === 'svg') el.setAttribute('focusable', 'false');
    });

    // Botões/links só com ícone: usa o "title" como nome acessível
    root.querySelectorAll('a, button').forEach(function (el) {
      if (el.hasAttribute('aria-label') || el.hasAttribute('aria-labelledby')) return;
      var text = (el.textContent || '').replace(/\s+/g, ' ').trim();
      if (!text) {
        var t = el.getAttribute('title');
        var img = el.querySelector('img[alt]');
        if (t) el.setAttribute('aria-label', t);
        else if (img && img.alt) el.setAttribute('aria-label', img.alt);
      }
    });

    // Links que abrem em nova aba avisam o usuário
    root.querySelectorAll('a[target="_blank"]').forEach(function (a) {
      if (a.dataset.a11yNewtab) return;
      a.dataset.a11yNewtab = '1';
      var rel = (a.getAttribute('rel') || '');
      if (rel.indexOf('noopener') === -1) a.setAttribute('rel', (rel + ' noopener noreferrer').trim());
      var label = a.getAttribute('aria-label');
      if (label) {
        if (label.indexOf('nova aba') === -1) a.setAttribute('aria-label', label + ' (abre em nova aba)');
      } else {
        var sr = document.createElement('span');
        sr.className = 'sr-only';
        sr.textContent = ' (abre em nova aba)';
        a.appendChild(sr);
      }
    });

    // Imagens sem alt: alt vazio = decorativa (evita o leitor ler o nome do arquivo)
    root.querySelectorAll('img:not([alt])').forEach(function (img) { img.setAttribute('alt', ''); });

    // Labels sem "for": liga ao campo mais próximo
    root.querySelectorAll('label:not([for])').forEach(function (label) {
      if (label.querySelector('input, select, textarea')) return;
      var scope = label.parentElement;
      if (!scope) return;
      var fields = scope.querySelectorAll('input:not([type="hidden"]), select, textarea');
      if (fields.length === 1) {
        label.setAttribute('for', ensureId(fields[0], 'campo'));
      }
    });

    // Campos obrigatórios anunciados como obrigatórios
    root.querySelectorAll('input[required], select[required], textarea[required]').forEach(function (f) {
      f.setAttribute('aria-required', 'true');
    });

    // Campos sem rótulo nenhum: usa o placeholder como nome acessível
    root.querySelectorAll('input:not([type="hidden"]):not([aria-label]), select:not([aria-label]), textarea:not([aria-label])').forEach(function (f) {
      var hasLabel = (f.id && document.querySelector('label[for="' + f.id + '"]')) || f.closest('label') || f.hasAttribute('aria-labelledby');
      if (!hasLabel && f.placeholder) f.setAttribute('aria-label', f.placeholder);
    });

    // Gráficos (canvas) recebem descrição a partir do título mais próximo
    root.querySelectorAll('canvas:not([aria-label])').forEach(function (c) {
      var box = c.closest('div');
      var h = box ? (box.parentElement || box).querySelector('h2, h3, h4') : null;
      c.setAttribute('role', 'img');
      c.setAttribute('aria-label', 'Gráfico' + (h ? ': ' + h.textContent.trim() : ''));
    });

    // Tabelas: cabeçalhos com escopo de coluna
    root.querySelectorAll('thead th:not([scope])').forEach(function (th) { th.setAttribute('scope', 'col'); });
  }

  // Modais existentes (notícias, mensagens, presidentes): vira "dialog" acessível
  function initModals() {
    var modals = document.querySelectorAll('[id$="-modal"], [id$="modal"]');
    modals.forEach(function (modal) {
      if (modal.id === 'a11y-panel') return;
      modal.setAttribute('role', 'dialog');
      modal.setAttribute('aria-modal', 'true');
      var title = modal.querySelector('h2, h3, [id*="titulo"], [id*="nome"]');
      if (title) modal.setAttribute('aria-labelledby', ensureId(title, 'modal-titulo'));
      modal.setAttribute('tabindex', '-1');

      var lastFocus = null;
      var wasHidden = modal.classList.contains('hidden');

      new MutationObserver(function () {
        var hidden = modal.classList.contains('hidden');
        if (hidden === wasHidden) return;
        wasHidden = hidden;
        if (!hidden) {
          lastFocus = document.activeElement;
          setTimeout(function () {
            var closeBtn = modal.querySelector('[aria-label*="echar"], #modal-close, button');
            (closeBtn || modal).focus();
          }, 50);
        } else if (lastFocus && document.body.contains(lastFocus)) {
          lastFocus.focus();
        }
      }).observe(modal, { attributes: true, attributeFilter: ['class'] });

      modal.addEventListener('keydown', function (e) {
        if (modal.classList.contains('hidden')) return;
        if (e.key === 'Escape') {
          var closeBtn = modal.querySelector('#modal-close, [aria-label*="echar"]');
          if (closeBtn) closeBtn.click();
        }
        if (e.key === 'Tab') {
          var f = modal.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), select, textarea, [tabindex]:not([tabindex="-1"])');
          if (!f.length) return;
          var first = f[0], last = f[f.length - 1];
          if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
          else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
        }
      });
    });
  }

  // Menu mobile: atualiza o nome do botão (Abrir/Fechar)
  function initMobileMenu() {
    var btn = document.getElementById('mobile-menu-btn');
    if (!btn) return;
    btn.setAttribute('aria-controls', 'mobile-menu');
    new MutationObserver(function () {
      var open = btn.getAttribute('aria-expanded') === 'true';
      var label = open ? 'Fechar menu' : 'Abrir menu';
      if (btn.getAttribute('aria-label') !== label) btn.setAttribute('aria-label', label);
    }).observe(btn, { attributes: true, attributeFilter: ['aria-expanded'] });
  }

  // Marca o link do menu da página atual
  function markCurrentPage() {
    var path = location.pathname.replace(/\/+$/, '').replace(/\.html$/, '');
    document.querySelectorAll('header nav a').forEach(function (a) {
      var href = (a.getAttribute('href') || '').replace(/\/+$/, '').replace(/\.html$/, '');
      if (href && href === path) a.setAttribute('aria-current', 'page');
    });
  }

  // Conteúdo principal: alvo do link "Pular para o conteúdo"
  function initMain() {
    var main = document.querySelector('main');
    if (main) {
      if (!main.id) main.id = 'conteudo-principal';
      main.setAttribute('tabindex', '-1');
    }
  }

  function boot() {
    initMain();
    initWidget();
    initMobileMenu();
    initModals();
    markCurrentPage();
    enhance(document);

    // Conteúdo criado depois (ícones Lucide, cards de notícias, CIDs) também é ajustado
    var pending = false;
    new MutationObserver(function () {
      if (pending) return;
      pending = true;
      requestAnimationFrame(function () { pending = false; enhance(document); });
    }).observe(document.body, { childList: true, subtree: true });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
