# EX

## Open API спецификация:

В корне проекта есть файл `openapi.yaml`.
Это актуальная документация проекта.
Можно открыть здесь:

1. [Swagger editor](https://editor.swagger.io/) 
2. [Postman](https://www.postman.com/)
3. [Insomnia](https://nsomnia.rest/download)
4. Прямо в PHP Storm

## Тесты:

Директория `tests/Unit`.

## Запускать через docker (использовал sail)

1. Запуск
```shell
cp .env.example .env # Проверить .env

docker-compose run --rm laravel.test composer install

docker-compose up -d

docker-compose exec laravel.test bash

php artisan migrate
```
2. Создать валюты
```shell
php artisan db:seed --class=CurrenciesSeeder
```
3. Получить курс на сегодня
```shell
php artisan rates:load
```

## Юзер стори:
**P.S. Расписание здесь `app/Console/Kernel.php`. Там же можно и посмотреть команды**

1. Регистрация
2. Авторизация
3. Просмотреть список всех валют (GET: /currencies)
4. Создать кошелек (POST: /wallets)
5. Создать второй кошелек (POST: /wallets)
6. Создать невыполнимую сделку (POST: /exchanges)
7. Создать выполнимую сделку (POST: /exchanges)
8. Проверить список сделок (GET: /exchanges)
9. Удалить невыполнимую сделку (DELETE: /exchanges/{id})
