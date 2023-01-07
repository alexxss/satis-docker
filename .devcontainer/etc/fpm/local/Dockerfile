FROM scratch AS composer-installer
ADD --link https://raw.githubusercontent.com/composer/getcomposer.org/298f4ba7b62708d16c291573e1c52811b5e494bd/web/installer  /composer-installer

FROM php:8.4-fpm AS base

RUN <<EOF
  apt update
  apt install -y \
    libzip-dev zip unzip \
    libbz2-dev
  docker-php-ext-install zip bz2
  apt clean
EOF

RUN --mount=from=composer-installer,source=/composer-installer,target=/composer-installer <<EOF
  /usr/local/bin/php /composer-installer
  mv composer.phar /usr/local/bin/composer
EOF

ARG FPM_UID
ARG FPM_GID

RUN <<EOF
  addgroup --gid ${FPM_GID} fpm
  adduser --disabled-password --gecos "" --uid ${FPM_UID} --gid ${FPM_GID} fpm
EOF

USER fpm
