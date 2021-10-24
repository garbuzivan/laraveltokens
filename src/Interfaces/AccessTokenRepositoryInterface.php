<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Interfaces;

use DateTime;
use Garbuzivan\Laraveltokens\Exceptions\UserNotExistsException;
use Garbuzivan\Laraveltokens\Models\AccessToken;
use Illuminate\Database\Eloquent\Collection;

interface AccessTokenRepositoryInterface
{
    /**
     * Создать токен
     *
     * @param string        $title      - заголовок токена
     * @param DateTime|null $expiration - до когда действует токен, null - бессрочно
     * @param int           $user_id    - ID клиента
     * @param string        $user_type  - класс полиморфной связи
     * @param string        $token      - токен
     *
     * @return AccessToken
     */
    public function createAccessToken(
        string    $title,
        ?DateTime $expiration = null,
        int       $user_id,
        string    $user_type,
        string    $token
    ): AccessToken;

    /**
     * Удалить токен по ID токена
     *
     * @param int $token_id - ID токена
     *
     * @return bool
     */
    public function deleteAccessTokenById(int $token_id): bool;

    /**
     * Удалить токен
     *
     * @param string $token
     *
     * @return bool
     */
    public function deleteAccessToken(string $token): bool;

    /**
     * Удалить все токены пользователя по id пользователя
     *
     * @param int    $user_id
     * @param string $user_type
     *
     * @return bool
     */
    public function deleteAccessTokenByUser(int $user_id, string $user_type): bool;

    /**
     * Очистить таблицу токенов
     *
     * @return bool
     */
    public function deleteAllAccessToken(): bool;

    /**
     * Деактивировать токен (прекратить срок действия токена) по ID токена
     *
     * @param int $token_id - ID токена
     *
     * @return bool
     */
    public function deactivationAccessTokenById(int $token_id): bool;

    /**
     * Деактивировать токен (прекратить срок действия токена) по токену
     *
     * @param string $token
     *
     * @return bool
     */
    public function deactivationAccessToken(string $token): bool;

    /**
     * Деактивировать токен (прекратить срок действия токена) по id пользователя
     *
     * @param int    $user_id
     * @param string $user_type
     *
     * @return bool
     */
    public function deactivationAccessTokenByUser(int $user_id, string $user_type): bool;

    /**
     * Продлить срок действия токена по id токена
     *
     * @param int           $token_id
     * @param DateTime|null $expiration
     *
     * @return bool
     */
    public function prolongationAccessTokenById(int $token_id, ?DateTime $expiration = null): bool;

    /**
     * Продлить срок действия всех токенов по id пользователя
     *
     * @param int           $user_id
     * @param string        $user_type
     * @param DateTime|null $expiration
     *
     * @return bool
     */
    public function prolongationAccessTokenByUser(int $user_id, string $user_type, ?DateTime $expiration = null): bool;

    /**
     * Продлить срок действия токена по токену
     *
     * @param string        $token
     * @param DateTime|null $expiration
     *
     * @return bool
     */
    public function prolongationAccessToken(string $token, ?DateTime $expiration = null): bool;

    /**
     * Получить коллекцию токенов по ID пользователя
     *
     * @param int    $user_id
     * @param string $user_type
     *
     * @return Collection|null
     */
    public function getAccessTokenByUser(int $user_id, string $user_type): ?Collection;

    /**
     * Получить данные токена по ID
     *
     * @param int $token_id
     *
     * @return AccessToken|null
     */
    public function getAccessTokenById(int $token_id): ?AccessToken;

    /**
     * Получить данные о токене
     *
     * @param string $token
     *
     * @return AccessToken|null
     */
    public function getAccessToken(string $token): ?AccessToken;

    /**
     * Фиксация последней активности токена
     *
     * @param int $token_id
     *
     * @return bool
     */
    public function setLastUseAccessToken(int $token_id): bool;
}
