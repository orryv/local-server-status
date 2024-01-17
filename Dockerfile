FROM php:8.3-apache

# Set the working directory
WORKDIR /var/www/html

# Update packages and install any needed packages
RUN apt-get update && apt-get install -y \
    jq \
    && rm -rf /var/lib/apt/lists/*

# Copy the script to the container
COPY docker/entrypoint.sh /var/www/html/docker/entrypoint.sh

# Make the script executable
RUN chmod +x /var/www/html/docker/entrypoint.sh

# Copy existing application directory contents
# Set ownership to www-data for Apache
COPY --chown=www-data:www-data . /var/www/html
RUN chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Run the script when the container starts
ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]

