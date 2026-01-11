FROM php:8.2-apache

# Copiar arquivos
COPY . /var/www/html/

# Dar permissão ao Apache
RUN chown -R www-data:www-data /var/www/html

# Porta
EXPOSE 80

# Comando para iniciar
CMD ["apache2-foreground"]
