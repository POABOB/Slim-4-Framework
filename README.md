# SLIM 4 FRAMEWORK

## Introduction

> This project was built with php SLIM 4 framework with ADR mode, whcich is a compatible resolution of RESTful Api.

### Feature

* Framework - SLIM 4
* Container - Docker
  * Http Server - Nginx
  * Database - MariaDB
  * php8 - php-fpm
* Test - Codeception
* Api Document - Swagger-ui
* ORM - Medoo
* CI/CD - Github Actions

### Minimal requirements

* docker/docker-compose
* php: ^8.0
* composer

## Installation

### Config

* Write your Db schema

`init.sql`
```
CREATE DATABASE IF NOT EXISTS Example;
USE Example;
CREATE TABLE IF NOT EXISTS `Example`.`Users` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `name` TEXT NOT NULL, `password` TEXT NOT NULL ,  PRIMARY KEY (`id`)) ENGINE = InnoDB;
```

* DB config

> If you use sqlite for your database, juse commit mysql config.

`docker-compose.yml`
```
  mysql:
    ...
    environment:
      - MYSQL_DATABASE={DB}
      - MYSQL_ROOT_PASSWORD={root_password}
      - MYSQL_USER={user}
      - MYSQL_PASSWORD={user_password}
```

* Generate Secret Key for Jwt

```

```

* .env/.env.test configuration

> If you use sqlite for your database, juse commit mysql config.

```
# dev/prod/stage/test
MODE=dev
# MYSQL CONFIG
DB_DRIVER=mysql
DB_NAME={DB}
DB_HOST=mysql
DB_USER={user}
DB_PASS={user_password}
DB_CHARSET=utf8mb4

# SQLITE CONFIG
# DB_DRIVER=sqlite
# DB_NAME={}./path/Example.db}

# SETTINGS FOR DEBUG (0/1)
# please don't display error details in production environment.
DISPLAY_ERROR_DETAILS=1
LOG_ERROR_DETAILS=1
LOG_ERRORS=1


# JWT SETTINGS
JWT_ISSUER=SLIM_4
JWT_LIFETIME=86400
JWT_PRIVATE_KEY="{PRIVATE KEY}"
JWT_PUBLIC_KEY="{PUBLIC_KEY}"
```

## Run

### dev

```
composer install
docker-compose up -d
```

### prod

```
composer install --no-dev --optimize-autoloader
docker-compose up -d
```

### test

```
<!-- Before you test, please check out your vendor which was instlled. -->
php vendor/bin/codecept run --steps
```
