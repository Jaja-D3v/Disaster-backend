# Use official PHP 8.2 Apache image
FROM php:8.2-apache

# Enable PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Set working directory inside container
WORKDIR /var/www/html/

# Copy your backend files into Apache's web root
COPY . /var/www/html/

# Expose default Apache port
EXPOSE 80

# (Optional) Install Composer if you have dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install || true

# Apache will start automatically, so no CMD is required
