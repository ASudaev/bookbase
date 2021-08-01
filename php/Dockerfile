FROM php:7.4-fpm

#Install modules
RUN apt-get update && apt-get install -y \
        curl \
        wget \
        git \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
	    libpng-dev \
	    libonig-dev \
	    libzip-dev \
	    libmcrypt-dev \
        zlib1g-dev \
        libicu-dev \
        zip \
        && pecl install mcrypt-1.0.3 \
	    && docker-php-ext-enable mcrypt \
        && docker-php-ext-install -j$(nproc) opcache intl pdo_mysql zip \
	    && docker-php-ext-configure gd --with-freetype --with-jpeg \
        && docker-php-ext-install -j$(nproc) gd 

#Set working directory
WORKDIR /var/www/bookbase

#Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]