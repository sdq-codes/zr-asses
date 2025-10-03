# Use PHP 8.3 with required extensions
FROM php:8.3-cli

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy app files (optional: usually mounted via volume in docker-compose)
COPY . .

# Install dependencies (for local dev keep dev dependencies too)
RUN composer install --no-interaction --prefer-dist
RUN php artisan migrate
RUN php artisan db:seed

# Expose port 8000
EXPOSE 8000

# Run Laravel artisan serve for hot reload
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
