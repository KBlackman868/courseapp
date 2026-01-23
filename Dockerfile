# Ministry of Health Course Application
# Multi-stage Dockerfile for Laravel + React/Vite application

# =============================================================================
# Stage 1: Build frontend assets
# =============================================================================
FROM node:20-alpine AS frontend-builder

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm ci

# Copy frontend source files
COPY resources/ ./resources/
COPY vite.config.js ./
COPY tailwind.config.js ./
COPY postcss.config.js ./

# Build frontend assets
RUN npm run build

# =============================================================================
# Stage 2: Install PHP dependencies
# =============================================================================
FROM composer:2 AS composer-builder

WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies (no dev dependencies for production)
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

# Copy application code
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize --no-dev

# =============================================================================
# Stage 3: Production image
# =============================================================================
FROM php:8.2-fpm-alpine AS production

LABEL maintainer="Ministry of Health Trinidad and Tobago"
LABEL description="Course Registration Application"

# Install system dependencies
RUN apk add --no-cache \
    # Build dependencies
    $PHPIZE_DEPS \
    # Runtime dependencies
    nginx \
    supervisor \
    curl \
    zip \
    unzip \
    git \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    # LDAP dependencies
    openldap-dev \
    # MySQL client
    mysql-client \
    # SQLite
    sqlite \
    sqlite-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure ldap \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        pdo_sqlite \
        mysqli \
        gd \
        zip \
        intl \
        mbstring \
        exif \
        pcntl \
        bcmath \
        ldap \
        opcache

# Install Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Clean up
RUN apk del $PHPIZE_DEPS \
    && rm -rf /var/cache/apk/* /tmp/* /var/tmp/*

# Configure PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Configure Nginx
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Configure Supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set working directory
WORKDIR /var/www/html

# Copy application from composer stage
COPY --from=composer-builder /app/vendor ./vendor
COPY . .

# Copy built frontend assets
COPY --from=frontend-builder /app/public/build ./public/build

# Create necessary directories and set permissions
RUN mkdir -p \
    storage/app/public \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/certs \
    bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Create SQLite database file if using SQLite
RUN touch database/database.sqlite \
    && chown www-data:www-data database/database.sqlite \
    && chmod 664 database/database.sqlite

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Set entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Default command
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

# =============================================================================
# Stage 4: Development image (optional - for local development)
# =============================================================================
FROM production AS development

# Install development dependencies
RUN apk add --no-cache \
    nodejs \
    npm

# Install Xdebug for debugging
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Copy Xdebug configuration
COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Override command for development
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
