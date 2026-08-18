# syntax=docker/dockerfile:1
FROM php:8.3-cli

# --- System dependencies ---
RUN apt-get update && apt-get install -y \
        git curl zip unzip libzip-dev libssl-dev pkg-config \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/*

# --- MongoDB PHP extension (not bundled by default — required by mongodb/laravel-mongodb) ---
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# --- Composer ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --- Node.js (for building Tailwind/Vite assets) ---
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY . .

# --- Install dependencies & build frontend assets ---
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm install && npm run build

# --- Make sure the public storage symlink exists in the built image ---
RUN php artisan storage:link || true

# --- Writable dirs (Railway's filesystem is ephemeral, but these must exist per-boot) ---
RUN mkdir -p storage/app/temp storage/app/private/livewire-tmp \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

# Cache config using the REAL runtime env vars Railway injects, then serve.
CMD php artisan config:cache && php artisan view:cache && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
