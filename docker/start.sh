#!/bin/bash

cd /var/www/html

# Render define PORT; default 10000
export PORT="${PORT:-10000}"

echo "==> A gerar configuração Nginx na porta $PORT..."
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# Verificar se a BD está configurada
if [ -z "${DB_HOST}" ]; then
    echo "==> Sem DB_HOST, usando SQLite..."
    touch /var/www/html/database/database.sqlite 2>/dev/null || true
else
    echo "==> Aguardar base de dados ($DB_HOST)..."
    for i in $(seq 1 30); do
        php -r "
            try {
                new PDO(getenv('DB_CONNECTION').':host='.getenv('DB_HOST').';port='.(getenv('DB_PORT') ?: 3306).';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
                exit(0);
            } catch (Exception \$e) { exit(1); }
        " 2>/dev/null && { echo "    BD disponível!"; break; } || true
        echo "    BD não disponível, tentativa $i/30..."
        sleep 2
    done
fi

echo "==> A executar migrações..."
php artisan migrate --force --no-interaction 2>&1 || echo "WARN: migrações falharam, a continuar..."

echo "==> A cachear configurações..."
php artisan config:cache 2>&1 || echo "WARN: config:cache falhou"
php artisan route:cache 2>&1 || echo "WARN: route:cache falhou (verificar conflitos de rotas)"
php artisan view:cache 2>&1 || echo "WARN: view:cache falhou"

# Desativar queue worker se QUEUE_CONNECTION=sync
if [ "${QUEUE_CONNECTION:-sync}" = "sync" ]; then
    echo "==> QUEUE_CONNECTION=sync, queue worker desativado"
    sed -i '/\[program:laravel-queue\]/,/^\[/{ /^\[program:laravel-queue\]/!{ /^\[/!d } }' /etc/supervisord.conf 2>/dev/null || true
fi

echo "==> A iniciar Supervisor (Nginx + PHP-FPM) na porta $PORT..."
exec /usr/bin/supervisord -c /etc/supervisord.conf


