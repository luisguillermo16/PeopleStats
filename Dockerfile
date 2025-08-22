# ===========================
# Etapa 1: Construcción de assets con Vite + React
# ===========================
FROM node:23.4-alpine AS frontend-builder
WORKDIR /app

# Instalar dependencias de Node
COPY package*.json ./
RUN npm install

# Copiar el resto del proyecto (incluye código frontend)
COPY . .

# Compilar assets con Vite
RUN npm run build


# ===========================
# Etapa 2: PHP + Apache
# ===========================
FROM php:8.3-apache AS php-apache

# Dependencias del sistema
RUN apt-get update && apt-get install -y \
    openssl zip unzip git curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP requeridas
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
    ctype curl dom fileinfo filter hash mbstring openssl pcre \
    pdo session tokenizer xml mysqli pdo_mysql gd zip

# Habilitar mod_rewrite en Apache
RUN a2enmod rewrite

# Configuración del DocumentRoot para Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf \
    && sed -ri -e "s!/var/www/!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

# Copiar el código de Laravel
COPY --chown=www-data:www-data . .

# Copiar los assets compilados desde la etapa frontend
COPY --from=frontend-builder /app/public/build public/build

# Instalar Composer y dependencias de Laravel
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --prefer-dist --optimize-autoloader

# Permisos recomendados
RUN chown -R www-data:www-data bootstrap/cache storage \
    && chmod -R 777 bootstrap/cache storage

# Crear symlink de storage (si no existe)
RUN php artisan storage:link || true

# Volúmenes persistentes
VOLUME [ "/var/www/html/storage", "/var/www/html/bootstrap/cache" ]

# Exponer puerto HTTP
EXPOSE 80

# Copiar entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]