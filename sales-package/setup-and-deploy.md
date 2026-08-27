# Setup e Deploy

## 1. Requisitos

- PHP 8.3+
- Composer
- Node.js + npm
- Banco de dados: MySQL / PostgreSQL / SQLite
- Git
- Servidor para produção (Linux + Nginx/Apache)
- SSL

## 2. Setup local

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm run build
php artisan serve
```

## 3. Variáveis importantes de ambiente

```env
APP_NAME="Finance Pro"
APP_ENV=production
APP_KEY=
APP_DEBUG=false

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=finance_pro
DB_USERNAME=root
DB_PASSWORD=

STRIPE_KEY=
pk_test_...
STRIPE_SECRET=
sk_test_...
STRIPE_WEBHOOK_SECRET=
CASHIER_CURRENCY=eur

OPENROUTER_API_KEY=
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@yourdomain.com
MAIL_FROM_NAME="Finance Pro"
```

## 4. Deploy em produção

### Recomendação

- VPS Ubuntu / Debian
- Nginx
- PHP-FPM
- MySQL ou PostgreSQL
- Certificado SSL
- Supervisor para queue worker

### Comandos básicos

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan storage:link
php artisan queue:work --daemon
```

## 5. Checklist de produção

- APP_DEBUG=false
- SSL ativo
- secrets configurados
- Stripe webhook configurado
- fila de jobs ativa
- logs e backups ativos
- armazenamento público configurado
- cron para jobs periódicos
- cache ativado

## 6. Observações de risco

O sistema usa Stripe, AI APIs e filas. Antes de vender, é importante testar:

- login e cadastro
- criação de workspace
- criação de categorias
- pagamento de planos
- webhooks do Stripe
- envio de emails
- AI fallback se a API falhar
