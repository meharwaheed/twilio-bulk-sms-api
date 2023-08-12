FROM dock.abdulwaheed.me/devbox/base:latest as BasePHP82

WORKDIR /home/app

# Copy project from local into docker container
COPY . /home/app

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN chmod -R 777 /home/app/public \
    && touch    /home/app/storage/logs/laravel.log \
    && chmod -R 777 /home/app/storage \
    && mkdir -p /home/app/bootstrap/cache \
    && chmod -R 777 /home/app/bootstrap/cache \
    && cd /home/app \
    && composer install && cp .env.example .env && php artisan key:generate
