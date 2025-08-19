FROM php:8.3-fpm-alpine
# Install dependencies
RUN apk add --no-cache \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    mysql-client \
    mariadb-connector-c-dev \
    supervisor \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip gd redis

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy app code
WORKDIR /var/www/html
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html
RUN composer install --no-dev --optimize-autoloader

# Supervisor config
COPY docker/conf/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
