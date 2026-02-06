# ===== Stage 1: Vite build =====
FROM node:18 AS node-builder
WORKDIR /app

COPY package*.json ./
RUN npm install

COPY resources ./resources
COPY vite.config.js .
COPY tailwind.config.js .
COPY postcss.config.js .

RUN npm run build

# ===== Stage 2: Laravel =====
FROM php:8.1-cli

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# COPY build Vite
COPY --from=node-builder /app/public/build /app/public/build

RUN composer install --no-dev --optimize-autoloader
RUN php artisan optimize:clear

EXPOSE 10000
CMD php artisan serve --host=0.0.0.0 --port=10000
