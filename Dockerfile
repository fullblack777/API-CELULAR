FROM php:8.2-apache

# Atualizar e instalar dependências
RUN apt-get update && apt-get install -y \
    curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Habilitar módulos do Apache
RUN a2enmod rewrite

# Configurar Apache
RUN echo '<Directory /var/www/html>' > /etc/apache2/conf-available/docker-php.conf \
    && echo '    Options Indexes FollowSymLinks' >> /etc/apache2/conf-available/docker-php.conf \
    && echo '    AllowOverride All' >> /etc/apache2/conf-available/docker-php.conf \
    && echo '    Require all granted' >> /etc/apache2/conf-available/docker-php.conf \
    && echo '    DirectoryIndex index.php index.html' >> /etc/apache2/conf-available/docker-php.conf \
    && echo '</Directory>' >> /etc/apache2/conf-available/docker-php.conf

RUN a2enconf docker-php

# Copiar arquivos
COPY . /var/www/html/

# Configurar permissões CORRETAMENTE
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod 644 /var/www/html/*.php \
    && chmod 644 /var/www/html/*.html

EXPOSE 80

CMD ["apache2-foreground"]
