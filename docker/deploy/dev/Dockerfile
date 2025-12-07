FROM php:8.4-fpm

ARG NODE_VERSION=20
ARG MYSQL_CLIENT="default-mysql-client"
ARG POSTGRES_VERSION=17

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    curl zip unzip git supervisor \
    sqlite3 libpng-dev libpq-dev libzip-dev \
    libonig-dev libxml2-dev libssl-dev \
    python3 dnsutils ffmpeg nano \
    librsvg2-bin fswatch libcap2-bin \
    postgresql-client-$POSTGRES_VERSION \
    $MYSQL_CLIENT \
    libicu-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip bcmath intl pcntl sockets \
    && curl -sL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm pnpm bun yarn \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
RUN curl -sS https://getcomposer.org/installer | php \
    -- --install-dir=/usr/local/bin --filename=composer

COPY . .

RUN composer install --no-dev --optimize-autoloader

# Supervisor конфигурация
COPY docker/deploy/dev/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Запуск: миграции + supervisor
CMD ["sh", "-c", "php artisan migrate --force && exec /usr/bin/supervisord"]