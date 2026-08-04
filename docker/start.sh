#!/bin/bash
set -e

cd /var/www/html

# Render define PORT; default 10000
export PORT="${PORT:-10000}"

echo "==> A gerar configuração Nginx na porta $PORT..."
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# Verificar se a BD está configurada
if [ -z "${DB_HOST}" ] && [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    echo "==> Usando SQLite"
    touch /var/www/html/database/database.sqlite || true
else
    echo "==> Aguardar base de dados ($DB_HOST)..."
    for i in $(seq 1 30); do
        php -r "
            try {
                \$pdo = new PDO('${DB_CONNECTION:-mysql}:host=${DB_HOST};port=${DB_PORT:-3306};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');
                exit(0);
            } catch (Exception \$e) { exit(1); }
        " 2>/dev/null && break || true
        echo "    BD não disponível, tentativa $i/30..."
        sleep 2
    done
fi

echo "==> A executar migrações..."
php artisan migrate --force --no-interaction || echo "WARN: migrações falharam, a continuar..."

echo "==> A cachear configurações..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Desativar queue worker se QUEUE_CONNECTION=sync
if [ "${QUEUE_CONNECTION:-sync}" = "sync" ]; then
    echo "==> QUEUE_CONNECTION=sync, queue worker desativado"
    sed -i '/\[program:laravel-queue\]/,/^$/d' /etc/supervisord.conf
fi

echo "==> A iniciar Supervisor (Nginx + PHP-FPM) na porta $PORT..."
exec /usr/bin/supervisord -c /etc/supervisord.conf

