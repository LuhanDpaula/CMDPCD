# Execução local no XAMPP (sem internet)

## Requisitos

- XAMPP com Apache, MySQL/MariaDB e PHP 8.1 ou superior.
- Extensões PHP `pdo_mysql`, `fileinfo` e `openssl` habilitadas. `mbstring` é recomendada; o projeto inclui compatibilidade mínima caso ela esteja desativada.
- Não é necessário Composer, Node.js, CDN, acesso externo ou instalar a extensão `mongodb` do PHP.

## Instalação

1. Extraia a pasta `CMDPCD-main` em `C:\xampp\htdocs\`.
2. Abra o painel do XAMPP e inicie **Apache** e **MySQL**.
3. Acesse `http://localhost/CMDPCD-main/`.
4. Na primeira conexão, a aplicação cria o banco `cmpcd_jau` quando o usuário local do MySQL tem permissão, importa o esquema e os dados base e prepara a conta administrativa e os 50 registros fictícios de demonstração.

A configuração padrão considera XAMPP local com usuário `root` e senha vazia (a conexão também tenta `root` como senha). Para outra configuração, copie `config/db.local.example.php` para `config/db.local.php` e informe os dados de conexão. Esse arquivo não deve ser publicado.

## Acesso de administrador local

- **E-mail:** `luhan@email.com`
- **Senha inicial:** `123456`
- **Perfil:** `admin_total`

A senha é armazenada no banco como hash `password_hash()` e verificada por `password_verify()`. O login já aceita e-mail e senha nesses formatos; nenhuma alteração no formulário foi necessária. A senha solicitada é apenas para desenvolvimento local: altere-a depois do primeiro acesso e não reutilize em produção.

## Cadastros de demonstração

A inicialização inclui 50 registros fictícios identificados por `[TESTE NN]`. Eles usam e-mails reservados no domínio `.test`, CPFs de demonstração e observações fictícias. Não representam cidadãos nem devem ser usados para atendimento real.

O arquivo `database/dados_demo_pcds.csv` contém os mesmos 50 registros, em CSV UTF-8 com BOM e separador `;` (prático para abrir no Excel em configuração regional brasileira). A rotina é idempotente: não duplica os CPFs de teste nem apaga cadastros já existentes.

Para executar a carga manualmente, com MySQL ligado, use no terminal:

```bash
php database/seed_demo_pcds.php
```

## Banco não relacional do chatbot

A camada `Mongo` mantém a interface documental usada pelo chatbot (coleções, documentos, filtros, atualizações, agregações e contadores):

1. Se houver um MongoDB local acessível em `127.0.0.1:27017`, ele é utilizado primeiro.
2. Caso contrário, o projeto ativa automaticamente o **MongoJsonStore**, um banco documental local persistido em arquivos JSON dentro de `storage/mongodb/cmpcd_jau/`.

O modo local não precisa de internet nem de serviço adicional. As coleções são documentos JSON separados (`faq`, `perguntas_sem_resposta`, `pedidos_ajuda`, `contadores`, `logs_auditoria`, `config_sistema` e outras conforme o uso). O primeiro acesso inicializa as perguntas frequentes a partir de `database/mongo/faq_seed.json`.

Para configurar um MongoDB real, ajuste `mongo_uri` e `mongo_db` em `config/db.local.php`. O JSON local continua como fallback automático quando o servidor MongoDB não responder.

## Exportação de cadastro para PDF

No painel administrativo, abra **Cadastrados**. Ao lado do botão de editar/visualizar há um botão com ícone de arquivo para exportar o cadastro correspondente em PDF. A rota requer usuário autenticado com perfil `admin_total` ou `admin_parcial`.

O gerador `app/helpers/PdfDocument.php` é local e não utiliza bibliotecas baixadas. Permite compor documentos com `addTitle()`, `addSection()`, `addText()`, `addField()`, `addHtml()` e `addSeparator()`, ou criar um documento a partir de HTML simples com `PdfDocument::fromHtml()`. Renderiza PDF A4 com quebra automática de página e caracteres em português. O PDF é entregue diretamente para download, sem gravar cópias dos dados pessoais em diretórios públicos. A conversão HTML cobre texto, títulos, parágrafos e listas simples; não é um conversor Office de arquivos DOCX/XLSX arbitrários.

## Dependências externas removidas da interface principal

Os ícones e gráficos usam implementações locais em `public/js/lucide-local.js` e `public/js/chart-local.js`. A tipografia usa fontes de sistema. Imagens de demonstração e o bloco de localização usam recursos locais, então o portal, o painel e o chatbot não dependem de carregar bibliotecas, fontes, mapas ou imagens de CDNs para funcionar.

## Observações de segurança

- O acesso aos arquivos internos (`app`, `config`, `database` e `storage`) permanece bloqueado pelo Apache via `.htaccess`.
- O armazenamento JSON local fica em `storage`, que não deve ser publicado nem compartilhado.
- Os cadastros reais de PCD podem conter dados pessoais e dados sensíveis. Utilize apenas dados de teste neste pacote e proteja backups e arquivos locais conforme a LGPD.
- Antes de colocar em produção, configure credenciais próprias, remova a senha inicial e revise HTTPS, permissões, backups e políticas de acesso.
