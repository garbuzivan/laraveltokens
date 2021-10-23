<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Interfaces;

use DateTime;
use Garbuzivan\Laraveltokens\Models\GlobalToken;

interface GlobalTokenRepositoryInterface
{
    /**
     * Создать токен
     *
     * @param string        $title      - заголовок токена
     * @param DateTime|null $expiration - до когда действует токен, null - бессрочно
     * @param string        $token      - токен
     *
     * @return GlobalToken
     */
    public function createGlobalToken(
        string    $title,
        ?DateTime $expiration = null,
        string    $token
    ): GlobalToken;

    /**
     * Удалить токен по ID токена
     *
     * @param int $token_id - ID токена
     *
     * @return bool
     */
    public function deleteGlobalTokenById(int $token_id): bool;

    /**
     * Удалить токен
     *
     * @param string $token
     *
     * @return bool
     */
    public function deleteGlobalToken(string $token): bool;

    /**
     * Очистить таблицу токенов
     *
     * @return bool
     */
    public function deleteAllGlobalToken(): bool;

    /**
     * Деактивировать токен (прекратить срок действия токена) по ID токена
     *
     * @param int $token_id - ID токена
     *
     * @return bool
     */
    public function deactivationGlobalTokenById(int $token_id): bool;

    /**
     * Деактивировать токен (прекратить срок действия токена) по токену
     *
     * @param string $token
     *
     * @return bool
     */
    public function deactivationGlobalToken(string $token): bool;

    /**
     * Продлить срок действия токена по id токена
     *
     * @param int           $token_id
     * @param DateTime|null $expiration
     *
     * @return bool
     */
    public function prolongationGlobalTokenById(int $token_id, ?DateTime $expiration = null): bool;

    /**
     * Продлить срок действия токена по токену
     *
     * @param string        $token
     * @param DateTime|null $expiration
     *
     * @return bool
     */
    public function prolongationGlobalToken(string $token, ?DateTime $expiration = null): bool;

    /**
     * Получить данные токена по ID
     *
     * @param int $token_id
     *
     * @return GlobalToken|null
     */
    public function getGlobalTokenById(int $token_id): ?GlobalToken;

    /**
     * Получить данные о токене
     *
     * @param string $token
     *
     * @return GlobalToken|null
     */
    public function getGlobalToken(string $token): ?GlobalToken;

    /**
     * Фиксация последней активности токена
     *
     * @param string $token
     *
     * @return bool
     */
    public function setLastUseGlobalToken(string $token): bool;
}
