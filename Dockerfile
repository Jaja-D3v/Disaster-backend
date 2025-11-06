# Use official PHP 8.2 CLI image
FROM php:8.2-cli

# Set working directory inside the container
WORKDIR /app

# Copy all your backend files into the container
COPY . .

# Expose port for Render
EXPOSE 10000

# Install Composer if you need dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install || true  # ignore if no composer.json

# Start PHP built-in server, serve public folder
CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]
