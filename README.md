# CMDPCD Jahu - Portal & Painel Administrativo

Este é o portal institucional do **Conselho Municipal dos Direitos da Pessoa com Deficiência (CMDPCD) de Jahu**, desenvolvido em arquitetura MVC utilizando PHP puro, HTML5, CSS customizado e JavaScript. 

O projeto conta com um portal público e um painel de administração completo para gerenciamento de conteúdos (notícias, composição de conselheiros, atas e resoluções), gerenciamento de usuários com controle de níveis de acesso e um formulário de cadastro estruturado para moderação de pessoas com deficiência.

---

## 🚀 Como Rodar o Projeto Localmente (XAMPP)

O sistema foi arquitetado para ser auto-configurável, facilitando a execução em qualquer máquina local que utilize o XAMPP.

### Passo 1: Copiar o Projeto
Extraia a pasta do projeto no diretório raiz do seu servidor local:
- No Windows (XAMPP): `C:\xampp\htdocs\CMDPCD_SITE`

### Passo 2: Iniciar os Serviços no Painel do XAMPP
Abra o painel de controle do XAMPP e inicie os seguintes módulos:
1. **Apache**
2. **MySQL**

### Passo 3: Acessar no Navegador
Abra seu navegador de preferência e acesse:
- **[http://localhost/CMDPCD_SITE/](http://localhost/CMDPCD_SITE/)**

> [!NOTE]  
> **Auto-Configuração do Banco de Dados**: Ao acessar o site pela primeira vez, o próprio sistema PHP se conectará ao seu MySQL (`host: localhost`, `user: root` e senha vazia `""` ou `"root"`), criará o banco de dados `cmpcd_jau` automaticamente, executará a estrutura de tabelas em `database/CMDPCD_PI_BD.sql` e depois populará os dados iniciais através do seeder `database/seeder.sql`.

---

## 💾 Gestão e Carga do Banco de Dados (CLI & Manual)

Para desenvolvedores e administradores, o repositório foi separado em **Estrutura (Schema)** e **Dados Iniciais (Seeds)**.

### Método 1: Linha de Comando (PHP CLI - Recomendado)
Abra o terminal na pasta raiz do projeto (`C:\xampp\htdocs\CMDPCD_SITE`) e execute:

* **Resetar banco do zero (Estrutura + Seeds)**:
  ```bash
  php database/reset_db.php
  ```
  *Este comando remove o banco `cmpcd_jau` se ele existir, recria-o vazio, aplica a estrutura (migrations) e insere todos os dados fictícios de demonstração (seeds).*

* **Executar apenas Migrations (Cria estrutura vazia)**:
  ```bash
  php database/migrate.php
  ```
  *Carrega o arquivo `database/CMDPCD_PI_BD.sql`.*

* **Executar apenas Seeds (Popula o banco)**:
  ```bash
  php database/seeder.php
  ```
  *Carrega o arquivo `database/seeder.sql`.*

### Método 2: Importação Manual (via phpMyAdmin)
Se preferir usar a interface gráfica do phpMyAdmin:
1. Acesse o **phpMyAdmin** em `http://localhost/phpmyadmin/`
2. Crie uma base de dados chamada `cmpcd_jau`
3. Selecione a base criada e vá na aba **Importar**
4. Escolha o arquivo da estrutura: **`database/CMDPCD_PI_BD.sql`** e clique em **Executar**
5. (Opcional para dados de demonstração) Vá novamente em **Importar**, escolha o arquivo contendo os dados: **`database/seeder.sql`** e clique em **Executar**

---

## 🔑 Credenciais para Teste (Painel Administrativo)

Para acessar o painel restrito e gerenciar o portal, acesse **[http://localhost/CMDPCD_SITE/admin](http://localhost/CMDPCD_SITE/admin)** ou clique em **Entrar** no menu e utilize os seguintes dados de login padrão:

- **E-mail**: `admin@admin.com`
- **Senha**: `admin`

> [!WARNING]
> Essa senha é só para demonstração. **Troque-a antes de publicar o site** e crie novos usuários com senha de no mínimo 8 caracteres.

---

## 📁 Estrutura de Pastas do Projeto

O portal segue a especificação MVC (Model-View-Controller) com rotas centralizadas no front controller:

- `app/`
  - `controllers/` - Lógica de controle e fluxo de requisições.
  - `models/` - Conexão e transações com o banco de dados.
  - `views/` - Telas do site público e do painel de administração em PHP/HTML.
- `config/` - Arquivos de configuração de conexão de banco de dados e arquivos de cache.
- `css/` - Estilização customizada em CSS vanilla do portal (`utilitarios.css` completa classes que faltavam no `style.css`).
- `database/` - Contém o dump SQL (`CMDPCD_PI_BD.sql`) para importação da base de dados.
- `js/` - Scripts front-end e interações reativas.
- `public/` - Diretório público contendo o front controller `index.php`, uploads físicos e assets de mídia.
- `routes/` - Mapeamento de rotas amigáveis da plataforma.
- `index.php` - Redirecionamento da raiz da URL.
- `.htaccess` - Configurações de reescrita de URL amigáveis (Apache).

---

## 🔒 Segurança

- **Documentos dos PCDs (RG, laudo, comprovante)** ficam em `storage/pcd_docs/`, fora da pasta pública. Só abrem pela rota `/admin/pcds/documento?id=`, que exige login de administrador.
- **Pastas internas bloqueadas** (`app/`, `config/`, `database/`, `routes/`, `storage/`) via `.htaccess`. Em produção, o ideal é apontar o DocumentRoot do servidor direto para `/public`.
- **SQL Injection:** todas as consultas usam *prepared statements* (PDO).
- **XSS:** toda saída é escapada com `e()`; o conteúdo das notícias passa por `sanitize_html()` (remove scripts, eventos e links `javascript:`).
- **CSRF:** todo formulário e toda ação de excluir/deferir/indeferir/sair exigem token (`csrf_field()` / `csrf_query()`).
- **Login:** máximo de 5 tentativas erradas a cada 15 min por IP, novo ID de sessão ao entrar, cookie `HttpOnly` + `SameSite`, sessão expira após 30 min sem uso.
- **Uploads:** tipo real do arquivo verificado (não só a extensão), nomes aleatórios, PHP desligado na pasta de uploads.
- **LGPD:** o cadastro público não revela se um CPF já está na base; erros técnicos não aparecem para o visitante (vão para o log).
- **Scripts de banco** (`database/*.php`) só rodam pelo terminal.
- **Credenciais do banco:** copie `config/db.local.example.php` para `config/db.local.php` e use um usuário MySQL próprio (não o root) em produção.

Funções de segurança: `app/helpers/security.php`.

## ♿ Acessibilidade

- **Botão flutuante** (canto inferior direito, atalho **Alt + A**) com o Símbolo Internacional de Acessibilidade Universal: aumentar/diminuir fonte, fonte padrão, alto contraste, destacar links, fonte legível, pausar animações e restaurar tudo. As preferências ficam salvas no navegador.
- **Leitores de tela (NVDA, JAWS, VoiceOver, TalkBack):** link "Pular para o conteúdo", ícones decorativos ocultos, nomes acessíveis em botões só com ícone, labels ligados aos campos, campos obrigatórios anunciados, modais como `dialog` (Esc fecha, foco preso), avisos falados (`aria-live`), página atual marcada no menu e aviso de "abre em nova aba".
- Respeita a configuração "reduzir movimento" do sistema operacional.

Arquivos: `app/views/partials/a11y_widget.php`, `public/css/a11y.css`, `public/js/a11y.js`.

---

## 🍃 MongoDB (Chat "Posso ajudar?" e Pedidos de Ajuda)

O projeto usa **persistência poliglota**:

| Banco | O que guarda | Por quê |
|---|---|---|
| **MySQL** | PCDs, usuários, notícias, membros, documentos | Dados muito relacionados, com regras fixas e transações |
| **MongoDB** | Perguntas frequentes, pedidos de ajuda, dúvidas sem resposta, resumo mensal | Estrutura flexível, histórico dentro do documento, busca por texto e expiração automática (TTL) |

Os dois bancos são independentes: nenhum dado pessoal dos PCDs vai para o MongoDB. Se o MongoDB estiver desligado, o site continua funcionando; só o chat e a aba de pedidos ficam indisponíveis.

### Passo 1: Instalar o MongoDB
Escolha **uma** opção:
- **Local:** instale o *MongoDB Community Server* (site oficial do MongoDB). Ele roda em `mongodb://127.0.0.1:27017`, que já é o padrão do projeto.
- **Nuvem (MongoDB Atlas, gratuito):** crie um cluster, libere seu IP e copie a *connection string* para `mongo_uri` em `config/db.local.php` (use o modelo `config/db.local.example.php`). Bom para o grupo todo usar o mesmo banco.

### Passo 2: Ativar a extensão `mongodb` no PHP do XAMPP
O projeto usa só a extensão nativa do PHP (**não precisa de Composer**).
1. Descubra sua versão do PHP e se ela é *Thread Safe*: abra `http://localhost/dashboard/phpinfo.php` e veja **PHP Version**, **Architecture** (x64) e **Thread Safety** (no XAMPP normalmente é *enabled* = TS).
2. Baixe o arquivo `php_mongodb.dll` compatível (mesma versão do PHP, **TS**, **x64**) na página de *releases* do driver oficial: github.com/mongodb/mongo-php-driver/releases.
3. Copie o `php_mongodb.dll` para `C:\xampp\php\ext\`.
4. Abra `C:\xampp\php\php.ini` e adicione a linha: `extension=mongodb`
5. Reinicie o Apache no painel do XAMPP.
6. Confira no terminal: `php -m` deve listar **mongodb**. Se a versão da DLL não bater com o PHP, o Apache mostra um aviso ao iniciar; baixe a versão correta.

### Passo 3: Criar índices e carregar as perguntas
Na pasta do projeto:
```bash
php database/mongo_setup.php
```
Mostra cada índice criado (`OK`) e quantos documentos existem em cada coleção. Isso também acontece automaticamente no primeiro acesso ao chat.
Para apagar e recarregar as 30 perguntas iniciais: `php database/mongo_setup.php --resetar-faq`

Alternativa pelo terminal do MongoDB:
```bash
mongoimport --db cmpcd_jau --collection faq --jsonArray --file database/mongo/faq_seed.json
```

### Coleções

| Coleção | Conteúdo | Recursos do MongoDB usados |
|---|---|---|
| `faq` | Perguntas e respostas do chat | CRUD, **índice de texto em português** (`$text`, com pesos), `$inc` (visualizações e "ajudou?") |
| `pedidos_ajuda` | Pedidos "Quero ajuda do Conselho" | Array `historico` com `$push`, `$set`/`$unset` no status, **índice TTL** (apaga 1 ano após o encerramento), índice composto da fila, índice único do protocolo |
| `perguntas_sem_resposta` | O que foi digitado e o chat não achou | **TTL de 90 dias**, aggregation `$group` para mostrar as mais repetidas |
| `resumo_mensal_pedidos` | Números por mês, sem dados pessoais | Aggregation com **`$merge`**: o histórico fica mesmo depois que os pedidos são apagados |
| `contadores` | Sequência do número de protocolo | `findAndModify` + `$inc` (atômico: dois pedidos nunca recebem o mesmo número) |
| `logs_auditoria` | Trilha de auditoria e ações dos usuários | **Índice TTL de 2 anos**, append-only de alto throughput, rastreabilidade LGPD (acesso a laudos) e **fallback automático para MySQL** quando sem MongoDB |

### Regras de acesso
- **Perguntas Frequentes** (Painel > Conteúdos): `admin_total` e `editor`.
- **Pedidos de Ajuda** (aba própria no painel): somente `admin_total`, pois contêm dados pessoais.
- **Auditoria / Logs** (Painel > Auditoria): somente `admin_total`, controle de conformidade e segurança.

### LGPD no chat e na Auditoria
- A pessoa precisa marcar o consentimento, e o texto aceito fica gravado no pedido.
- O pedido é **apagado automaticamente 1 ano após ser encerrado** (status *Concluído* ou *Sem retorno*), via índice TTL no campo `encerrado_em`. Pedidos em aberto não expiram, e se um pedido for reaberto a contagem é cancelada (`$unset`).
- O botão "Excluir dados agora" atende quando a pessoa pede a exclusão antes do prazo.
- As dúvidas sem resposta não guardam nenhum dado pessoal e expiram em 90 dias.
- **Auditoria de Dados Médicos (LGPD Art. 6º)**: Toda visualização de documento ou laudo médico sensível de PCD é gravada com IP, data/hora e identificação do administrador responsável.

### Arquivos
- Conexão: `config/Mongo.php`
- Models: `app/models/Faq.php`, `PedidoAjuda.php`, `PerguntaSemResposta.php`, `AuditLog.php`
- Controllers: `app/controllers/ChatController.php` (público), `FaqController.php`, `PedidoController.php`, `LogController.php` (painel)
- Chat: `app/views/partials/chat_widget.php`, `public/js/chat.js`, `public/css/chat.css`
- Auditoria: `app/views/admin/logs/index.php`
- Perguntas iniciais: `database/mongo/faq_seed.json`

> **Observação:** quando o servidor não suporta algum operador de aggregation (por exemplo, bancos apenas *compatíveis* com MongoDB), o sistema faz o mesmo cálculo em PHP automaticamente. Com o MongoDB oficial, os recursos nativos são usados. Caso o MongoDB não esteja instalado no ambiente (ex: computadores de laboratório acadêmico), o sistema ativa o modo de contingência no MySQL de forma 100% transparente.
