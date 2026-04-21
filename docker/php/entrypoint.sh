#!/bin/bash
set -e

# ── Instala dependências PHP se vendor ainda não existir (após volume mount) ─
if [ ! -d /var/www/humoria/vendor ]; then
    echo "[entrypoint] Rodando composer install..."
    composer install --no-dev --no-interaction --working-dir=/var/www/humoria
fi

# ── Exporta variáveis de ambiente para o cron (www-data) ─────────────────────
printenv | grep -E '^(DB_|EMAIL_|DJANGO_)' \
    | sed 's/\(.*\)=\(.*\)/export \1="\2"/' \
    > /etc/environment

# ── Cron daemon ───────────────────────────────────────────────────────────────
service cron start

# ── Apache em foreground ──────────────────────────────────────────────────────
exec apache2-foreground
