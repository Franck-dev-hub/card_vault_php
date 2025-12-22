FROM php:8.5-cli

# Install system packages
RUN apt-get update -y
RUN apt-get upgrade -y
RUN apt-get install -y git curl build-essential libpq-dev libzip-dev libicu-dev zip unzip
RUN rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql zip intl

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define working directory
WORKDIR /app

# Copy projet files
COPY . /app

# Configure git to avoid ownership errors
RUN git config --global --add safe.directory /app

# Install PHP dependencies with compose
RUN composer install --no-dev --optimize-autoloader --no-scripts
RUN composer dump-autoload --optimize

# Open dev port
EXPOSE 8000

# Launch server
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
