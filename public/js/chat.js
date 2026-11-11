/* ============================================================
   CHAT "POSSO AJUDAR?" - CMDPCD Jahu
   - Busca respostas no MongoDB pelas rotas /chat/*
   - Todo texto vindo do servidor é inserido com textContent (sem XSS)
   - Acessível: role="log" anuncia mensagens, Esc fecha, foco gerenciado
   ============================================================ */
(function () {
  'use strict';

  var toggle = document.getElementById('chat-toggle');
  var panel = document.getElementById('chat-panel');
  if (!toggle || !panel) return;

  var log = document.getElementById('chat-mensagens');
  var form = document.getElementById('chat-busca');
  var input = document.getElementById('chat-input');
  var fechar = document.getElementById('chat-fechar');
  var BASE = (document.querySelector('meta[name="base-url"]') || {}).content || '';
  var TOKEN = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

  var dados = null;        // categorias, assuntos, texto de consentimento
  var iniciado = false;
  var ocupado = false;

  // ------------------------------------------------------------
  // Utilidades de interface
  // ------------------------------------------------------------
  function el(tag, cls, text) {
    var e = document.createElement(tag);
    if (cls) e.className = cls;
    if (text !== undefined && text !== null) e.textContent = text;
    return e;
  }

  function rolar() {
    log.scrollTop = log.scrollHeight;
  }

  function msgBot(texto) {
    var m = el('div', 'chat-msg bot', texto);
    log.appendChild(m);
    rolar();
    return m;
  }

  function msgUsuario(texto) {
    var m = el('div', 'chat-msg usuario', texto);
    m.setAttribute('aria-label', 'Você: ' + texto);
    log.appendChild(m);
    rolar();
  }

  /** Grupo de botões. itens = [{label, onClick, destaque}] */
  function opcoes(itens, emLinha) {
    var box = el('div', 'chat-opcoes' + (emLinha ? ' linha' : ''));
    itens.forEach(function (it) {
      var b = el('button', 'chat-opcao' + (it.destaque ? ' destaque' : ''), it.label);
      b.type = 'button';
      b.addEventListener('click', function () {
        if (ocupado) return;
        // desativa o grupo depois de escolher (evita cliques duplos)
        if (!it.manter) box.querySelectorAll('button').forEach(function (x) { x.disabled = true; });
        it.onClick(b);
      });
      box.appendChild(b);
    });
    log.appendChild(box);
    rolar();
    return box;
  }

  function digitando() {
    var d = el('div', 'chat-digitando', 'Procurando...');
    d.setAttribute('aria-hidden', 'true');
    log.appendChild(d);
    rolar();
    return d;
  }

  function api(url, opts) {
    opts = opts || {};
    opts.headers = Object.assign({ 'Accept': 'application/json', 'X-CSRF-Token': TOKEN }, opts.headers || {});
    opts.credentials = 'same-origin';
    return fetch(BASE + url, opts).then(function (r) {
      return r.json().catch(function () { return { success: false }; });
    }).catch(function () {
      return { success: false, message: 'Sem conexão. Verifique sua internet ou ligue para (14) 3624-5077.' };
    });
  }

  function erroGenerico(res) {
    msgBot((res && res.message) || 'Não consegui carregar agora. Tente novamente ou ligue para (14) 3624-5077.');
  }

  // ------------------------------------------------------------
  // Fluxo da conversa
  // ------------------------------------------------------------
  function iniciar() {
    iniciado = true;
    msgBot('Olá! 👋 Sou o assistente do Conselho Municipal da Pessoa com Deficiência de Jahu.\nEscolha um assunto abaixo ou digite sua dúvida.');
    var d = digitando();
    api('/chat/categorias').then(function (res) {
      d.remove();
      if (!res.success) { erroGenerico(res); return; }
      dados = res;
      menuPrincipal();
    });
  }

  function menuPrincipal() {
    if (!dados) return;
    var itens = dados.categorias.map(function (c) {
      return {
        label: c.label,
        onClick: function () { msgUsuario(c.label); abrirCategoria(c); }
      };
    });
    itens.push({ label: '🙋 Quero ajuda do Conselho', destaque: true, onClick: function () { msgUsuario('Quero ajuda do Conselho'); formularioPedido(); } });
    opcoes(itens);
  }

  function abrirCategoria(cat) {
    var d = digitando();
    api('/chat/perguntas?categoria=' + encodeURIComponent(cat.id)).then(function (res) {
      d.remove();
      if (!res.success) { erroGenerico(res); return; }
      msgBot('Estas são as perguntas sobre "' + cat.label + '". Qual é a sua dúvida?');
      listaPerguntas(res.perguntas, true);
    });
  }

  function listaPerguntas(perguntas, comVoltar) {
    var itens = perguntas.map(function (p) {
      return { label: p.pergunta, onClick: function () { msgUsuario(p.pergunta); mostrarResposta(p.id); } };
    });
    if (comVoltar) itens.push({ label: '↩ Ver outros assuntos', onClick: function () { msgUsuario('Ver outros assuntos'); menuPrincipal(); } });
    opcoes(itens);
  }

  function mostrarResposta(id) {
    var d = digitando();
    api('/chat/resposta?id=' + encodeURIComponent(id)).then(function (res) {
      d.remove();
      if (!res.success) { erroGenerico(res); return; }
      var m = el('div', 'chat-msg bot');
      m.appendChild(el('span', 'chat-titulo-resposta', res.pergunta));
      m.appendChild(document.createTextNode(res.resposta));
      log.appendChild(m);
      rolar();
      perguntarSeAjudou(res.id);
    });
  }

  function perguntarSeAjudou(id) {
    msgBot('Essa resposta ajudou?');
    opcoes([
      { label: '👍 Sim', onClick: function () { msgUsuario('Sim, ajudou'); avaliar(id, true); } },
      { label: '👎 Não', onClick: function () { msgUsuario('Não ajudou'); avaliar(id, false); } }
    ], true);
  }

  function avaliar(id, util) {
    var fd = new FormData();
    fd.append('id', id);
    fd.append('util', util ? '1' : '0');
    fd.append('_token', TOKEN);
    api('/chat/avaliar', { method: 'POST', body: fd });
    if (util) {
      msgBot('Que bom! 😊 Posso ajudar com mais alguma coisa?');
      menuPrincipal();
    } else {
      msgBot('Sinto muito. Você pode escolher outro assunto, digitar a dúvida com outras palavras ou pedir para o Conselho entrar em contato com você.');
      menuPrincipal();
    }
  }

  function buscar(texto) {
    msgUsuario(texto);
    var d = digitando();
    ocupado = true;
    api('/chat/buscar?q=' + encodeURIComponent(texto)).then(function (res) {
      d.remove();
      ocupado = false;
      if (!res.success) { erroGenerico(res); return; }
      if (res.resultados.length) {
        msgBot(res.resultados.length === 1 ? 'Encontrei esta pergunta:' : 'Encontrei estas perguntas parecidas. Escolha uma:');
        listaPerguntas(res.resultados, false);
        opcoes([{ label: 'Nenhuma dessas', onClick: function () { msgUsuario('Nenhuma dessas'); naoEncontrado(); } }]);
      } else {
        naoEncontrado();
      }
    });
  }

  function naoEncontrado() {
    msgBot('Não encontrei uma resposta para isso. 😕\nVou avisar o Conselho para incluir essa dúvida nas perguntas frequentes.\n\nEnquanto isso, você pode escolher um assunto ou pedir para o Conselho entrar em contato com você.');
    if (!dados) { iniciar(); return; }
    menuPrincipal();
  }

  // ------------------------------------------------------------
  // Formulário "Quero ajuda do Conselho"
  // ------------------------------------------------------------
  var formCount = 0;

  function campo(f, id, label, tipo, attrs, dica) {
    var wrap = el('div');
    var lab = el('label', null, label);
    lab.htmlFor = id;
    wrap.appendChild(lab);
    var inp;
    if (tipo === 'textarea') {
      inp = el('textarea');
    } else if (tipo === 'select') {
      inp = el('select');
    } else {
      inp = el('input');
      inp.type = tipo;
    }
    inp.id = id;
    Object.keys(attrs || {}).forEach(function (k) { inp.setAttribute(k, attrs[k]); });
    wrap.appendChild(inp);
    if (dica) {
      var p = el('p', 'chat-dica', dica);
      p.id = id + '-dica';
      inp.setAttribute('aria-describedby', p.id);
      wrap.appendChild(p);
    }
    f.appendChild(wrap);
    return inp;
  }

  function radios(f, nome, legenda, opcoesObj, padrao) {
    var fs = el('fieldset');
    fs.appendChild(el('legend', null, legenda));
    var box = el('div', 'chat-radios');
    Object.keys(opcoesObj).forEach(function (k) {
      var lab = el('label');
      var r = el('input');
      r.type = 'radio'; r.name = nome; r.value = k;
      if (k === padrao) r.checked = true;
      lab.appendChild(r);
      lab.appendChild(document.createTextNode(opcoesObj[k]));
      box.appendChild(lab);
    });
    fs.appendChild(box);
    f.appendChild(fs);
  }

  function formularioPedido() {
    if (!dados) { iniciar(); return; }
    formCount++;
    var p = 'pedido' + formCount + '-';

    msgBot('Certo! Preencha seus dados e o Conselho vai entrar em contato com você.\n\nO contato não é imediato: a equipe atende os pedidos conforme a demanda, em dias úteis.');

    var f = el('form', 'chat-form');
    f.setAttribute('aria-label', 'Pedido de ajuda ao Conselho');
    f.noValidate = true;

    var nome = campo(f, p + 'nome', 'Nome *', 'text', { name: 'nome', maxlength: '120', autocomplete: 'name', required: '' });
    var tel = campo(f, p + 'tel', 'Telefone com DDD *', 'tel', { name: 'telefone', maxlength: '20', autocomplete: 'tel', inputmode: 'tel', required: '' }, 'Ex.: (14) 99999-0000');
    campo(f, p + 'bairro', 'Bairro *', 'text', { name: 'bairro', maxlength: '80', required: '' });
    campo(f, p + 'endereco', 'Endereço (opcional)', 'text', { name: 'endereco', maxlength: '200', autocomplete: 'street-address' }, 'Só se precisar de visita.');

    var assunto = campo(f, p + 'assunto', 'Assunto *', 'select', { name: 'assunto', required: '' });
    var op0 = el('option', null, 'Selecione'); op0.value = ''; assunto.appendChild(op0);
    Object.keys(dados.assuntos).forEach(function (k) {
      var o = el('option', null, dados.assuntos[k]); o.value = k; assunto.appendChild(o);
    });

    // Assunto de violação de direitos: mostra canais de emergência
    var aviso = el('div', 'chat-alerta');
    aviso.setAttribute('role', 'alert');
    aviso.hidden = true;
    aviso.textContent = 'Se for uma situação de violência ou perigo agora, ligue 190. Para denúncias de violação de direitos, ligue Disque 100 (gratuito, 24h).';
    f.appendChild(aviso);
    assunto.addEventListener('change', function () { aviso.hidden = assunto.value !== 'direitos'; });

    campo(f, p + 'msg', 'Como podemos ajudar? *', 'textarea', { name: 'mensagem', maxlength: '1000', required: '' });

    radios(f, 'para_quem', 'O pedido é para:', { propria: 'Mim', outra: 'Outra pessoa (sou familiar/responsável)' }, 'propria');
    radios(f, 'preferencia_contato', 'Prefere receber contato por:', { ligacao: 'Ligação', whatsapp: 'WhatsApp' }, 'ligacao');
    radios(f, 'melhor_horario', 'Melhor horário:', { manha: 'Manhã', tarde: 'Tarde', qualquer: 'Qualquer' }, 'qualquer');

    // Armadilha anti-robô (invisível e ignorada por leitores de tela)
    var hp = el('div', 'chat-honeypot');
    hp.setAttribute('aria-hidden', 'true');
    var hpi = el('input'); hpi.type = 'text'; hpi.name = 'website'; hpi.tabIndex = -1; hpi.autocomplete = 'off';
    hp.appendChild(hpi);
    f.appendChild(hp);

    var chk = el('label', 'chat-check');
    var cb = el('input'); cb.type = 'checkbox'; cb.name = 'consentimento'; cb.value = '1'; cb.required = true;
    chk.appendChild(cb);
    chk.appendChild(document.createTextNode(dados.consentimento + ' *'));
    f.appendChild(chk);

    var erro = el('p', 'chat-erro');
    erro.setAttribute('role', 'alert');
    f.appendChild(erro);

    var enviar = el('button', 'chat-opcao destaque', 'Enviar pedido');
    enviar.type = 'submit';
    f.appendChild(enviar);

    var cancelar = el('button', 'chat-opcao', 'Cancelar');
    cancelar.type = 'button';
    cancelar.addEventListener('click', function () {
      f.remove();
      msgBot('Tudo bem, pedido cancelado. Posso ajudar com mais alguma coisa?');
      menuPrincipal();
    });
    f.appendChild(cancelar);

    f.addEventListener('input', function () { erro.textContent = ''; });

    f.addEventListener('submit', function (ev) {
      ev.preventDefault();
      erro.textContent = '';
      if (!f.checkValidity()) {
        var inval = f.querySelector(':invalid');
        erro.textContent = inval === cb
          ? 'É preciso autorizar o uso dos dados para o Conselho entrar em contato.'
          : 'Preencha os campos obrigatórios (*).';
        if (inval) inval.focus();
        return;
      }
      enviar.disabled = true;
      enviar.textContent = 'Enviando...';
      var fd = new FormData(f);
      fd.append('_token', TOKEN);
      api('/chat/pedido', { method: 'POST', body: fd }).then(function (res) {
        if (res.success) {
          f.remove();
          var m = msgBot('');
          m.appendChild(document.createTextNode('✅ Pedido registrado!\n\nSeu protocolo é '));
          m.appendChild(el('strong', null, res.protocolo));
          m.appendChild(document.createTextNode('.\n\nO Conselho vai entrar em contato pelo telefone informado, conforme a ordem de atendimento. Se precisar, ligue (14) 3624-5077 e informe o protocolo.'));
          m.setAttribute('tabindex', '-1');
          menuPrincipalSimples();
          m.focus();
        } else {
          enviar.disabled = false;
          enviar.textContent = 'Enviar pedido';
          erro.textContent = res.message || 'Não foi possível enviar. Tente novamente.';
        }
      });
    });

    log.appendChild(f);
    rolar();
    nome.focus();
  }

  function menuPrincipalSimples() {
    opcoes([{ label: 'Ver perguntas frequentes', onClick: function () { msgUsuario('Ver perguntas frequentes'); menuPrincipal(); } }]);
  }

  // ------------------------------------------------------------
  // Abrir / fechar
  // ------------------------------------------------------------
  // Chat e menu de acessibilidade ficam no mesmo canto: abrir um fecha o outro
  var a11yToggle = document.getElementById('a11y-toggle');
  var a11yPanel = document.getElementById('a11y-panel');

  if (a11yToggle) {
    a11yToggle.addEventListener('click', function () {
      if (!panel.hidden) {
        panel.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Abrir chat: Posso ajudar?');
      }
    });
  }

  function abrir() {
    if (a11yPanel && !a11yPanel.hidden && a11yToggle) a11yToggle.click();
    panel.hidden = false;
    toggle.setAttribute('aria-expanded', 'true');
    toggle.setAttribute('aria-label', 'Fechar chat');
    if (!iniciado) iniciar();
    setTimeout(function () { input.focus(); }, 50);
  }

  function fecharChat() {
    panel.hidden = true;
    toggle.setAttribute('aria-expanded', 'false');
    toggle.setAttribute('aria-label', 'Abrir chat: Posso ajudar?');
    toggle.focus();
  }

  toggle.addEventListener('click', function () { panel.hidden ? abrir() : fecharChat(); });
  fechar.addEventListener('click', fecharChat);
  // Esc fecha o chat de qualquer lugar da página enquanto ele estiver aberto
  // (o painel de acessibilidade tem prioridade se também estiver aberto)
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape' || panel.hidden) return;
    var a11y = document.getElementById('a11y-panel');
    if (a11y && !a11y.hidden) return;
    e.preventDefault();
    fecharChat();
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var t = input.value.trim();
    if (t.length < 2 || ocupado) return;
    input.value = '';
    if (!dados && !iniciado) iniciar();
    buscar(t);
  });
})();
