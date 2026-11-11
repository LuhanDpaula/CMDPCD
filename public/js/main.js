// ============================================================
// SEGURANÇA
// ============================================================

// 1) Envia o token CSRF automaticamente em todo fetch() para o próprio site
(function () {
  const meta = document.querySelector('meta[name="csrf-token"]');
  if (!meta || !window.fetch) return;
  const token = meta.getAttribute('content');
  const originalFetch = window.fetch.bind(window);
  window.fetch = function (input, init) {
    init = init || {};
    const url = typeof input === 'string' ? input : (input && input.url) || '';
    const sameOrigin = !/^https?:\/\//i.test(url) || url.indexOf(location.origin) === 0;
    if (sameOrigin) {
      const headers = new Headers(init.headers || {});
      if (!headers.has('X-CSRF-Token')) headers.set('X-CSRF-Token', token);
      init.headers = headers;
      init.credentials = init.credentials || 'same-origin';
    }
    return originalFetch(input, init);
  };
})();

// 2) Confirmação antes de ações destrutivas (excluir, indeferir...)
//    Suporta tanto botões clicáveis ([data-confirm]) quanto submissão de formulários (<form data-confirm="...">)
document.addEventListener('click', (e) => {
  const el = e.target.closest('[data-confirm]');
  if (!el) return;
  if (!window.confirm(el.getAttribute('data-confirm'))) {
    e.preventDefault();
    e.stopImmediatePropagation();
  }
}, true);

document.addEventListener('submit', (e) => {
  const form = e.target;
  const msg = form ? form.getAttribute('data-confirm') : null;
  if (msg && !window.confirm(msg)) {
    e.preventDefault();
    e.stopImmediatePropagation();
  }
}, true);

document.addEventListener('DOMContentLoaded', () => {
  // Inicializar os ícones do Lucide
  if (typeof lucide !== 'undefined') {
    lucide.createIcons();
  }

  // Configurar ano atual no rodapé
  const yearElement = document.getElementById('current-year');
  if (yearElement) {
    yearElement.textContent = new Date().getFullYear();
  }

  // Lógica do Menu Mobile
  const mobileMenuBtn = document.getElementById('mobile-menu-btn');
  const mobileMenu = document.getElementById('mobile-menu');

  if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener('click', () => {
      const isExpanded = mobileMenuBtn.getAttribute('aria-expanded') === 'true';
      mobileMenuBtn.setAttribute('aria-expanded', !isExpanded);
      
      if (isExpanded) {
        mobileMenu.classList.add('hidden');
        mobileMenuBtn.innerHTML = '<i data-lucide="menu" class="w-6 h-6" aria-hidden="true"></i>';
        mobileMenuBtn.setAttribute('aria-label', 'Abrir menu');
      } else {
        mobileMenu.classList.remove('hidden');
        mobileMenuBtn.innerHTML = '<i data-lucide="x" class="w-6 h-6" aria-hidden="true"></i>';
        mobileMenuBtn.setAttribute('aria-label', 'Fechar menu');
      }
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
    });
  }

  // Animações simples de Scroll (Fade In)
  const observerOptions = {
    root: null,
    rootMargin: '0px',
    threshold: 0.1
  };

  const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  const animatedElements = document.querySelectorAll('.fade-in-up, .fade-in-left, .fade-in-right, .zoom-in');
  animatedElements.forEach(el => observer.observe(el));
});
