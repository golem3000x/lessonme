FROM php:8.4-fpm

# Аргументы Sail (просто чтобы избежать ошибок)
ARG NODE_VERSION=20
ARG MYSQL_CLIENT="default-mysql-client"
ARG POSTGRES_VERSION=17

WORKDIR /var/www/html

# Устанавливаем системные пакеты
RUN apt-get update && apt-get install -y \
    curl zip unzip git supervisor \
    sqlite3 libpng-dev libpq-dev libzip-dev \
    libonig-dev libxml2-dev libssl-dev \
    python3 dnsutils ffmpeg nano \
    # Ключевые зависимости Sail
    librsvg2-bin fswatch libcap2-bin \
    # База (клиенты)
    postgresql-client-$POSTGRES_VERSION \
    $MYSQL_CLIENT \
    # Node.js (стабильная версия Debian – Node 18)
    nodejs npm \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip bcmath \
    && npm install -g pnpm bun yarn \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php \
    -- --install-dir=/usr/local/bin --filename=composer

# Копируем приложение
COPY . .

# Устанавливаем Laravel-зависимости
RUN composer install --no-dev --optimize-autoloader

# Supervisor конфигурация
COPY docker/deploy/dev/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Запуск supervisor
CMD ["/usr/bin/supervisord"]