Yii 2 приложение как тестовое задание для компании CheJam.

INSTALLATION
------------

Задайте cookie validation key в файле `config/web.php`, в виде строки из случайных символов:

```php
'request' => [
    'cookieValidationKey' => '<secret random string goes here>',
]
```

Укажите github token в файле `config/params.php`, чтобы не попасть снять ограничение в 60 запросов:

```php
return [
    'github_token' => '<token>',
]
```

Установка пакетов приложения

    sudo docker-compose run --rm php composer update --prefer-dist
    sudo docker-compose run --rm php composer install    
    
Запуск контейнеров

    sudo docker-compose up -d
    
Создать миграцию для таблицы файлов:

    sudo docker-compose run --rm php yii migrate

Приложение доступно по адресу:

    http://127.0.0.1:8000
