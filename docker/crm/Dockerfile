FROM ubuntu:22.04 as base

# No tty
ENV DEBIAN_FRONTEND noninteractive
ENV TZ Europe/Berlin

RUN apt-get update \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        software-properties-common \
        gpg-agent \
        zip \
        unzip  \
        memcached  \
    && add-apt-repository -y ppa:ondrej/php \
    && apt-get -y --no-install-recommends install \
        supervisor \
        apache2 \
        php7.1-fpm \
        php7.1-mysql \
        php7.1-xml \
        php7.1-gd \
        php7.1-mbstring \
        php7.1-bcmath \
        php7.1-zip \
        php7.1-memcache \
        php7.1-curl \
        php7.1-bz2 \
        php7.1-cli \
        php7.1-enchant \
        php7.1-imagick \
        php7.1-imap \
        php7.1-intl \
        php7.1-mcrypt \
        php7.1-memcached \
        php7.1-msgpack \
        php7.1-soap \
        php7.1-xsl \
    && a2enconf php7.1-fpm \
    && a2enmod proxy \
    && a2enmod proxy_fcgi \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Logs
RUN ln -sf /dev/stdout /var/log/apache2/access.log \
 && ln -sf /dev/stderr /var/log/apache2/error.log \
 && ln -sf /dev/stderr /var/log/php7.1-fpm.log \
 && ln -sf /dev/stderr /var/log/apache2/error_php.log

# Supervisor config
RUN mkdir -p /run/php/ && rm -rf /var/www/html/*
COPY ./docker/crm/config/supervisord/supervisord.conf /etc/supervisor/supervisord.conf
COPY ./docker/crm/config/supervisord/conf.d/ /etc/supervisor/conf.d/

# Apache config
COPY ./docker/crm/config/apache/000-default.conf /etc/apache2/sites-enabled/000-default.conf
COPY ./docker/crm/config/apache/js.js.com.conf /etc/apache2/sites-enabled/js.js.com.conf

# PHP config
COPY ./docker/crm/config/php/php.ini /etc/php/7.1/fpm/php.ini
COPY ./docker/crm/config/php/php-fpm.conf /etc/php/7.1/fpm/php-fpm.conf
COPY ./docker/crm/config/php/pool.d/www.conf /etc/php/7.1/fpm/pool.d/www.conf

# Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php \
 && php -r "unlink('composer-setup.php');" \
 && mv composer.phar /usr/local/bin/composer

#################
# Release image #
#################
FROM base as release
# Code
## CRM
COPY --chown=www-data:www-data ./src/crm/ /var/www/html

## Messenger
COPY --chown=www-data:www-data ./src/messenger /var/www/private/messenger
WORKDIR /var/www/private/messenger
RUN rm composer.lock \
    && composer install --no-dev --no-interaction --optimize-autoloader
RUN mv .env.example .env

## idktime
COPY --chown=www-data:www-data ./src/idktime /var/www/private/idktime
WORKDIR /var/www/private/idktime
RUN rm composer.lock \
    && composer install --no-dev --no-interaction --optimize-autoloader

## online-viza
COPY --chown=www-data:www-data ./src/online-viza /var/www/private/online-viza
WORKDIR /var/www/private/online-viza
RUN rm composer.lock \
    && composer config --no-interaction --no-plugins allow-plugins.kylekatarnls/update-helper true \
    && composer install --no-dev --no-interaction --optimize-autoloader \
    && composer clear-cache

# Shutdown script
COPY ./docker/crm/scripts/stop.sh /usr/local/bin/stop.sh
RUN chmod +x /usr/local/bin/stop.sh

WORKDIR /var/www/html

EXPOSE 80

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]

#############
# Dev image #
#############
FROM base as dev
RUN mkdir /php-sessions && chown www-data:www-data /php-sessions
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        php7.1-xdebug \
    && rm -rf /var/lib/apt/lists/*
RUN echo "xdebug.remote_host=host.docker.internal" >> /etc/php/7.1/fpm/php.ini
RUN echo "xdebug.remote_autostart=1" >> /etc/php/7.1/fpm/php.ini
RUN echo "xdebug.remote_enable=1" >> /etc/php/7.1/fpm/php.ini
WORKDIR /var/www/html

EXPOSE 80

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
