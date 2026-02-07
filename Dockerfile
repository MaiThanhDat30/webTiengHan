# ======================
# STAGE 1: BUILD VITE
# ======================
FROM node:18 AS node-builder
WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build

# Debug (rất quan trọng – KHÔNG xóa)
RUN ls -la public/build


# ======================
# STAGE 2: PHP + LARAVEL
# ======================
FROM php:8.1-cli

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# ✅ COPY ĐÚNG FILE BUILD
COPY --from=node-builder /app/public/build /app/public/build

RUN composer install --no-dev --optimize-autoloader
RUN chmod -R 777 storage bootstrap/cache

RUN php artisan config:clear
RUN php artisan route:clear
RUN php artisan view:clear

ENV PORT=10000
EXPOSE 10000

CMD php -S 0.0.0.0:$PORT -t public
