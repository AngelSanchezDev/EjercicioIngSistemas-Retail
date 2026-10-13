FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli

# El front controller y los endpoints de API viven en /public.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-enabled/*.conf

COPY . /var/www/html

EXPOSE 80
