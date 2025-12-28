FROM php:8.5-cli-alpine

# Install system packages
RUN apk add --no-cache git
RUN apk add --no-cache curl
RUN apk add --no-cache build-base
RUN apk add --no-cache libpq-dev
RUN apk add --no-cache libzip-dev
RUN apk add --no-cache icu-dev
RUN apk add --no-cache zip
RUN apk add --no-cache unzip
RUN apk add --no-cache postgresql-client
RUN rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install pdo_pgsql
RUN docker-php-ext-install zip
RUN docker-php-ext-install intl

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define working directory
WORKDIR /app

# Copy projet files
COPY . /app

# Configure git to avoid ownership errors
RUN git config --global --add safe.directory /app

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-scripts
RUN composer dump-autoload --optimize

# Create and copy entrypoint script
COPY docker-entrypoint.sh /app/docker-entrypoint.sh
RUN chmod +x /app/docker-entrypoint.sh

# Open dev port
EXPOSE 8000

# Launch server
CMD ["/app/docker-entrypoint.sh"]
