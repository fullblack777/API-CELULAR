FROM php:8.2-apache

# Copiar tudo
COPY . /var/www/html/

# Apenas dar permissão (sem comandos complexos)
RUN chmod -R 755 /var/www/html/

EXPOSE 80

CMD ["apache2-foreground"]
