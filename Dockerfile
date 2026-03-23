FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl

# Enable Apache Rewrite Module
RUN a2enmod rewrite

# Point Apache to the Laravel public directory
RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html
