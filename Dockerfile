FROM php:8.1-cli
LABEL authors="Franck"

# Update packages
RUN apt-get update -y
RUN apt-get upgrade -y
RUN apt-get install -y curl wget git unzip libzip-dev libxml2-dev

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Define working directory
WORKDIR /app

# Copy project files
COPY . /app

# Install PHP dependencies with Composer
RUN composer install --no-dev --optimize-autoloader
RUN composer require tcgdex/sdk

# Open dev port
EXPOSE 8000

# Laaunch PHP
CMD ["php", "-S", "0.0.0.0:8000"]