#!/bin/bash

cd /var/www/html

export PORT="${PORT:-10000}"

echo "==> A gerar configuração Nginx na porta $PORT..."
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# Desativar queue worker se QUEUE_CONNECTION=sync
if [ "${QUEUE_CONNECTION:-sync}" = "sync" ]; then
    echo "==> QUEUE_CONNECTION=sync, queue worker desativado"
    sed -i '/^\[program:laravel-queue\]/,/^$/d' /etc/supervisord.conf 2>/dev/null || true
fi

# Executar migrações em segundo plano para não bloquear o arranque do servidor
(
    echo "==> [bg] Aguardar base de dados ($DB_HOST)..."
    for i in $(seq 1 30); do
        php -r "
            try {
                new PDO(getenv('DB_CONNECTION').':host='.getenv('DB_HOST').';port='.(getenv('DB_PORT') ?: 5432).';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
                exit(0);
            } catch (Exception \$e) { exit(1); }
        " 2>/dev/null && { echo "==> [bg] BD disponível!"; break; } || true
        echo "    [bg] BD não disponível, tentativa $i/30..."
        sleep 2
    done

    echo "==> [bg] A executar migrações..."
    php artisan migrate --force --no-interaction 2>&1 || echo "WARN: migrações falharam"

    echo "==> [bg] A cachear configurações..."
    php artisan config:cache 2>&1 || true
    php artisan route:cache 2>&1 || echo "WARN: route:cache falhou"
    php artisan view:cache 2>&1 || true
    echo "==> [bg] Bootstrap completo."
) &

echo "==> A iniciar Supervisor (Nginx + PHP-FPM) na porta $PORT..."
exec /usr/bin/supervisord -c /etc/supervisord.conf



