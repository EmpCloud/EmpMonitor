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

RUN mkdir -p /var/www/html/storage/framework/sessions && chmod -R 777 /var/www/html/storage/framework/sessions

# Add and extract entrypoint script
COPY docker-entrypoint.zip /tmp/docker-entrypoint.zip
RUN unzip /tmp/docker-entrypoint.zip -d /usr/local/bin/ && \
    chmod +x /usr/local/bin/docker-entrypoint.sh

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Expose port 8000
EXPOSE 8000

# Use the entrypoint script to start the server
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
