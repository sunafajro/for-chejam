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

Update your vendor packages

    docker-compose run --rm php composer update --prefer-dist
    
Run the installation triggers (creating cookie validation code)

    docker-compose run --rm php composer install    
    
Запуск контейнера

    docker-compose up -d
    
Приложение доступно по адресу:

    http://127.0.0.1:8000
