FROM php:7.3.6-fpm-alpine3.10

RUN apk add --no-cache git \
            shadow \ 
            openssl \
            bash \
            mysql-client \
            nodejs \
            npm \
            freetype-dev \
            libjpeg-turbo-dev \
            libpng-dev \
            libzip-dev

RUN touch /home/www-data/.bashrc | echo "PS1='\w\$ '" >> /home/www-data/.bashrc
RUN npm config set cache /var/www/.npm-cache --global

RUN docker-php-ext-install pdo pdo_mysql zip
RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/
RUN docker-php-ext-install -j$(nproc) gd

ENV DOCKERIZE_VERSION v0.6.1
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz

RUN usermod -u 1000 www-data

WORKDIR /var/www
RUN rm -rf /var/www/html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN ln -s public html

USER www-data

EXPOSE 9000
