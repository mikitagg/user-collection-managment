FROM php:8.2-fpm as php-app

RUN apt-get update && apt-get install -y \
        nginx \
        git \
        unzip \
        libpng-dev \
        libonig-dev \
        libpq-dev \
        libxml2-dev \
        zip \
        curl \
        && apt-get clean && rm -rf /var/lib/apt/lists/*

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd

COPY ./ /var/www
WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader


COPY nginx.conf /etc/nginx/nginx.conf
COPY default.conf /etc/nginx/conf.d/

RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

EXPOSE 80 9000

FROM elasticsearch:7.17.1 AS elasticsearch

COPY elasticsearch.yml /etc/elasticsearch/

EXPOSE 9200 9300

CMD ["elasticsearch"]

FROM php-app

COPY --from=elasticsearch /usr/share/elasticsearch /usr/share/elasticsearch

RUN php bin/console doctrine:migrations:migrate

RUN php bin/console fos:elastica:populate

CMD ["nginx", "-g", "daemon off;"]

#RUN php bin/console doctrine:migrations:migrate


