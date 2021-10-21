<?php

use Garbuzivan\Laraveltokens\Repositories\TokenRepository;

/*
 * Garbuzivan\Laraveltokens
 */
return [
    /*
     * Хранение в токена в открытом или зашифрованном виде
     * Зашифрованный тип хранения отключает возможность просматривать токен или вносить его вручную
     * @var bool
     */
    'encryption' => false,

    /*
     * Автоматическое удаление токенов с законченым сроком действия
     * @var bool
     */
    'auto_clear' => true,

    /*
     * Пауза перед автоматическим удалением токенов в секундах
     * @var int
     */
    'auto_clear_pause' => 8640000,

    /*
     * Репозиторий
     * @var int
     */
    'repository' => TokenRepository::class,

    /*
     * Фиксация последней активности токена
     * @var bool
     */
    'last_use' => true,
];
