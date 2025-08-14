FROM php:7.2-fpm

# Copy source
COPY src/index.php /var/www/html/index.php
COPY src/robots.txt /var/www/html/robots.txt
COPY src/protected /var/www/html/protected
COPY scripts /var/www/scripts

COPY compose/production/php.ini /usr/local/etc/php/php.ini
COPY compose/config.php /var/www/html/protected/application/conf/config.php
COPY compose/config.d /var/www/html/protected/application/conf/config.d

COPY compose/common/config.php /var/www/html/protected/application/conf/config.php
COPY compose/common/config.d /var/www/html/protected/application/conf/conf-common.d
COPY compose/production/config.d /var/www/html/protected/application/conf/config.d

COPY version.txt /var/www/version.txt
COPY compose/jobs-cron.sh /jobs-cron.sh
COPY compose/recreate-pending-pcache-cron.sh /recreate-pending-pcache-cron.sh
COPY compose/entrypoint.sh /entrypoint.sh

RUN echo "deb http://archive.debian.org/debian buster main contrib non-free" > /etc/apt/sources.list && \
    echo "deb http://archive.debian.org/debian-security buster/updates main contrib non-free" >> /etc/apt/sources.list && \
    apt-get update && apt-get install -y --no-install-recommends \
        curl libcurl4-gnutls-dev locales imagemagick libmagickcore-dev libmagickwand-dev zip \
        ruby ruby-dev libpq-dev gnupg git \
        libfreetype6-dev libjpeg62-turbo-dev libpng-dev sudo procps \
    #instalação do node 14 
    && curl -sL https://deb.nodesource.com/setup_14.x | bash - \
    && apt-get install -y nodejs \
    # Install uglify and terser
    && npm install -g \
        terser \
        uglifycss \
        autoprefixer \
    # Install sass
    && gem install sass -v 3.4.22 \
    # Install extensions
    && docker-php-ext-install opcache pdo_pgsql zip xml curl json sockets \
    # Install GD
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    # Install APCu
    && pecl install apcu \
    && echo "extension=apcu.so" > /usr/local/etc/php/conf.d/apcu.ini \
    # Install imagick
    && pecl install imagick-beta \
    && echo "extension=imagick.so" > /usr/local/etc/php/conf.d/ext-imagick.ini \
    # Install composer
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --version=1.10.16 --install-dir=/usr/local/bin && \
    rm composer-setup.php \
    # Install redis
    && pecl install -o -f redis-5.3.7 \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis \
    # Instalação da pasta vendor
    && cd /var/www/html/protected && composer.phar install && composer.phar update \
    && cd /var/www/html/protected/application/themes/ \
    && find . -maxdepth 1 -mindepth 1 -exec echo "compilando sass do tema " {} \; -exec sass {}/assets/css/sass/main.scss {}/assets/css/main.css -E "UTF-8" \; \
    && mkdir -p /var/www/html/protected/DoctrineProxies \
    && ln -s /var/www/html/protected/application/lib/postgis-restful-web-service-framework /var/www/html/geojson \
    && ln -s /var/www/html /var/www/src \
    && chown -R www-data:www-data /var/www/ \
    && apt-get clean && rm -rf /var/lib/apt/lists

ENTRYPOINT ["/entrypoint.sh"]

WORKDIR /var/www/html/
EXPOSE 9000

CMD ["php-fpm"]
