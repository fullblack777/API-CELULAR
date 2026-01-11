FROM php:8.2-apache

WORKDIR /var/www/html

# Atualizar e instalar dependências
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# Criar arquivo de configuração do Apache
RUN echo '<Directory /var/www/html>' > /etc/apache2/conf-available/docker-php.conf \
    && echo '  AllowOverride All' >> /etc/apache2/conf-available/docker-php.conf \
    && echo '  Require all granted' >> /etc/apache2/conf-available/docker-php.conf \
    && echo '  DirectoryIndex index.php index.html' >> /etc/apache2/conf-available/docker-php.conf \
    && echo '</Directory>' >> /etc/apache2/conf-available/docker-php.conf \
    && a2enconf docker-php

# Copiar arquivos da aplicação
COPY . .

# Configurar permissões (SEM pasta storage)
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && find /var/www/html -type d -exec chmod 755 {} \;

# Expor porta 80
EXPOSE 80

# Comando para iniciar Apache
CMD ["apache2-foreground"]
