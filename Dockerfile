# Stage 1: Builder
# This stage builds all necessary assets and dependencies.
FROM php:8.2-cli as builder

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

# Copy dependency definition files
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Install Node dependencies
RUN npm install

# Copy the rest of the application source code
COPY . .

# Build frontend assets
RUN npm run build

# Install RoadRunner via Octane
RUN php artisan octane:install --server=roadrunner --force

# ---

# Stage 2: Production Image
# This stage creates the final, lean image with Nginx and Supervisor.
FROM php:8.2-cli

WORKDIR /var/www/html

# Install Nginx and Supervisor
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install required PHP extensions
RUN docker-php-ext-install pdo_mysql sockets

# Copy application files from the builder stage
COPY --from=builder /var/www/html .

# Copy Nginx and Supervisor configurations
# We will create these files in the next steps
RUN rm -f /etc/nginx/sites-enabled/default
COPY docker/nginx/prod.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/supervisor/conf.d/app.conf /etc/supervisor/conf.d/app.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && mkdir -p /var/run/supervisor /var/log/supervisor \
    && chown -R www-data:www-data /var/run/supervisor /var/log/supervisor

# Expose port 80 for Nginx
EXPOSE 80

# Start Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
