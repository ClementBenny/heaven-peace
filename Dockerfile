FROM php:8.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libsqlite3-dev libzip-dev zip

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy project files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader || true

# Create SQLite DB
RUN mkdir -p database && touch database/database.sqlite

# Fix permissions
RUN chmod -R 775 storage bootstrap/cache || true

# Expose port
EXPOSE 10000

# Start app (with migrate)
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000