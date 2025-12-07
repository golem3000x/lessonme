FROM php:8.4-fpm

# Переменные окружения Sail (как в Laravel Sail)
ENV WWWGROUP=1000 \
    NODE_VERSION=20 \
    POSTGRES_VERSION=16 \
    MYSQL_CLIENT=default-mysql-client

# Устанавливаем системные пакеты + зависимости Sail
RUN apt-get update && apt-get install -y \
    gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin \
    libpng-dev python3 dnsutils librsvg2-bin fswatch ffmpeg nano \
    libpq-dev libzip-dev libonig-dev libxml2-dev libssl-dev \
    # зависимости PHP
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip bcmath \
    # Composer
    && curl -sLS https://getcomposer.org/installer | php -- \
         --install-dir=/usr/local/bin --filename=composer \
    # Node.js (как в Sail)
    && curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm pnpm bun \
    # Yarn
    && npm install -g yarn \
    # Postgres client (как в Sail)
    && apt-get install -y postgresql-client-$POSTGRES_VERSION \
    # MySQL client
    && apt-get install -y $MYSQL_CLIENT \
    # cleanup
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Копируем приложение
COPY . .

# Laravel зависимости
RUN composer install --no-dev --optimize-autoloader

# Supervisor конфигурация — путь НЕ менял!
COPY docker/deploy/dev/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord"]