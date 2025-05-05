FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Swagger requirements
RUN docker-php-ext-install mbstring xml dom \
    && pecl install pcov \
    && docker-php-ext-enable pcov

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Install dependencies
RUN composer install


# Expose port 8000 for Laravel's built-in server
EXPOSE 8000

# Creates the swagger docs
CMD php artisan l5-swagger:generate

# Start Laravel's server
CMD php artisan serve --host=0.0.0.0 --port=8000
