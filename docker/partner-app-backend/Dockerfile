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
COPY ./docker/partner-app-backend/config/supervisord/supervisord.conf /etc/supervisor/supervisord.conf
COPY ./docker/partner-app-backend/config/supervisord/conf.d/ /etc/supervisor/conf.d/

# Apache config
COPY ./docker/partner-app-backend/config/apache/000-default.conf /etc/apache2/sites-enabled/000-default.conf

# PHP config
COPY ./docker/partner-app-backend/config/php/php.ini /etc/php/7.1/fpm/php.ini
COPY ./docker/partner-app-backend/config/php/php-fpm.conf /etc/php/7.1/fpm/php-fpm.conf
COPY ./docker/partner-app-backend/config/php/pool.d/www.conf /etc/php/7.1/fpm/pool.d/www.conf

#################
# Release image #
#################
FROM base as release
# Code
COPY --chown=www-data:www-data ./src/jobstep-partner/ /var/www/html

# Shutdown script
COPY ./docker/partner-app-backend/scripts/stop.sh /usr/local/bin/stop.sh
RUN chmod +x /usr/local/bin/stop.sh

EXPOSE 80

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]

#############
# Dev image #
#############
FROM base as dev
RUN mkdir /php-sessions && chown www-data:www-data /php-sessions
WORKDIR /var/www/html

EXPOSE 80

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
