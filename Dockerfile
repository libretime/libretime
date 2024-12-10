ARG LIBRETIME_VERSION
#======================================================================================#
# Python Builder                                                                       #
#======================================================================================#
FROM python:3.10-slim-bullseye AS python-builder

WORKDIR /build

# Wheels
WORKDIR /build/shared
COPY shared .
RUN pip wheel --wheel-dir . --no-deps .

WORKDIR /build/api-client
COPY api-client .
RUN pip wheel --wheel-dir . --no-deps .

#======================================================================================#
# Python base                                                                          #
#======================================================================================#
FROM python:3.10-slim-bullseye AS python-base

ENV PYTHONDONTWRITEBYTECODE=1
ENV PYTHONUNBUFFERED=1

# Custom user
ARG USER=libretime
ARG UID=1000
ARG GID=1000

RUN set -eux \
    && adduser --disabled-password --uid=$UID --gecos '' --no-create-home ${USER} \
    && install --directory --owner=${USER} /etc/libretime /srv/libretime

ENV LIBRETIME_CONFIG_FILEPATH=/etc/libretime/config.yml

# Shared packages
COPY tools/packages.py /tmp/packages.py
COPY shared/packages.ini /tmp/packages.ini

RUN set -eux \
    && DEBIAN_FRONTEND=noninteractive apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    $(python3 /tmp/packages.py --format=line --exclude=python bullseye /tmp/packages.ini) \
    && rm -rf /var/lib/apt/lists/* \
    && rm -f /tmp/packages.py /tmp/packages.ini

#======================================================================================#
# Python base with ffmpeg                                                              #
#======================================================================================#
FROM python-base AS python-base-ffmpeg

RUN set -eux \
    && DEBIAN_FRONTEND=noninteractive apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    ffmpeg \
    && rm -rf /var/lib/apt/lists/*

#======================================================================================#
# Analyzer                                                                             #
#======================================================================================#
FROM python-base-ffmpeg AS libretime-analyzer

COPY tools/packages.py /tmp/packages.py
COPY analyzer/packages.ini /tmp/packages.ini

RUN set -eux \
    && DEBIAN_FRONTEND=noninteractive apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    $(python3 /tmp/packages.py --format=line --exclude=python bullseye /tmp/packages.ini) \
    && rm -rf /var/lib/apt/lists/* \
    && rm -f /tmp/packages.py /tmp/packages.ini

WORKDIR /src

COPY analyzer/requirements.txt .
RUN --mount=type=cache,target=/root/.cache/pip \
    pip install --no-compile -r requirements.txt

COPY --from=python-builder /build/shared/*.whl .
RUN --mount=type=cache,target=/root/.cache/pip \
    pip install --no-compile *.whl && rm -Rf *.whl

COPY analyzer .
RUN --mount=type=cache,target=/root/.cache/pip \
    pip install --editable .[sentry]

# Run
USER ${UID}:${GID}
WORKDIR /app

CMD ["/usr/local/bin/libretime-analyzer"]

ARG LIBRETIME_VERSION
ENV LIBRETIME_VERSION=$LIBRETIME_VERSION

#======================================================================================#
# Playout                                                                              #
#======================================================================================#
FROM python-base-ffmpeg AS libretime-playout

COPY tools/packages.py /tmp/packages.py
COPY playout/packages.ini /tmp/packages.ini

RUN set -eux \
    && DEBIAN_FRONTEND=noninteractive apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    $(python3 /tmp/packages.py --format=line --exclude=python bullseye /tmp/packages.ini) \
    && rm -rf /var/lib/apt/lists/* \
    && rm -f /tmp/packages.py /tmp/packages.ini

WORKDIR /src

COPY playout/requirements.txt .
RUN --mount=type=cache,target=/root/.cache/pip \
    pip install --no-compile -r requirements.txt

COPY --from=python-builder /build/shared/*.whl .
COPY --from=python-builder /build/api-client/*.whl .
RUN --mount=type=cache,target=/root/.cache/pip \
    pip install --no-compile *.whl && rm -Rf *.whl

COPY playout .
RUN --mount=type=cache,target=/root/.cache/pip \
    pip install --editable .[sentry]

# Run
USER ${UID}:${GID}
WORKDIR /app

CMD ["/usr/local/bin/libretime-playout"]

ARG LIBRETIME_VERSION
ENV LIBRETIME_VERSION=$LIBRETIME_VERSION

#======================================================================================#
# API                                                                                  #
#======================================================================================#
FROM python-base AS libretime-api

RUN set -eux \
    && DEBIAN_FRONTEND=noninteractive apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    curl \
    gcc \
    libc6-dev \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /src

COPY api/requirements.txt .
RUN --mount=type=cache,target=/root/.cache/pip \
    pip install --no-compile -r requirements.txt

COPY --from=python-builder /build/shared/*.whl .
RUN --mount=type=cache,target=/root/.cache/pip \
    pip install --no-compile *.whl && rm -Rf *.whl

COPY api .
RUN --mount=type=cache,target=/root/.cache/pip \
    pip install --editable .[prod,sentry]

# Run
USER ${UID}:${GID}
WORKDIR /app

CMD ["/usr/local/bin/gunicorn", \
    "--workers=4", \
    "--worker-class=libretime_api.gunicorn.Worker", \
    "--log-file", "-", \
    "--bind=0.0.0.0:9001", \
    "libretime_api.asgi"]

ARG LIBRETIME_VERSION
ENV LIBRETIME_VERSION=$LIBRETIME_VERSION

HEALTHCHECK CMD ["curl", "--fail", "http://localhost:9001/api/v2/version"]

#======================================================================================#
# Worker                                                                               #
#======================================================================================#
FROM python-base AS libretime-worker

WORKDIR /src

COPY worker/requirements.txt .
RUN --mount=type=cache,target=/root/.cache/pip \
    pip install --no-compile -r requirements.txt

COPY --from=python-builder /build/shared/*.whl .
COPY --from=python-builder /build/api-client/*.whl .
RUN --mount=type=cache,target=/root/.cache/pip \
    pip install --no-compile *.whl && rm -Rf *.whl

COPY worker .
RUN --mount=type=cache,target=/root/.cache/pip \
    pip install --editable .[sentry]

# Run
USER ${UID}:${GID}
WORKDIR /app

CMD ["/usr/local/bin/libretime-worker"]
ARG LIBRETIME_VERSION
ENV LIBRETIME_VERSION=$LIBRETIME_VERSION

#======================================================================================#
# Legacy                                                                               #
#======================================================================================#
FROM php:8.4-fpm AS libretime-legacy

ENV LIBRETIME_CONFIG_FILEPATH=/etc/libretime/config.yml
ENV LIBRETIME_LOG_FILEPATH=php://stderr

# Custom user
ARG USER=libretime
ARG UID=1000
ARG GID=1000

RUN set -eux \
    && adduser --disabled-password --uid=$UID --gecos '' --no-create-home ${USER} \
    && install --directory --owner=${USER} /etc/libretime /srv/libretime

RUN set -eux \
    && DEBIAN_FRONTEND=noninteractive apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    gettext \
    libcurl4-openssl-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libonig-dev \
    libpng-dev \
    libpq-dev \
    libxml2-dev \
    libyaml-dev \
    libzip-dev \
    locales \
    unzip \
    zlib1g-dev \
    && rm -rf /var/lib/apt/lists/* \
    && pecl install apcu yaml \
    && docker-php-ext-enable apcu yaml \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    bcmath \
    curl \
    exif \
    gd \
    gettext \
    mbstring \
    opcache \
    pdo_pgsql \
    pgsql \
    sockets \
    xml

COPY legacy/locale/locale.gen /etc/locale.gen
RUN locale-gen

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY "legacy/install/php/libretime-legacy.ini" "$PHP_INI_DIR/conf.d/"

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

COPY legacy/composer.* ./
RUN composer --no-cache install --no-progress --no-interaction --no-dev --no-autoloader

COPY legacy .
RUN set -eux \
    && make locale-build \
    && composer --no-cache dump-autoload --no-interaction --no-dev

# Run
USER ${UID}:${GID}

ARG LIBRETIME_VERSION
ENV LIBRETIME_VERSION=$LIBRETIME_VERSION
