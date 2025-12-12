FROM ubuntu:latest
LABEL authors="Franck"

# Update packages
RUN apt-get update -y
RUN apt-get upgrade -y php php-cli php-fpm php-mysql php-curl php-json curl wget git sudo

# Define orking directory
WORKDIR /app

# Copy project files
COPY . /app

# Open dev port
EXPOSE 8000

# Laaunch PHP
CMD ["php", "-S", "0.0.0.0:8000"]