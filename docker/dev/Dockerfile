FROM php:8.2-fpm

ARG UID=1000
ARG GID=1000

RUN apt-get update && apt-get install -y --no-install-recommends \
    default-libmysqlclient-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    default-mysql-client \
    zip \
    unzip \
    git \
    curl \
    cron \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mysqli \
        bcmath \
        gd \
        zip \
        exif \
        intl \
        mbstring \
        opcache

RUN groupadd -g ${GID} app \
 && useradd -u ${UID} -g app -m -s /bin/bash app

RUN git config --global --add safe.directory /var/www

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY --chown=app:app . .
RUN chown -R app:app /var/www

USER app
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

USER root
RUN mkdir -p runtime \
             web/assets \
             console/runtime \
 && chown -R app:app runtime web/assets console/runtime

RUN sed -i 's/^user = .*/user = app/'  /usr/local/etc/php-fpm.d/www.conf \
 && sed -i 's/^group = .*/group = app/' /usr/local/etc/php-fpm.d/www.conf

COPY docker/dev/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
