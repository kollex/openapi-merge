FROM php:8.1-cli-alpine

RUN php --version

RUN docker-php-ext-configure pcntl --enable-pcntl \
  && docker-php-ext-install \
    pcntl

ENV COMPOSER_ALLOW_SUPERUSER 1

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apk add git \
    && rm -rf /var/cache/apk/* /var/tmp/* /tmp/*

ARG COMPOSER_REQUIRE_VERSION=dev-main

RUN composer global require marcelthole/openapi-merge:"$COMPOSER_REQUIRE_VERSION" \
	&& composer clear-cache

VOLUME ["/app"]
WORKDIR /app

ENTRYPOINT ["/root/.composer/vendor/bin/openapi-merge"]
