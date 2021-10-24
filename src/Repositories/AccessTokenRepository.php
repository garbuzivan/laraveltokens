<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Repositories;

use Carbon\Carbon;
use DateTime;
use Garbuzivan\Laraveltokens\Exceptions\UserNotExistsException;
use Garbuzivan\Laraveltokens\Interfaces\AccessTokenRepositoryInterface;
use Garbuzivan\Laraveltokens\Models\AccessToken;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AccessTokenRepository implements AccessTokenRepositoryInterface
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
    ): AccessToken {
        return AccessToken::create([
            'token'      => $token,
            'user_id'    => $user_id,
            'user_type'  => $user_type,
            'title'      => $title,
            'expiration' => $expiration,
        ]);
    }

    /**
     * Удалить токен по ID токена
     *
     * @param int $token_id - ID токена
     *
     * @return bool
     */
    public function deleteAccessTokenById(int $token_id): bool
    {
        return (bool)AccessToken::where('id', $token_id)->delete();
    }

    /**
     * Удалить токен
     *
     * @param string $token
     *
     * @return bool
     */
    public function deleteAccessToken(string $token): bool
    {
        return (bool)AccessToken::where('token', $token)->delete();
    }

    /**
     * Удалить все токены пользователя по id пользователя
     *
     * @param int    $user_id
     * @param string $user_type
     *
     * @return bool
     */
    public function deleteAccessTokenByUser(int $user_id, string $user_type): bool
    {
        return (bool)AccessToken::where('user_id', $user_id)->where('user_type', $user_type)->delete();
    }

    /**
     * Очистить таблицу токенов
     *
     * @return bool
     */
    public function deleteAllAccessToken(): bool
    {
        DB::table('access_tokens')->truncate();
        return true;
    }

    /**
     * Деактивировать токен (прекратить срок действия токена) по ID токена
     *
     * @param int $token_id - ID токена
     *
     * @return bool
     */
    public function deactivationAccessTokenById(int $token_id): bool
    {
        return (bool)AccessToken::where('id', $token_id)->update([
            'expiration' => Carbon::now()->subMinutes(),
        ]);
    }

    /**
     * Деактивировать токен (прекратить срок действия токена) по токену
     *
     * @param string $token
     *
     * @return bool
     */
    public function deactivationAccessToken(string $token): bool
    {
        return (bool)AccessToken::where('token', $token)->update([
            'expiration' => Carbon::now()->subMinutes(),
        ]);
    }

    /**
     * Деактивировать токен (прекратить срок действия токена) по id пользователя
     *
     * @param int    $user_id
     * @param string $user_type
     *
     * @return bool
     */
    public function deactivationAccessTokenByUser(int $user_id, string $user_type): bool
    {
        return (bool)AccessToken::where('user_id', $user_id)
            ->where('user_type', $user_type)
            ->update([
                'expiration' => Carbon::now()->subMinutes(),
            ]);
    }

    /**
     * Продлить срок действия токена по id токена
     *
     * @param int           $token_id
     * @param DateTime|null $expiration
     *
     * @return bool
     */
    public function prolongationAccessTokenById(int $token_id, ?DateTime $expiration = null): bool
    {
        return (bool)AccessToken::where('id', $token_id)->update(['expiration' => $expiration]);
    }

    /**
     * Продлить срок действия токена
     *
     * @param string        $token
     * @param DateTime|null $expiration
     *
     * @return bool
     */
    public function prolongationAccessToken(string $token, ?DateTime $expiration = null): bool
    {
        return (bool)AccessToken::where('token', $token)->update(['expiration' => $expiration]);
    }

    /**
     * Продлить срок действия всех токенов по id пользователя
     *
     * @param int           $user_id
     * @param string        $user_type
     * @param DateTime|null $expiration
     *
     * @return bool
     */
    public function prolongationAccessTokenByUser(int $user_id, string $user_type, ?DateTime $expiration = null): bool
    {
        return (bool)AccessToken::where('user_id', $user_id)
            ->where('user_type', $user_type)
            ->update(['expiration' => $expiration]);
    }

    /**
     * Получить список токенов по ID пользователя
     *
     * @param int    $user_id
     * @param string $user_type
     *
     * @return Collection|null
     */
    public function getAccessTokenByUser(int $user_id, string $user_type): ?Collection
    {
        return AccessToken::where('user_id', $user_id)
            ->where('user_type', $user_type)
            ->orderByDesc('expiration')->get();
    }

    /**
     * Получить токен по ID
     *
     * @param int $token_id
     *
     * @return AccessToken|null
     */
    public function getAccessTokenById(int $token_id): ?AccessToken
    {
        return AccessToken::where('id', $token_id)->first();
    }

    /**
     * Получить данные о токене
     *
     * @param string $token
     *
     * @return AccessToken|null
     */
    public function getAccessToken(string $token): ?AccessToken
    {
        return AccessToken::where('token', $token)->first();
    }

    /**
     * Фиксация последней активности токена
     *
     * @param int $token_id
     *
     * @return bool
     */
    public function setLastUseAccessToken(int $token_id): bool
    {
        return (bool)AccessToken::where('id', $token_id)->update([
            'last_use' => Carbon::now()->subMinutes(),
        ]);
    }
}
