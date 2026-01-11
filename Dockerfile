FROM php:8.2-apache

# Ativar módulos necessários
RUN a2enmod rewrite headers

# Instalar dependências básicas
RUN apt-get update && apt-get install -y \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Configuração do Apache
COPY . /var/www/html/

# Permissões
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
