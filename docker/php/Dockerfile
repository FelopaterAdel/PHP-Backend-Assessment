FROM php:8.2-fpm-alpine

WORKDIR /var/www/

RUN apk add --no-cache \
    build-base \
    libc-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    postgresql-dev \
    git \
    curl \
    zip \
    unzip \
    nodejs \
    npm

RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install \
    gd \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    bcmath
RUN apk add --no-cache \
    $PHPIZE_DEPS \
    linux-headers \
    autoconf \
    make \
    gcc \
    g++

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-interaction --optimize-autoloader --no-scripts

COPY package.json package-lock.json ./
RUN npm install

COPY . .

RUN npm run build

RUN composer install --no-interaction --optimize-autoloader

RUN mkdir -p storage/logs storage/framework/cache \
    storage/framework/sessions storage/framework/views \
    bootstrap/cache && \
    chown -R www-data:www-data /var/www

EXPOSE 9000

CMD ["php-fpm"]