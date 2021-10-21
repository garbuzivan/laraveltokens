[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/garbuzivan/laraveltokens/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/garbuzivan/laraveltokens/?branch=main)
[![Code Coverage](https://scrutinizer-ci.com/g/garbuzivan/laraveltokens/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/garbuzivan/laraveltokens/?branch=main)
[![Build Status](https://scrutinizer-ci.com/g/garbuzivan/laraveltokens/badges/build.png?b=main)](https://scrutinizer-ci.com/g/garbuzivan/laraveltokens/build-status/main)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/garbuzivan/laraveltokens/badges/code-intelligence.svg?b=main)](https://scrutinizer-ci.com/code-intelligence)

# Laravel tokens

Package - manager for working with personal and global tokens in Laravel + API Middleware

### Отличительные особенности

- Пакет позволяет опционально хранить токены в закрытым или открытом виде опционально.
- Возможность привязать токены к пользователю
- Возможность создать глобальные токены, которые позволяют получить доступ через свой Middleware без привязки к
  пользователю
- Собственный Middleware
- Возможность отслеживать последнюю активность токена
- Консольные команды для создания, редактирования и удаления

## Локальная установка пакета после генерации, без публикации в GIT и PACKAGIST

Добавить в секцию repositories файла composer.json путь пакета в формате:

<pre>
"repositories": [
    {
        "type": "path",
        "url": "./packages/garbuzivan/laraveltokens/"
    }
]
</pre>

## Install - Установка

<pre>composer require garbuzivan/laraveltokens</pre>

## Добавление ServiceProvider в config/app.php секция 'providers'

<pre>Garbuzivan\Laraveltokens\ServiceProvider::class,</pre>

## Конфигурационный файл

<pre>php artisan vendor:publish --force --provider="Garbuzivan\Laraveltokens\ServiceProvider" --tag="config"</pre>

## Подключение Middleware

Добавить в файл app/Http/Kernel.php в $middlewareGroups блок "api" новый Middleware

```\Garbuzivan\Laraveltokens\Middleware\LaravelTokens::class,```

Добавить в файл app/Http/Kernel.php в $routeMiddleware

```'auth.laravel.tokens' => \Garbuzivan\Laraveltokens\Middleware\LaravelTokens::class,```

## Подключить Trait к Model/User

```use UserTrait;```

## Консольные команды

Список команд можно посмотреть в artisan. Доступно создание, удаление и продление токенов

```php artisan token```

## Пример применения Route::middleware

<pre>Route::get('/', function () {
    return "Test API auth.laravel.tokens - Garbuzivan\Laraveltokens\Middleware\LaravelTokens";
})->middleware(['auth.laravel.tokens']);</pre>
