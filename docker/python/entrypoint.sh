#!/bin/bash
set -e

# Exporta variáveis de ambiente para que o cron as enxergue
printenv | grep -E '^(DB_|EMAIL_|NLTK_|OLLAMA_)' \
    | sed 's/\(.*\)=\(.*\)/export \1="\2"/' \
    > /etc/environment

# Aplica o crontab
crontab /etc/cron.d/humorai-worker

# Inicia o cron daemon
service cron start

echo "Worker iniciado. Logs em /var/log/humorai_worker.log"

# Mantém o container vivo e exibe logs
tail -f /var/log/humorai_worker.log
