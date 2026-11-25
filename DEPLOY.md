# Guia de Publicação em Produção (Domínio) — CMDPCD Jahu

Este guia descreve o passo a passo para colocar o portal **CMDPCD Jahu** no ar em hospedagem compartilhada (cPanel, Hostinger, Locaweb, KingHost etc.) ou VPS com Apache + PHP + MySQL.

---

## 1. Requisitos do servidor

- **PHP 8.1 ou superior** com extensões: `pdo_mysql`, `fileinfo`, `openssl`, `dom`, `mbstring`
- **MySQL 5.7+ / MariaDB 10.3+**
- **Apache** com `mod_rewrite` (e `mod_headers`, `mod_deflate`, `mod_expires` recomendados)
- **Certificado SSL (HTTPS)** ativo no domínio (ex.: Let's Encrypt / AutoSSL / Cloudflare)
- Não exige Composer, Node.js nem servidor MongoDB externo (o chatbot e os logs funcionam com armazenamento documental local em `storage/mongodb/` ou MongoDB externo opcional)

---

## 2. Subir os arquivos para o servidor

Envie o conteúdo da pasta `CMDPCD-main/` para o servidor (via FTP/SFTP, Gerenciador de Arquivos ou Git).

### Opção A — Recomendada (DocumentRoot apontando para `/public`)
- Envie o projeto para uma pasta acima da raiz pública (ex.: `/home/usuario/cmdpcd/`).
- No painel da hospedagem ou VirtualHost do Apache, aponte o **DocumentRoot** do domínio para `/home/usuario/cmdpcd/public`.
- Assim, apenas a pasta `public/` fica exposta na internet.

### Opção B — Hospedagem compartilhada padrão (`public_html/`)
- Envie o conteúdo de `CMDPCD-main/` para dentro de `public_html/`.
- O arquivo `.htaccess` na raiz redireciona automaticamente as requisições para `public/` e bloqueia o acesso web às pastas internas (`app/`, `config/`, `database/`, `routes/`, `storage/`) e a arquivos sensíveis (`.sql`, `.env`, `.md`, `.csv`, `.json` etc.).

---

## 3. Permissões de escrita

Garanta que o servidor web (PHP) tenha permissão de escrita nas pastas abaixo:

- `storage/pcd_docs/` (documentos privados de PCDs — LGPD)
- `storage/mongodb/` (FAQ, pedidos de ajuda e auditoria quando usado modo `json_local`)
- `storage/ratelimit/` (proteção contra força bruta e spam)
- `public/uploads/` (imagens de notícias, fotos de conselheiros e PDFs públicos)

Em Linux/VPS, normalmente `750` ou `755` para diretórios pertencentes ao usuário do PHP-FPM/Apache.

---

## 4. Banco de dados MySQL

1. No painel da hospedagem, crie um **banco de dados MySQL** e um **usuário exclusivo** com senha forte e permissões apenas nesse banco (nunca use `root`).
2. Abra o **phpMyAdmin**, selecione o banco recém-criado e importe o arquivo:
   ```
   database/producao_schema.sql
   ```
   > Este arquivo é seguro para produção: não usa `CREATE DATABASE`/`USE`, não apaga tabelas existentes e insere apenas `perfis_acesso` e `tipos_deficiencia` (nenhum usuário padrão ou dado fictício).

---

## 5. Configurar `config/db.local.php`

No servidor, copie `config/db.local.example.php` para `config/db.local.php` e preencha com os dados reais:

```php
<?php
return [
    'env'       => 'production',
    'seed_demo' => false,

    'host'   => 'localhost',           // ou host MySQL da hospedagem
    'port'   => 3306,
    'dbname' => 'u123456_cmpcd_jau',
    'user'   => 'u123456_cmpcd_user',
    'pass'   => 'SUA_SENHA_FORTE_AQUI',

    // Use 'json_local' se a hospedagem não tiver MongoDB; ou 'auto' + mongo_uri se usar MongoDB/Atlas
    'mongo_driver' => 'json_local',
    'mongo_uri'    => '',
    'mongo_db'     => 'cmpcd_jau',
];
```

> Em domínios de produção, o sistema bloqueia automaticamente senhas padrão do XAMPP e **não** cria a conta local de demonstração (`luhan@email.com`) nem os 50 cadastros fictícios de teste.

---

## 6. Criar o primeiro administrador real

### Via terminal SSH (recomendado)
Execute na raiz do projeto:
```bash
php database/criar_admin.php "Nome do Administrador" "admin@seudominio.org.br" "00000000000" "SenhaForte@2026" "(14) 99999-9999"
```

### Sem acesso SSH (via phpMyAdmin)
1. Gere o hash bcrypt da senha (em qualquer PHP 8+ local):
   ```bash
   php -r "echo password_hash('SuaSenhaForte@2026', PASSWORD_DEFAULT) . PHP_EOL;"
   ```
2. Execute no SQL do phpMyAdmin (substituindo os dados e o hash gerado):
   ```sql
   INSERT INTO usuarios (perfil_id, nome_completo, cpf, celular, email, senha, status)
   VALUES (1, 'Administrador CMDPCD', '00000000000', '(14) 3624-5077', 'admin@seudominio.org.br', '$2y$10$SEU_HASH_AQUI', 'ativo');
   ```

---

## 7. HTTPS / SSL

Os arquivos `.htaccess` (raiz e `public/.htaccess`) redirecionam automaticamente `http://` para `https://` em qualquer domínio real (ignorando `localhost` e `127.0.0.1` e compatíveis com proxy reverso/Cloudflare).

> Caso precise testar o domínio antes de emitir o certificado SSL, comente temporariamente as 4 linhas de `Força HTTPS em produção` nos arquivos `.htaccess` e reative assim que o SSL estiver ativo.

---

## 8. Checklist final antes de divulgar o domínio

- [ ] Atualizar a chave PIX (CNPJ real) em `app/views/apoio.php` (atualmente consta `00.000.000/0001-00`)
- [ ] Conferir se `https://seudominio/config/db.local.php` e `https://seudominio/storage/` retornam **403 Forbidden**
- [ ] Acessar `https://seudominio/admin`, fazer login com o administrador real e cadastrar os conselheiros, notícias e documentos oficiais
- [ ] Testar envio de contato (`/contato`), cadastro de PCD (`/cadastro-pcd`), download protegido de anexos no painel e o chatbot ("Posso ajudar?")
- [ ] Configurar backup periódico do banco MySQL, de `storage/` e de `public/uploads/` (conforme LGPD)
