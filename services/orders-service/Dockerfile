# define which server image to use
FROM php:8.3-fpm

# install dependencies and extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    librdkafka-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mbstring exif pcntl bcmath xml zip pcntl exif pdo_mysql sockets

# install mongodb php driver 
RUN pecl install mongodb redis rdkafka \
    && echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini \
    && echo "extension=rdkafka.so" > /usr/local/etc/php/conf.d/rdkafka.ini


# install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# set working directory for serve micro service
WORKDIR /var/www

# copy existing . local files to the docker container
COPY . .

#  install laravel dependencies
RUN composer install && \
    # php artisan key:generate && \
    php artisan storage:link 

# set permissions for laravel storage and cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# expose port 8000 and start the server
CMD php artisan serve --host=0.0.0.0 --port=8000