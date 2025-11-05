# Laravel Octane with RoadRunner
FROM php:8.2-cli

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip sockets \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files first for better layer caching
COPY composer.json composer.lock ./

# Install PHP dependencies (without scripts first)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy package.json for Node dependencies
COPY package.json package-lock.json ./
RUN npm install

# Copy application files
COPY . .

# Run composer scripts now that application files are available
RUN composer run-script post-autoload-dump

# Build frontend assets (non-blocking)
RUN npm run build || echo "Build failed, skipping asset compilation"

# Install RoadRunner via Octane
RUN php artisan octane:install --server=roadrunner --force

# Publish Octane config during build time
RUN php artisan vendor:publish --provider="Laravel\\Octane\\OctaneServiceProvider" --force || \
    echo "Octane config will be published at runtime"

# Create necessary directories
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create startup script
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
if [ "${SKIP_DB_CHECK:-false}" = "true" ]; then\n\
    echo "Skipping database health check..."\n\
else\n\
    echo "Waiting for MySQL..."\n\
    echo "DB_HOST: ${DB_HOST:-mysql}"\n\
    echo "DB_PORT: ${DB_PORT:-3306}"\n\
    echo "DB_USERNAME: ${DB_USERNAME:-root}"\n\
    echo "DB_PASSWORD: ***SET***"\n\
    \n\
    while ! mysqladmin ping -h ${DB_HOST:-mysql} -P ${DB_PORT:-3306} -u ${DB_USERNAME:-root} -p${DB_PASSWORD} --silent 2>/dev/null; do\n\
        echo "Database not ready, waiting..."\n\
        sleep 2\n\
    done\n\
    echo "MySQL is ready!"\n\
fi\n\
\n\
# Ensure storage directories exist\n\
mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache\n\
chown -R www-data:www-data storage bootstrap/cache\n\
\n\
# Copy .env if not exists\n\
if [ ! -f .env ]; then\n\
    cp env.example .env 2>/dev/null || echo "No env.example found"\n\
fi\n\
\n\
# Generate APP_KEY if needed\n\
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "null" ]; then\n\
    echo "Generating application key..."\n\
    php artisan key:generate --force 2>/dev/null || echo "Failed to generate key"\n\
fi\n\
\n\
# Run migrations only if AUTO_MIGRATE is enabled\n\
if [ "${AUTO_MIGRATE:-false}" = "true" ]; then\n\
    echo "Running migrations..."\n\
    php artisan migrate --force 2>/dev/null || echo "Migration failed"\n\
else\n\
    echo "Skipping migrations (set AUTO_MIGRATE=true to enable)"\n\
fi\n\
\n\
# Start Octane with RoadRunner\n\
echo "Starting Laravel Octane with RoadRunner..."\n\
exec php artisan octane:start --server=roadrunner --host=0.0.0.0 --port=8000 --workers=4 --max-requests=500\n\
' > /usr/local/bin/start.sh

RUN chmod +x /usr/local/bin/start.sh

# Expose Octane port
EXPOSE 8000

# Health check
HEALTHCHECK --interval=10s --timeout=3s --start-period=30s --retries=3 \
    CMD curl -f http://localhost:8000/up || exit 1

# Switch to www-data user for security
USER www-data

# Start Octane
CMD ["/usr/local/bin/start.sh"]
