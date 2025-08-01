FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy app files
COPY . .

# Add entrypoint script
COPY docker-entrypoint.zip /usr/local/bin/docker-entrypoint.zip
RUN unzip docker-entrypoint.zip
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Expose port 8000
EXPOSE 8000

# Use the entrypoint script to start the server
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"] 