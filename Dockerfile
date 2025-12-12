FROM ubuntu:latest
LABEL authors="Franck"

# Update packages
RUN apt-get update -y
RUN apt-get upgrade -y
RUN apt-get install -y php php-cli php-fpm php-mysql php-curl php-json php-xml curl wget

# Define orking directory
WORKDIR /app

# Copy project files
COPY . /app

# Open dev port
EXPOSE 8000

# Laaunch PHP
CMD ["php", "-S", "0.0.0.0:8000"]