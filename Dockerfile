FROM php:8.2-fpm

# System deps
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    curl \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /app

# Copy source
COPY . .

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Build Vite
RUN npm install
RUN npm run build

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache public/build

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=8000
