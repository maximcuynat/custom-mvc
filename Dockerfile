# On prend l'image PHP avec Apache
FROM php:8.2-apache

# Activer mod_rewrite pour Apache
RUN a2enmod rewrite

# Copier les fichiers PHP dans le dossier web d'Apache
COPY /app /var/www/html/

# Donner les droits corrects
RUN chown -R www-data:www-data /var/www/html

# Permettre à Apache de lire les .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Optionnel : définir ServerName pour supprimer l'avertissement
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Expose le port 80
EXPOSE 80
