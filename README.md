# Benzintel ATM

# How to install
  - cd laravel
  - copy file .env.example and new file name .env same content .env.example
  -----------------------------
  - install Docker  [https://docs.docker.com]
```sh
$ sh start.sh
```
  - Wait for install all container
  - New Terminal
```sh
$ docekr ps
$ docker exec -it world-check-php-fpm /bin/sh
$ composer install
$ chmod -R 777 public
$ chmod -R 777 storage
$ php artisan key:generate
$ exit
```
  - goto browser url: localhost
