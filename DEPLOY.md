# 🛡️ Guia de Implantação em Produção & Hardening (DEPLOY.md)

Este documento descreve os procedimentos recomendados e requisitos de segurança para hospedar o portal do **CMDPCD Jahu** em um servidor de produção (Linux / VPS / Cloud).

---

## 1. Arquitetura de Diretórios e DocumentRoot (SEC-05)

### ⚠️ O Princípio Fundamental
**O servidor web NUNCA deve apontar seu `DocumentRoot` para a raiz do repositório (`/var/www/cmdpcd`).**  
O `DocumentRoot` DEVE apontar exclusivamente para a pasta **`public`** (`/var/www/cmdpcd/public`).

```
/var/www/cmdpcd/               <-- Raiz do projeto (FORA DO ALCANCE DO NAVEGADOR)
├── app/                       <-- Código-fonte MVC, controllers, models, views
├── config/                    <-- Credenciais de banco de dados (db.local.php)
├── database/                  <-- Scripts de migração e schemas SQL
├── storage/                   <-- Laudos médicos e documentos confidenciais de PCDs (LGPD)
│   ├── pcd_docs/              <-- NUNCA servido diretamente por HTTP
│   └── ratelimit/             <-- Contadores temporários de taxa
├── routes/                    <-- Definição de rotas
└── public/                    <-- DOCUMENTROOT DO SERVIDOR WEB
    ├── css/
    ├── js/
    ├── uploads/               <-- Fotos e anexos públicos do portal
    └── index.php              <-- Front controller único do sistema
```

Ao apontar o servidor para `/public`:
1. Arquivos sensíveis (`.env`, `config/db.local.php`, `storage/pcd_docs/*`) tornam-se fisicamente inacessíveis via URL direta, eliminando qualquer dependência exclusiva de `.htaccess`.
2. Documentos confidenciais só podem ser acessados através do controller seguro `/admin/pcds/documento`, que valida autenticação, perfil e registra log de auditoria.

---

## 2. Configurações de Servidor Web

### Opção A: Apache 2.4+ (VirtualHost Recomendado)

Crie o arquivo de configuração do site em `/etc/apache2/sites-available/cmdpcd.conf`:

```apache
<VirtualHost *:80>
    ServerName cmdpcd.jau.sp.gov.br
    ServerAdmin webmaster@jau.sp.gov.br

    # Redirecionamento obrigatório para HTTPS
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</VirtualHost>

<VirtualHost *:443>
    ServerName cmdpcd.jau.sp.gov.br
    ServerAdmin webmaster@jau.sp.gov.br

    # DocumentRoot apontando OBRIGATORIAMENTE para /public
    DocumentRoot /var/www/cmdpcd/public
    DirectoryIndex index.php

    # Certificados SSL/TLS (Let's Encrypt ou Institucional)
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/cmdpcd.jau.sp.gov.br/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/cmdpcd.jau.sp.gov.br/privkey.pem

    # Cabeçalhos de Segurança HTTP
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains" env=HTTPS

    <Directory /var/www/cmdpcd/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted

        # Reescrita de URLs para o Front Controller
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.*)$ index.php [QSA,L]
    </Directory>

    # Bloqueio de execução de scripts PHP na pasta de uploads públicos
    <Directory /var/www/cmdpcd/public/uploads>
        php_admin_flag engine off
        Options -ExecCGI -Indexes
        <FilesMatch "\.(php|phtml|php5|php7|php8)$">
            Require all denied
        </FilesMatch>
    </Directory>

    # Logs do Servidor
    ErrorLog ${APACHE_LOG_DIR}/cmdpcd_error.log
    CustomLog ${APACHE_LOG_DIR}/cmdpcd_access.log combined
</VirtualHost>
```

Ative os módulos necessários e o virtual host:
```bash
sudo a2enmod rewrite ssl headers
sudo a2ensite cmdpcd.conf
sudo systemctl reload apache2
```

---

### Opção B: Nginx + PHP-FPM

Crie o arquivo `/etc/nginx/sites-available/cmdpcd`:

```nginx
server {
    listen 80;
    server_name cmdpcd.jau.sp.gov.br;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name cmdpcd.jau.sp.gov.br;

    # DocumentRoot estritamente em /public
    root /var/www/cmdpcd/public;
    index index.php;

    # Certificados SSL/TLS
    ssl_certificate /etc/letsencrypt/live/cmdpcd.jau.sp.gov.br/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/cmdpcd.jau.sp.gov.br/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Cabeçalhos de Segurança
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Ocultar versão do servidor
    server_tokens off;

    # Limite de tamanho de upload
    client_max_body_size 12M;

    # Roteamento Front Controller
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Bloqueio estrito de execução PHP na pasta de uploads
    location ~* ^/uploads/.*\.php$ {
        deny all;
        return 404;
    }

    # Processamento PHP via FastCGI
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Bloquear arquivos ocultos (.git, .env, .htaccess)
    location ~ /\.(?!well-known).* {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Cache de assets estáticos
    location ~* \.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }

    access_log /var/log/nginx/cmdpcd_access.log;
    error_log /var/log/nginx/cmdpcd_error.log;
}
```

Ative o site no Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/cmdpcd /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 3. Permissões de Arquivos e Diretórios (Menor Privilégio)

Considere o usuário de deploy como `deployer` e o usuário do servidor web como `www-data`:

```bash
# Definir usuário e grupo proprietários
sudo chown -R deployer:www-data /var/www/cmdpcd

# Pastas em 755 e arquivos em 644 por padrão (somente leitura para www-data)
sudo find /var/www/cmdpcd -type d -exec chmod 755 {} \;
sudo find /var/www/cmdpcd -type f -exec chmod 644 {} \;

# Pastas que necessitam de ESCRITA pelo servidor web:
sudo chmod -R 775 /var/www/cmdpcd/storage
sudo chmod -R 775 /var/www/cmdpcd/public/uploads

# Proteger credenciais do banco para leitura restrita
sudo chmod 640 /var/www/cmdpcd/config/db.local.php
```

---

## 4. Configuração de Variáveis de Ambiente e Credenciais (SEC-07)

1. Crie o arquivo de credenciais a partir do modelo:
   ```bash
   cp /var/www/cmdpcd/config/db.local.php.example /var/www/cmdpcd/config/db.local.php
   ```

2. Edite `config/db.local.php` definindo senhas seguras:
   ```php
   return [
       'env'       => 'production',     // OBRIGATÓRIO: 'production' em servidores reais!
       'db_host'   => '127.0.0.1',
       'db_name'   => 'cmpcd_jau',
       'db_user'   => 'cmdpcd_user',     // NÃO UTILIZE 'root'
       'db_pass'   => 'SuaSenhaForteAqui@2026', // Proibido senhas vazias ou fracas
       'db_port'   => 3306,
       'mongo_uri' => 'mongodb://127.0.0.1:27017' // Ou connection string Atlas
   ];
   ```

> **Atenção:** Em ambiente de produção (`env => 'production'`), o sistema bloqueia conexões com senhas vazias ou usuário `root`, garantindo proteção por design.

---

## 5. Endurecimento do PHP (`php.ini`)

No arquivo de configuração do PHP (`/etc/php/8.2/fpm/php.ini` ou `/etc/php/8.2/apache2/php.ini`):

```ini
; Segurança Geral
expose_php = Off
display_errors = Off
display_startup_errors = Off
log_errors = On
error_log = /var/log/php/cmdpcd_errors.log

; Sessões Seguras
session.use_strict_mode = 1
session.cookie_httponly = 1
session.cookie_secure = 1
session.cookie_samesite = "Lax"
session.gc_maxlifetime = 1800

; Uploads e Tamanhos
file_uploads = On
upload_max_filesize = 10M
post_max_size = 12M
max_execution_time = 30
max_input_time = 60
memory_limit = 128M

; Desabilitar funções perigosas não utilizadas
disable_functions = exec,passthru,shell_exec,system,proc_open,popen,parse_ini_file,show_source
```

---

## 6. Checklist de Go-Live (Pré-Lançamento)

Antes de abrir o site ao público:

- [ ] **Trocar a senha padrão do admin**: Entrar com `admin@admin.com` e trocar a senha imediatamente para uma senha forte de 12+ caracteres.
- [ ] **Criar contas individuais**: Cadastrar usuários administrativos nominais para cada conselheiro/secretário.
- [ ] **Verificar `APP_ENV`**: Garantir que `config/db.local.php` tem `'env' => 'production'`.
- [ ] **Testar isolamento de documentos**: Tentar acessar diretamente no navegador `https://seudominio.com/storage/pcd_docs/` e certificar-se de receber erro 403 ou 404.
- [ ] **Testar Sudo Mode**: Acessar o painel administrativo como admin, tentar excluir um usuário de teste e certificar-se de que a senha do administrador é exigida.
- [ ] **Certificado SSL**: Validar classificação A+ no teste do *Qualys SSL Labs*.
- [ ] **Testar Trilha de Auditoria**: Entrar na aba *Auditoria* e validar que logins, visualizações de laudos e alterações de dados estão sendo devidamente gravados com IP e data/hora.
