document.addEventListener('DOMContentLoaded', () => {
  
  // Constantes de chaves do localStorage
  const SESSION_KEY = "cmpcd_admin_session";
  const CADASTROS_KEY = "cmpcd_cadastros";
  const USERS_KEY = "cmpcd_admin_users";

  // Usuários Padrão (caso não exista no localStorage)
  const defaultUsers = [
    {
      id: "1",
      username: "admin",
      password: "admin",
      name: "Administrador",
      role: "admin_total",
      createdAt: new Date().toISOString()
    }
  ];

  function getAdminUsers() {
    const stored = localStorage.getItem(USERS_KEY);
    if (!stored) {
      localStorage.setItem(USERS_KEY, JSON.stringify(defaultUsers));
      return defaultUsers;
    }
    return JSON.parse(stored);
  }

  // Elementos da UI
  const loginView = document.getElementById('login-view');
  const panelView = document.getElementById('panel-view');
  
  const loginForm = document.getElementById('admin-login-form');
  const loginError = document.getElementById('login-error');
  const btnLogout = document.getElementById('btn-logout');
  
  const adminNameDisplay = document.getElementById('admin-name-display');

  // Gerenciamento de Sessão
  function getSession() {
    const stored = localStorage.getItem(SESSION_KEY);
    return stored ? JSON.parse(stored) : null;
  }

  function setSession(user) {
    localStorage.setItem(SESSION_KEY, JSON.stringify(user));
  }

  function clearSession() {
    localStorage.removeItem(SESSION_KEY);
  }

  // Navegação de Abas
  const tabBtns = document.querySelectorAll('.admin-tab-btn');
  const tabContents = document.querySelectorAll('.admin-tab-content');

  function switchTab(tabId) {
    tabBtns.forEach(btn => {
      if (btn.dataset.tab === tabId) {
        btn.classList.remove('bg-card', 'text-foreground', 'border');
        btn.classList.add('bg-primary', 'text-primary-foreground');
      } else {
        btn.classList.add('bg-card', 'text-foreground', 'border');
        btn.classList.remove('bg-primary', 'text-primary-foreground');
      }
    });

    tabContents.forEach(content => {
      if (content.id === `tab-${tabId}`) {
        content.classList.remove('hidden');
      } else {
        content.classList.add('hidden');
      }
    });
  }

  tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      switchTab(btn.dataset.tab);
    });
  });

  // Atualiza as Telas com base no estado da sessão
  function updateUI() {
    const user = getSession();
    if (user) {
      loginView.classList.add('hidden');
      panelView.classList.remove('hidden');
      adminNameDisplay.textContent = user.name;
      
      // Carregar os dados das abas
      loadDashboardData();
      loadCadastros();
      loadUsers();
      
      // Recriar ícones lucide caso novos tenham sido injetados
      if (window.lucide) lucide.createIcons();
    } else {
      loginView.classList.remove('hidden');
      panelView.classList.add('hidden');
    }
  }

  // Evento de Login
  if (loginForm) {
    loginForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const userVal = document.getElementById('login-username').value.trim();
      const passVal = document.getElementById('login-password').value.trim();
      
      const users = getAdminUsers();
      const authUser = users.find(u => u.username === userVal && u.password === passVal);
      
      if (authUser) {
        loginError.classList.add('hidden');
        setSession(authUser);
        updateUI();
      } else {
        loginError.classList.remove('hidden');
      }
    });
  }

  // Evento de Logout
  if (btnLogout) {
    btnLogout.addEventListener('click', () => {
      clearSession();
      updateUI();
    });
  }

  // --------------------------------------------------------------------------
  // LÓGICA DAS ABAS
  // --------------------------------------------------------------------------

  // 1. DASHBOARD
  let chartMes = null;
  let chartTipo = null;

  function loadDashboardData() {
    const cadastrosStr = localStorage.getItem(CADASTROS_KEY);
    const cadastros = cadastrosStr ? JSON.parse(cadastrosStr) : [];
    
    document.getElementById('stat-total-cadastros').textContent = cadastros.length;
    
    // Inicializar Gráficos (somente se ainda não existem e o Chart.js foi carregado)
    if (window.Chart) {
      const ctxMes = document.getElementById('admin-chart-mes');
      const ctxTipo = document.getElementById('admin-chart-tipo');

      if (ctxMes && !chartMes) {
        chartMes = new Chart(ctxMes, {
          type: 'bar',
          data: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
            datasets: [{
              label: 'Cadastros',
              data: [12, 19, 15, 25, 22, cadastros.length],
              backgroundColor: 'hsl(204, 67%, 52%)',
              borderRadius: 8
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } } }
          }
        });
      }

      if (ctxTipo && !chartTipo) {
        chartTipo = new Chart(ctxTipo, {
          type: 'doughnut',
          data: {
            labels: ['Física', 'Auditiva', 'Visual', 'Intelectual', 'Múltipla', 'Outra'],
            datasets: [{
              data: [35, 20, 15, 20, 5, 5],
              backgroundColor: [
                '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#64748b'
              ],
              borderWidth: 0
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
            },
            cutout: '70%'
          }
        });
      }
    }
  }

  // 2. CADASTROS
  function loadCadastros() {
    const tbody = document.getElementById('cadastros-tbody');
    if (!tbody) return;

    const cadastrosStr = localStorage.getItem(CADASTROS_KEY);
    const cadastros = cadastrosStr ? JSON.parse(cadastrosStr) : [];

    if (cadastros.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="4" class="px-6 py-8 text-center text-muted-foreground">
            Nenhum cadastro encontrado.
          </td>
        </tr>
      `;
      return;
    }

    tbody.innerHTML = cadastros.map(c => {
      // O formulário PCD gera um campo "dadosPessoais" com "nomeCompleto", "cpf", "dataNascimento"
      const nome = c.dadosPessoais ? c.dadosPessoais.nomeCompleto : 'Desconhecido';
      const cpf = c.dadosPessoais ? c.dadosPessoais.cpf : '---';
      const dataStr = c.id ? new Date(c.id).toLocaleDateString('pt-BR') : 'Recente';
      
      return `
        <tr class="hover:bg-muted/50 transition-colors">
          <td class="px-6 py-4 font-semibold text-foreground">${nome}</td>
          <td class="px-6 py-4 text-muted-foreground">${cpf}</td>
          <td class="px-6 py-4 text-muted-foreground">${dataStr}</td>
          <td class="px-6 py-4">
            <span class="inline-flex items-center gap-1 bg-green-500/10 text-green-600 text-xs font-bold px-2.5 py-1 rounded-full">
              <i data-lucide="check" class="w-3 h-3"></i> Recebido
            </span>
          </td>
        </tr>
      `;
    }).join('');
  }

  // 3. USUÁRIOS
  function loadUsers() {
    const tbody = document.getElementById('users-tbody');
    if (!tbody) return;

    const users = getAdminUsers();
    
    tbody.innerHTML = users.map(u => {
      const roleName = u.role === 'admin_total' ? 'Admin Total' : 
                       u.role === 'admin_parcial' ? 'Admin Parcial' : 'Editor';
                       
      return `
        <tr class="hover:bg-muted/50 transition-colors">
          <td class="px-6 py-4 font-semibold text-foreground flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
              ${u.name.charAt(0)}
            </div>
            ${u.name}
          </td>
          <td class="px-6 py-4 text-muted-foreground">@${u.username}</td>
          <td class="px-6 py-4">
            <span class="inline-block bg-primary/10 text-primary text-xs font-bold px-3 py-1 rounded-full">
              ${roleName}
            </span>
          </td>
        </tr>
      `;
    }).join('');
  }

  // Iniciar checando sessão
  updateUI();
});
