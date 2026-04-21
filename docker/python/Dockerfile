FROM python:3.11-slim

# ── Dependências do sistema ──────────────────────────────────────────────────
RUN apt-get update && apt-get install -y \
    ffmpeg \
    default-libmysqlclient-dev \
    pkg-config \
    gcc \
    cron \
    curl \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# ── Dependências Python ──────────────────────────────────────────────────────
COPY Script/requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# ── Pré-download dos dados NLTK durante o build ──────────────────────────────
RUN python -c "\
import nltk; \
nltk.download('stopwords', quiet=True); \
nltk.download('vader_lexicon', quiet=True); \
nltk.download('punkt', quiet=True); \
nltk.download('punkt_tab', quiet=True)"

# ── Crontab: executa main.py a cada hora ─────────────────────────────────────
COPY docker/python/crontab /etc/cron.d/humorai-worker
RUN chmod 0644 /etc/cron.d/humorai-worker

# ── Log do worker ─────────────────────────────────────────────────────────────
RUN touch /var/log/humorai_worker.log

COPY docker/python/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
