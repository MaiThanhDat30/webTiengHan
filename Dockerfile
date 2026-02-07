# ===== STAGE 1: BUILD VITE =====
FROM node:18 AS node-builder
WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build


# ===== STAGE 2: LARAVEL =====
FROM php:8.1-cli

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# COPY VITE BUILD
COPY --from=node-builder /app/public/build /app/public/build

# FIX PERMISSION (RẤT QUAN TRỌNG)
RUN chmod -R 755 public/build
RUN chmod -R 777 storage bootstrap/cache

RUN composer install --no-dev --optimize-autoloader
RUN php artisan optimize:clear || true

EXPOSE 10000

# ❗ KHÔNG DÙNG artisan serve
CMD php -S 0.0.0.0:10000 -t public
