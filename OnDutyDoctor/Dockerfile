FROM php:8.2-cli

WORKDIR /var/www/html

# Instalar dependências e librdkafka
RUN apt-get update && apt-get install -y \
    unzip zip curl git libzip-dev libonig-dev libpng-dev libpq-dev libxml2-dev \
    librdkafka-dev

# Instalar extensões PHP
RUN docker-php-ext-install pdo pdo_mysql zip

# Instalar php-rdkafka
RUN pecl install rdkafka && docker-php-ext-enable rdkafka

# Copiar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar código e instalar dependências Laravel
COPY . .
RUN composer install

# Copiar script de entrada
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

CMD ["/entrypoint.sh"]
