FROM php:8.1-cli

# Cài system dependencies + PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql

# Cài Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Thư mục làm việc
WORKDIR /app

# Copy source code
COPY . .

# Cài Laravel packages
RUN composer install --no-dev --optimize-autoloader

# Port app
EXPOSE 10000

# Chạy Laravel
CMD php artisan serve --host=0.0.0.0 --port=10000
