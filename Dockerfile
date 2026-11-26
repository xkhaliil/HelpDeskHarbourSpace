# Docker image used to run Harbour.Space HelpDesk on Vercel (container runtime).
# Based on Vercel's guide "Deploy Laravel on Vercel with Docker" (FrankenPHP).

# 1. PHP 8.5 with the extensions that Laravel and the packages need
FROM dunglas/frankenphp:1-php8.5-bookworm AS base

RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && install-php-extensions intl zip exif pdo_pgsql pdo_mysql

WORKDIR /app

# 2. Composer packages, without the development packages
FROM base AS dependencies

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --no-scripts --optimize-autoloader --prefer-dist

COPY . .
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && composer dump-autoload --no-dev --classmap-authoritative

# 3. The CSS and JavaScript (Tailwind CSS and Alpine.js), built with Vite
FROM node:22-bookworm-slim AS assets

WORKDIR /app
COPY --from=dependencies /app /app
RUN npm ci && npm run build

# 4. The image that runs on Vercel
FROM base AS runtime

COPY --from=dependencies --chown=www-data:www-data /app /app
COPY --from=assets --chown=www-data:www-data /app/public/build /app/public/build
COPY --chown=www-data:www-data Caddyfile /etc/frankenphp/Caddyfile

RUN setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp \
    && chown -R www-data:www-data /config/caddy /data/caddy

# Defaults for production. APP_KEY, APP_URL and the database URL are set in the Vercel dashboard.
# Sessions and cache are stored in the database, so every Vercel instance shares them.
ENV PORT=80 \
    APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    DB_CONNECTION=pgsql \
    SESSION_DRIVER=database \
    SESSION_SECURE_COOKIE=true \
    CACHE_STORE=database \
    QUEUE_CONNECTION=sync

USER www-data

CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]
