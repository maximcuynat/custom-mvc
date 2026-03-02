FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite

COPY /app /var/www/app/
COPY /vendor /var/www/vendor/

RUN chown -R www-data:www-data /var/www/app /var/www/vendor

RUN sed -i 's|/var/www/html|/var/www/app|g' /etc/apache2/sites-available/000-default.conf \
 && sed -i 's|/var/www/html|/var/www/app|g' /etc/apache2/apache2.conf

RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

EXPOSE 80