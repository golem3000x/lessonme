FROM php:8.4-fpm

ENV WWWGROUP=1000 \
    NODE_VERSION=20 \
    POSTGRES_VERSION=16

# Основные системные зависимости (совместимые с Debian)
RUN apt-get update && apt-get install -y \
    curl zip unzip git supervisor sqlite3 libcap2-bin \
    libpng-dev python3 dnsutils ffmpeg nano \
    libpq-dev libzip-dev libxml2-dev libssl-dev \
    ca-certificates gnupg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip bcmath

# Установка Composer
RUN curl -sLS https://getcomposer.org/installer | php \
    -- --install-dir=/usr/local/bin --filename=composer

# Node.js (официальный способ для Debian)
RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource.asc | gpg --dearmor -o /usr/share/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/usr/share/keyrings/nodesource.gpg] https://deb.nodesource.com/node_${NODE_VERSION}.x nodistro main" \
       > /etc/apt/sources.list.d/nodesource.list \
    && apt-get update \
    && apt-get install -y nodejs \
    && npm install -g npm pnpm bun yarn

# Postgres client
RUN apt-get install -y postgresql-client

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

# Supervisor — путь оставил как у тебя
COPY docker/deploy/dev/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord"]