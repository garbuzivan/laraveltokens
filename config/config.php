<?php

/*
 * Garbuzivan\Laraveltokens
 */
return [
    /*
     * Хранение в токена в открытом или зашифрованном виде
     * Зашифрованный тип хранения отключает возможность просматривать токен или вносить его вручную
     * @var bool
     */
    'salt'              => env('LARAVEL_TOKENS_SALT', 'Fo3SMqqUbrxKJMQW0sVOB4Q'),

    /*
     * Хранение в токена в открытом или зашифрованном виде
     * Зашифрованный тип хранения отключает возможность просматривать токен или вносить его вручную
     * @var bool
     */
    'encryption'        => env('LARAVEL_TOKENS_ENCRYPTION', false),

    /*
     * Автоматическое удаление токенов с законченым сроком действия
     * @var bool
     */
    'auto_clear'        => env('LARAVEL_TOKENS_AUTO_CLEAR', true),

    /*
     * Пауза перед автоматическим удалением токенов в секундах
     * @var int
     */
    'auto_clear_pause'  => env('LARAVEL_TOKENS_AUTO_CLEAR_PAUSE', 8640000),

    /*
     * Репозиторий Global
     * @var int
     */
    'repository_access' => \Garbuzivan\Laraveltokens\Repositories\AccessTokenRepository::class,

    /*
     * Репозиторий Global
     * @var int
     */
    'repository_global' => \Garbuzivan\Laraveltokens\Repositories\GlobalTokenRepository::class,

    /*
     * Фиксация последней активности токена
     * @var bool
     */
    'last_use'          => env('LARAVEL_TOKENS_LAST_USE', true),

    /*
     * Активация режима JWT
     * @var bool
     */
    'jwt'               => env('LARAVEL_TOKENS_JWT', true),
];
