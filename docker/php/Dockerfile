FROM php:7.4-fpm

LABEL maintainer="Aziz Muzafarov <bemyslavedarlin@gmail.com>"

# Set up environments
ENV DEBIAN_FRONTEND noninteractive
ENV TERM xterm-color
ENV EXPOSE_PORT=8000
ENV PHP_INI_PATH=/usr/local/etc/php/php.ini
ENV FPM_CONF_PATH=/usr/local/etc/php-fpm.d/www.conf
ENV PATH /root/composer/vendor/bin:/app/vendor/bin:/app/bin:${PATH}

# Update reps
RUN apt-get -y update

# Set up timezone
RUN apt-get install --no-install-recommends -yq tzdata
RUN cp /usr/share/zoneinfo/Europe/Moscow /etc/localtime

# Install libs
RUN apt-get install --no-install-recommends -yq \
    mc \
    nano \
    git \
    wget \
    curl \
    zip \
    unzip \
    g++ \
    re2c \
    ca-certificates \
    zlib1g-dev \
    libicu-dev \
    libpq-dev \
    libzip-dev

# Install intl
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl

# Install extra php extentions
RUN docker-php-ext-install zip
RUN docker-php-ext-install pdo_mysql

# Update and install pecl extra php extentions
RUN pecl update-channels
RUN pecl install psr

COPY modules/* /usr/local/etc/php/conf.d/

# Set up php.ini and configuring it
RUN cp /usr/local/etc/php/php.ini-production ${PHP_INI_PATH} \
    && sed -i -e 's/^default_charset = .*/default_charset = "UTF-8"/g' ${PHP_INI_PATH} \
    && sed -i -e 's/^default_mimetype = .*/default_mimetype = "text\/json"/g' ${PHP_INI_PATH} \
    && sed -i -e 's/^memory_limit = .*/memory_limit = -1/g' ${PHP_INI_PATH} \
    && sed -i -e 's/^max_execution_time = .*/max_execution_time = -1/g' ${PHP_INI_PATH} \
    && sed -i -e 's/^post_max_size = .*/post_max_size = 8M/g' ${PHP_INI_PATH} \
    && sed -i -e 's/^upload_max_filesize = .*/upload_max_filesize = 16M/g' ${PHP_INI_PATH}

RUN echo "#!/bin/sh\nexit 0" > /usr/sbin/policy-rc.d \
    && sed -i -e "s/^;clear_env = no$/clear_env = no/" ${FPM_CONF_PATH} \
    && sed -i -e "/listen = .*/c\listen = [::]:${EXPOSE_PORT}" ${FPM_CONF_PATH}

# Install composer
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
    && curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
    && php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer

# Clean
RUN apt-get autoremove -y \
    && apt-get clean -y \
    && rm -rf /tmp/* /var/tmp/* \
    && rm -rf /var/lib/apt/lists/* \
    && find /var/cache -type f -delete \
    && find /var/log -type f | while read f; do echo -n '' > ${f}; done

WORKDIR /app

RUN mkdir /app/var/
RUN chmod -R 777 /app/var/

EXPOSE ${EXPOSE_PORT}

CMD [ "php-fpm", "--nodaemonize", "--fpm-config=/usr/local/etc/php-fpm.d/www.conf"]
