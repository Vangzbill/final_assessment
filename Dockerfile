# # Gunakan base image resmi PHP
# FROM php:8.3-fpm

# # Install system dependencies
# RUN apt-get update && apt-get install -y \
#     build-essential \
#     libpng-dev \
#     libjpeg-dev \
#     libonig-dev \
#     libxml2-dev \
#     zip \
#     unzip \
#     git \
#     curl \
#     libzip-dev \
#     libfreetype6-dev \
#     libjpeg62-turbo-dev \
#     libwebp-dev \
#     libxpm-dev \
#     libvpx-dev \
#     && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
#     && docker-php-ext-install pdo_mysql zip gd

# # Install Composer
# COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# # Set working directory
# WORKDIR /var/www

# # Copy project files
# COPY . .

# # Install Laravel dependencies
# RUN composer install --no-dev --optimize-autoloader

# # Copy default nginx config
# COPY docker/nginx.conf /etc/nginx/nginx.conf
# COPY docker/default.conf /etc/nginx/conf.d/default.conf

# # Copy supervisord config
# COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# # Set permissions
# RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# # Expose port
# EXPOSE 80

# # Jalankan supervisor untuk Nginx + PHP-FPM
# CMD ["/usr/bin/supervisord"]
FROM php:8.3-cli

# Install deps
RUN apt-get update && apt-get install -y \
    git curl unzip libzip-dev libpng-dev libjpeg-dev libonig-dev libxml2-dev \
    libfreetype6-dev libjpeg62-turbo-dev libwebp-dev libxpm-dev libvpx-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo_mysql zip gd sockets pcntl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader \
    && php artisan cache:clear || true \
    && php artisan config:clear || true \
    && php artisan view:cache \
    && php artisan route:clear


# Install Octane with Swoole
RUN composer require laravel/octane \
    && php artisan octane:install --server=swoole

EXPOSE 8000

CMD php artisan octane:start --server=swoole --host=0.0.0.0 --port=${PORT}
