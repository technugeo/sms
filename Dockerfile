FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git curl unzip libpng-dev libjpeg-dev libfreetype6-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql gd zip \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/custom.ini

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
COPY . /var/www/html

# Copy custom PHP-FPM pool config
COPY ./www.conf /usr/local/etc/php-fpm.d/www.conf
