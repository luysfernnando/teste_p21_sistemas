FROM php:8.2-fpm

# Instala dependências
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libxml2-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mysqli \
    zip \
    gd \
    mbstring \
    xml

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configura o PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia os arquivos do projeto
COPY . .

# Instala as dependências do Composer
RUN composer install --no-interaction --optimize-autoloader

# Limpar cache do apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/* 