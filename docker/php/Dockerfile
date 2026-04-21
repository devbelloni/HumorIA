FROM php:8.2-apache

# ── Dependências do sistema ──────────────────────────────────────────────────
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libzip-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    libxml2-dev \
    libonig-dev \
    libssh2-1-dev \
    sshpass \
    rsync \
    openssh-client \
    cron \
    python3 \
    python3-venv \
    python3-dev \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

# ── Extensões PHP ────────────────────────────────────────────────────────────
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    mbstring \
    zip

# ── Extensão PHP SSH2 ────────────────────────────────────────────────────────
RUN pecl install ssh2-1.4 \
    && docker-php-ext-enable ssh2

# ── Venv Python para relatory.py (sem deps pesadas de IA) ───────────────────
RUN python3 -m venv /relatory-venv \
    && /relatory-venv/bin/pip install --no-cache-dir mysql-connector-python

# ── Composer (instalado no sistema, usado no entrypoint) ─────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ── Configuração do Apache ───────────────────────────────────────────────────
COPY docker/php/apache.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# ── Log do relatory ───────────────────────────────────────────────────────────
RUN touch /var/log/humorai_relatory.log \
    && chown www-data:www-data /var/log/humorai_relatory.log

COPY docker/php/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

WORKDIR /var/www/humoria
EXPOSE 80
ENTRYPOINT ["/entrypoint.sh"]
