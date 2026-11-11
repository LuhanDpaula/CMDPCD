<!-- ============================================================
     WIDGET DE ACESSIBILIDADE (botão flutuante)
     Ícone: Símbolo Internacional de Acessibilidade Universal (ONU),
     que representa TODAS as deficiências, não só a física.
     ============================================================ -->
<button type="button"
        id="a11y-toggle"
        class="a11y-fab"
        aria-label="Abrir menu de acessibilidade"
        aria-expanded="false"
        aria-controls="a11y-panel"
        title="Acessibilidade (Alt + A)">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    <circle cx="12" cy="12" r="10"/>
    <circle cx="12" cy="6.4" r="1.35" fill="currentColor" stroke="none"/>
    <path d="M6.8 9.4 12 10.5l5.2-1.1"/>
    <path d="M12 10.5v3.6"/>
    <path d="M9.3 18.2 12 14.1l2.7 4.1"/>
  </svg>
</button>

<div id="a11y-panel"
     class="a11y-panel"
     role="dialog"
     aria-modal="false"
     aria-labelledby="a11y-title"
     hidden>
  <h2 id="a11y-title">Acessibilidade</h2>
  <button type="button" class="a11y-close" data-a11y="fechar" aria-label="Fechar menu de acessibilidade">&times;</button>

  <ul role="list">
    <li>
      <button type="button" class="a11y-option" data-a11y="aumentar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M12 5v14M5 12h14"/></svg>
        Aumentar fonte
      </button>
    </li>
    <li>
      <button type="button" class="a11y-option" data-a11y="diminuir">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M5 12h14"/></svg>
        Diminuir fonte
      </button>
    </li>
    <li>
      <button type="button" class="a11y-option" data-a11y="padrao">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg>
        <span>Fonte padrão (<span id="a11y-font-pct">100%</span>)</span>
      </button>
    </li>
    <li>
      <button type="button" class="a11y-option" data-a11y="contraste" aria-pressed="false">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 0 1 0 18z" fill="currentColor"/></svg>
        Alto contraste
      </button>
    </li>

    <li class="a11y-sep" role="presentation"></li>

    <li>
      <button type="button" class="a11y-option" data-a11y="links" aria-pressed="false">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.7 1.7"/><path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.7-1.7"/></svg>
        Destacar links
      </button>
    </li>
    <li>
      <button type="button" class="a11y-option" data-a11y="legivel" aria-pressed="false">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M4 7V4h16v3M9 20h6M12 4v16"/></svg>
        Fonte legível
      </button>
    </li>
    <li>
      <button type="button" class="a11y-option" data-a11y="animacao" aria-pressed="false">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M9 5v14M15 5v14"/></svg>
        Pausar animações
      </button>
    </li>

    <li class="a11y-sep" role="presentation"></li>

    <li>
      <button type="button" class="a11y-option" data-a11y="resetar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M18 6 6 18M6 6l12 12"/></svg>
        Restaurar tudo
      </button>
    </li>
  </ul>
</div>

<!-- Região invisível que "fala" as mudanças para o leitor de tela -->
<div id="a11y-live" class="sr-only" role="status" aria-live="polite" aria-atomic="true"></div>
