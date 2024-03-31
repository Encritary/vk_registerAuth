# vk_registerAuth

Тестовое задание на стажировку по вакансии [«Back-End Developer«](https://internship.vk.company/vacancy/783) в команду VK ID.

## Требования

Минимальные требования для запуска проекта:
- PHP 8.1 CLI
- Composer

## Установка

Перед запуском нужно установить проект через Composer:

```shell
$ composer install
```

## База данных

Для сервиса может использоваться любая база данных, поддерживаемая расширением PDO для PHP.

В папке `sql` можно найти схемы базы данных:
- [для MySQL](https://github.com/Encritary/vk_registerAuth/blob/main/sql/mysql_schema.sql)
- [для SQLite](https://github.com/Encritary/vk_registerAuth/blob/main/sql/sqlite_schema.sql)

## Настройка

Также перед запуском необходимо создать файл `config.json` и заполнить его согласно примеру конфигурации: `config.example.json`.

В конфигурации нужно указать данные для подключения к БД:

```json
{
  "db": {
    "dsn": "mysql:host=127.0.0.1;dbname=register_auth",
    "username": "someuser",
    "password": "123456"
  }
}
```

## Ключ для JWT

Перед загрузкой можно поместить в файл `jwt_key.dat` ключ для создания и верификации 
токенов JWT, закодированный при помощи Base64. Так как приложение использует алгоритм `HS256`, то ключом может быть
абсолютно любая последовательность байтов.

Если файл `jwt_key.dat` отсутствует, то приложение автоматически сгенерирует 32-байтовый ключ и запишет его в файл.

## Запуск

Чтобы запустить проект, следует воспользоваться ``PHP Built-in Web Server`` в папке проекта:

```shell
$ php -S localhost:8080 router.php
```

Указанная выше команда запустит проект при помощи роутер-файла ``router.php``.

## Тестирование

Можно проверить корректность логики кода, используя тесты PHPUnit.

Для этого можно воспользоваться командой:

```shell
$ composer test
```

## Сборка через Docker

Чтобы собрать образ Docker для сервиса, можно воспользоваться командой:

```shell
$ docker build -t register_auth .
```

Стоит заметить, что Dockerfile изначально собирается только с поддержкой SQLite и MySQL.
Если необходима поддержка иных СУБД, то следует дописать нужные расширения в строчку с командой `docker-php-ext-install`.

Затем можно запустить образ Docker, указав в качестве тома конфигурационный файл и пробросив порт:

```shell
$ docker run -v ./config.json:/app/config.json -p 8080:8080/tcp register_auth
```

## Документация

Документацию по API можно прочитать [в отдельном файле](https://github.com/Encritary/vk_registerAuth/blob/main/API.md).