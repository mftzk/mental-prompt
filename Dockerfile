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

# Install PHP dependencies (without running scripts)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy package.json for Node dependencies
COPY package.json package-lock.json ./
RUN npm install

# Copy application files
COPY . .

# Build frontend assets (non-blocking)
RUN npm run build || echo "Build failed, skipping asset compilation"

# Install RoadRunner binary using Octane's built-in installer (will be done on first run)
# This approach is more reliable as Octane manages the correct version

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create startup script
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
echo "Waiting for MySQL..."\n\
while ! mysqladmin ping -h ${DB_HOST:-mysql} -P ${DB_PORT:-3306} -u ${DB_USERNAME:-root} -p${DB_PASSWORD} --silent 2>/dev/null; do\n\
    echo "Database not ready, waiting..."\n\
    sleep 2\n\
done\n\
echo "MySQL is ready!"\n\
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
# Run migrations\n\
echo "Running migrations..."\n\
php artisan migrate --force 2>/dev/null || echo "Migration failed"\n\
\n\
# Install RoadRunner if not exists\n\
if [ ! -f /usr/local/bin/rr ]; then\n\
    echo "Installing RoadRunner..."\n\
    php artisan octane:install --server=roadrunner --force 2>/dev/null || echo "RoadRunner install failed"\n\
    mv vendor/bin/rr /usr/local/bin/rr 2>/dev/null || echo "RoadRunner already in /usr/local/bin"\n\
fi\n\
\n\
# Publish Octane config if not exists\n\
if [ ! -f config/octane.php ]; then\n\
    echo "Publishing Octane configuration..."\n\
    php artisan vendor:publish --provider="Laravel\\\\Octane\\\\OctaneServiceProvider" --force 2>/dev/null || echo "Octane publish failed"\n\
fi\n\
\n\
# Clear and cache configurations\n\
echo "Optimizing application..."\n\
php artisan config:clear 2>/dev/null || echo "Config clear failed"\n\
php artisan config:cache 2>/dev/null || echo "Config cache failed"\n\
php artisan route:cache 2>/dev/null || echo "Route cache failed"\n\
php artisan view:cache 2>/dev/null || echo "View cache failed"\n\
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
