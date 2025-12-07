FROM php:8.4-fpm

# Устанавливаем системные пакеты
RUN apt-get update && apt-get install -y \
    curl zip unzip git supervisor \
    && docker-php-ext-install pdo pdo_mysql

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php \
    -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# Копируем приложение
COPY . .

# Ставим зависимости Laravel
RUN composer install --no-dev --optimize-autoloader

# Supervisor конфигурация
COPY docker/deploy/dev/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# PHP уже исполняемый — не трогаем!
# RUN chmod +x /usr/bin/php   # ← НЕ НУЖНО, ОПАСНО

CMD ["/usr/bin/supervisord"]