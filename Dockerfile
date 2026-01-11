FROM php:8.2-apache
COPY api.php /var/www/html/
CMD ["apache2-foreground"]
