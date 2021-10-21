<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Repositories;

use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Garbuzivan\Laraveltokens\Exceptions\UserNotExistsException;
use Garbuzivan\Laraveltokens\Interfaces\TokenRepositoryInterface;
use Garbuzivan\Laraveltokens\Models\Token;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TokenRepository implements TokenRepositoryInterface
{
    /**
     * Создать токен
     * @param string $title - заголовок токена
     * @param DateTime $expiration - до когда действует токен
     * @param null|int $user_id - ID пользователя или null для глобального токена
     * @param string|null $token - токен, если null - создается автоматически
     * @return Token
     */
    public function create(string $title, DateTime $expiration, ?int $user_id = null, string $token = null): Token
    {
        return Token::create([
            'token' => $token,
            'user_id' => $user_id,
            'title' => $title,
            'expiration' => $expiration,
        ]);
    }

    /**
     * Удалить токен по ID токена
     * @param int $token_id - ID токена
     * @return bool
     */
    public function deleteById(int $token_id): bool
    {
        return Token::where('id', $token_id)->delete();
    }

    /**
     * Удалить токен
     * @param string $token
     * @return bool
     */
    public function deleteByToken(string $token): bool
    {
        return Token::where('token', $token)->delete();
    }

    /**
     * Удалить все токены пользователя по id пользователя
     * @param int $user_id
     * @return bool
     */
    public function deleteByUser(int $user_id): bool
    {
        return Token::where('user_id', $user_id)->delete();
    }

    /**
     * Очистить таблицу токенов
     * @return bool
     */
    public function deleteAll(): bool
    {
        DB::table('sensors')->truncate();
        return true;
    }

    /**
     * Деактивировать токен (прекратить срок действия токена) по ID токена
     * @param int $token_id - ID токена
     * @return bool
     */
    public function deactivationById(int $token_id): bool
    {
        return (bool)Token::where('id', $token_id)->update([
            'expiration' => Carbon::now()->subMinutes()
        ]);
    }

    /**
     * Деактивировать токен (прекратить срок действия токена) по токену
     * @param string $token
     * @return bool
     */
    public function deactivationByToken(string $token): bool
    {
        return (bool)Token::where('token', $token)->update([
            'expiration' => Carbon::now()->subMinutes()
        ]);
    }

    /**
     * Деактивировать токен (прекратить срок действия токена) по id пользователя
     * @param int $user_id
     * @return bool
     */
    public function deactivationByUser(int $user_id): bool
    {
        return (bool)Token::where('user_id', $user_id)->update([
            'expiration' => Carbon::now()->subMinutes()
        ]);
    }

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
    ): bool
    {
        return (bool)Token::where('id', $token_id)->update([
            'token' => $token,
            'user_id' => $user_id,
            'title' => $title,
            'expiration' => $expiration,
        ]);
    }

    /**
     * Получить список токенов по ID пользователя
     * @param int $user_id
     * @return Collection|null
     */
    public function getByUser(int $user_id): ?Collection
    {
        return Token::where('user_id', $user_id)->orderByDesc('expiration')->get();
    }

    /**
     * Получить токен по ID
     * @param int $token_id
     * @return Token|null
     */
    public function getById(int $token_id): ?Token
    {
        return Token::where('id', $token_id)->first();
    }

    /**
     * Получить данные о токене
     * @param string $token
     * @return Token|null
     */
    public function getByToken(string $token): ?Token
    {
        return Token::where('token', $token)->first();
    }

    /**
     * Фиксация последней активности токена
     * @param string $token
     * @return bool
     */
    public function setLastUse(string $token): bool
    {
        return (bool)Token::where('token', $token)->update([
            'last_use' => Carbon::now()->subMinutes()
        ]);
    }

    /**
     * Получение пользователя
     * @param int|null $user_id
     * @return User|null
     * @throws UserNotExistsException
     */
    public function getUser(?int $user_id = null): ?User
    {
        $user = null;
        if (!is_null($user_id)) {
            $user = User::find($user_id);
            if (!$user instanceof User) {
                throw new UserNotExistsException;
            }
        }
        return $user;
    }
}
