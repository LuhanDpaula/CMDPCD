<!-- ============================================================
     CHAT "POSSO AJUDAR?" - perguntas frequentes e pedidos de ajuda
     Dados no MongoDB (rotas /chat/*). Funciona por regras: a pessoa
     escolhe um assunto ou digita a dúvida e o sistema busca a resposta.
     ============================================================ -->
<button type="button"
        id="chat-toggle"
        class="chat-fab"
        aria-expanded="false"
        aria-controls="chat-panel"
        aria-label="Abrir chat: Posso ajudar?">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    <path d="M21 12a8 8 0 0 1-11.6 7.1L4 20.5l1.4-4.8A8 8 0 1 1 21 12z"/>
    <path d="M9.6 9.5a2.5 2.5 0 0 1 4.8 1c0 1.6-2.4 2-2.4 3.2"/>
    <circle cx="12" cy="16.6" r=".6" fill="currentColor"/>
  </svg>
  <span class="chat-fab-texto">Posso ajudar?</span>
</button>

<section id="chat-panel"
         class="chat-panel"
         role="dialog"
         aria-modal="false"
         aria-labelledby="chat-titulo"
         aria-describedby="chat-subtitulo"
         hidden>
  <header class="chat-header">
    <div>
      <h2 id="chat-titulo">Posso ajudar?</h2>
      <p id="chat-subtitulo">Perguntas frequentes do Conselho</p>
    </div>
    <button type="button" class="chat-fechar" id="chat-fechar" aria-label="Fechar chat">&times;</button>
  </header>

  <!-- Mensagens: role="log" faz o leitor de tela anunciar cada nova resposta -->
  <div id="chat-mensagens" class="chat-mensagens" role="log" aria-live="polite" aria-relevant="additions" tabindex="0" aria-label="Conversa"></div>

  <form id="chat-busca" class="chat-busca" role="search" autocomplete="off">
    <label for="chat-input" class="sr-only">Digite sua dúvida</label>
    <input id="chat-input" type="text" maxlength="200" placeholder="Digite sua dúvida...">
    <button type="submit" aria-label="Buscar resposta">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M22 2 11 13M22 2l-7 20-4-9-9-4z"/></svg>
    </button>
  </form>
  <p class="chat-rodape">Precisa falar agora? Ligue <a href="tel:+551436245077">(14) 3624-5077</a> · seg. a sex., 8h às 17h</p>
</section>
