FROM php:8.2-apache

# Ativar módulo rewrite
RUN a2enmod rewrite

# Copiar arquivos
COPY . /var/www/html/

# Configuração do Apache
RUN echo '<Directory /var/www/html>' > /etc/apache2/conf-available/docker-php.conf \
    && echo '  AllowOverride All' >> /etc/apache2/conf-available/docker-php.conf \
    && echo '  Require all granted' >> /etc/apache2/conf-available/docker-php.conf \
    && echo '  DirectoryIndex index.php index.html' >> /etc/apache2/conf-available/docker-php.conf \
    && echo '</Directory>' >> /etc/apache2/conf-available/docker-php.conf \
    && a2enconf docker-php

# Permissões
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
