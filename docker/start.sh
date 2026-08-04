#!/bin/bash
set -e

cd /var/www/html

echo "==> Aguardar base de dados..."
# Tentar ligar até 30 segundos
for i in $(seq 1 30); do
    php artisan db:monitor --databases=default 2>/dev/null && break || true
    echo "    BD não disponível, tentativa $i/30..."
    sleep 1
done

echo "==> A executar migrações..."
php artisan migrate --force --no-interaction || echo "WARN: migrações falharam, a continuar..."

echo "==> A limpar e a cachear configurações..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Só iniciar o queue worker se QUEUE_CONNECTION não for 'sync'
if [ "${QUEUE_CONNECTION:-sync}" = "sync" ]; then
    echo "==> QUEUE_CONNECTION=sync, queue worker desativado"
    # Remover o programa do supervisord se não for necessário
    sed -i '/\[program:laravel-queue\]/,/^$/d' /etc/supervisord.conf
fi

echo "==> A iniciar Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
