<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Interfaces;

use App\Models\User;
use DateTime;
use Garbuzivan\Laraveltokens\Exceptions\UserNotExistsException;
use Garbuzivan\Laraveltokens\Models\Token;
use Illuminate\Database\Eloquent\Collection;

interface TokenRepositoryInterface
{
    /**
     * Создать токен
     * @param string $title - заголовок токена
     * @param DateTime $expiration - до когда действует токен
     * @param null|int $user_id - ID пользователя или null для глобального токена
     * @param string|null $token - токен, если null - создается автоматически
     * @return Token
     */
    public function create(string $title, DateTime $expiration, ?int $user_id = null, string $token = null): Token;


    /**
     * Удалить токен по ID токена
     * @param int $token_id - ID токена
     * @return bool
     */
    public function deleteById(int $token_id): bool;

    /**
     * Удалить токен
     * @param string $token
     * @return bool
     */
    public function deleteByToken(string $token): bool;

    /**
     * Удалить все токены пользователя по id пользователя
     * @param int $user_id
     * @return bool
     */
    public function deleteByUser(int $user_id): bool;

    /**
     * Очистить таблицу токенов
     * @return bool
     */
    public function deleteAll(): bool;

    /**
     * Деактивировать токен (прекратить срок действия токена) по ID токена
     * @param int $token_id - ID токена
     * @return bool
     */
    public function deactivationById(int $token_id): bool;

    /**
     * Деактивировать токен (прекратить срок действия токена) по токену
     * @param string $token
     * @return bool
     */
    public function deactivationByToken(string $token): bool;

    /**
     * Деактивировать токен (прекратить срок действия токена) по id пользователя
     * @param int $user_id
     * @return bool
     */
    public function deactivationByUser(int $user_id): bool;

    /**
     * Редактировать токен
     * @param int $token_id - ID токена
     * @param string $title - заголовок токена
     * @param DateTime $expiration - до когда действует токен
     * @param null|int $user_id - ID пользователя или null для глобального токена
     * @param string|null $token - токен, если null - создается автоматически
     * @return bool
     */
    public function edit(
        int      $token_id,
        string   $title,
        DateTime $expiration,
        ?int     $user_id = null,
        string   $token = null
    ): bool;

    /**
     * Получить коллекцию токенов по ID пользователя
     * @param int $user_id
     * @return Collection|null
     */
    public function getByUser(int $user_id): ?Collection;

    /**
     * Получить данные токена по ID
     * @param int $token_id
     * @return Token|null
     */
    public function getById(int $token_id): ?Token;

    /**
     * Получить данные о токене
     * @param string $token
     * @return Token|null
     */
    public function getByToken(string $token): ?Token;

    /**
     * Фиксация последней активности токена
     * @param string $token
     * @return bool
     */
    public function setLastUse(string $token): bool;

    /**
     * Получение пользователя
     * @param int|null $user_id
     * @return User|null
     * @throws UserNotExistsException
     */
    public function getUser(?int $user_id = null): ?User;
}
