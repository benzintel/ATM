# Benzintel ATM

# How to install

  - install Docker  [https://docs.docker.com]
  - cd path project
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
