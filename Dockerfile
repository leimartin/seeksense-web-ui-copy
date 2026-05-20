# Use an official PHP runtime as a parent image
FROM php:7.4-apache

# Install PHP extensions and other dependencies
RUN apt-get update && \
    apt-get install -y libpng-dev && \
    docker-php-ext-install mysqli gd

# Expose the port Apache listens on
EXPOSE 8181

# Set the working directory in the container
WORKDIR /var/www/html

# Copy your PHP application code into the container
COPY src/ .

# Start Apache when the container runs
CMD ["apache2-foreground"]