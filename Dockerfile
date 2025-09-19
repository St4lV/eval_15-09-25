FROM php:8.2-cli
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    libssl-dev \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_sqlite \
    && docker-php-ext-enable \
    pdo \
    pdo_mysql \
    pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY . .

EXPOSE 3001

CMD ["php", "-S", "0.0.0.0:3002", "-t", "."]
