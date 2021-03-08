FROM php:7.4-fpm

# For composer
RUN apt-get update && apt-get install -y \
     git \
     iputils-ping \
     net-tools \
     zlib1g-dev \
     unzip \
     libzip-dev \
     libfreetype6-dev \
     libpng-dev \
     libjpeg-dev

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php -r "if (hash_file('SHA384', 'composer-setup.php') === rtrim(file_get_contents('https://composer.github.io/installer.sig'))) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
  && php composer-setup.php \
  && php -r "unlink('composer-setup.php');" \
  && mv composer.phar /usr/local/bin/composer

# Set composer environment
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH $PATH:/composer/vendor/bin

# ZIP
RUN docker-php-ext-install zip

# sockets
RUN docker-php-ext-install sockets

# PHP's DB setting
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install mysqli

# Install opcache
RUN docker-php-ext-install opcache && docker-php-ext-enable opcache

# Install gd
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Install xdebug
RUN pecl channel-update pecl.php.net \
  && pecl install -o -f xdebug \
  && docker-php-ext-enable xdebug

# nginx
RUN apt install -y nginx vim
RUN useradd -m nginx

RUN ln -sf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime
RUN echo 'date.timezone = "Asia/Tokyo"' > /usr/local/etc/php/conf.d/timezone.ini

# move to Document Root
WORKDIR /var/www/src

COPY ./entrypoint.sh /etc/entrypoint.sh
ENTRYPOINT ["sh", "/etc/entrypoint.sh"]
