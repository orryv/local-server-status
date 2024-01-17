FROM php:8.3-apache

# Set the working directory
WORKDIR /var/www/html

# Copy existing application directory contents and set ownership
COPY --chown=www-data:www-data . /var/www/html

# port
EXPOSE 9999