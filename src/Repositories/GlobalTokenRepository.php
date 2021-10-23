<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Repositories;

use Carbon\Carbon;
use DateTime;
use Garbuzivan\Laraveltokens\Interfaces\GlobalTokenRepositoryInterface;
use Garbuzivan\Laraveltokens\Models\GlobalToken;
use Illuminate\Support\Facades\DB;

class GlobalTokenRepository implements GlobalTokenRepositoryInterface
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
    ): GlobalToken {
        return GlobalToken::create([
            'token'      => $token,
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
    public function deleteGlobalTokenById(int $token_id): bool
    {
        return (bool)GlobalToken::where('id', $token_id)->delete();
    }

    /**
     * Удалить токен
     *
     * @param string $token
     *
     * @return bool
     */
    public function deleteGlobalToken(string $token): bool
    {
        return (bool)GlobalToken::where('token', $token)->delete();
    }

    /**
     * Очистить таблицу токенов
     *
     * @return bool
     */
    public function deleteAllGlobalToken(): bool
    {
        DB::table('global_tokens')->truncate();
        return true;
    }

    /**
     * Деактивировать токен (прекратить срок действия токена) по ID токена
     *
     * @param int $token_id - ID токена
     *
     * @return bool
     */
    public function deactivationGlobalTokenById(int $token_id): bool
    {
        return (bool)GlobalToken::where('id', $token_id)->update([
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
    public function deactivationGlobalToken(string $token): bool
    {
        return (bool)GlobalToken::where('token', $token)->update([
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
    public function prolongationGlobalTokenById(int $token_id, ?DateTime $expiration = null): bool
    {
        return (bool)GlobalToken::where('id', $token_id)->update(['expiration' => $expiration]);
    }

    /**
     * Продлить срок действия токена
     *
     * @param string        $token
     * @param DateTime|null $expiration
     *
     * @return bool
     */
    public function prolongationGlobalToken(string $token, ?DateTime $expiration = null): bool
    {
        return (bool)GlobalToken::where('token', $token)->update(['expiration' => $expiration]);
    }

    /**
     * Получить токен по ID
     *
     * @param int $token_id
     *
     * @return GlobalToken|null
     */
    public function getGlobalTokenById(int $token_id): ?GlobalToken
    {
        return GlobalToken::where('id', $token_id)->first();
    }

    /**
     * Получить данные о токене
     *
     * @param string $token
     *
     * @return GlobalToken|null
     */
    public function getGlobalToken(string $token): ?GlobalToken
    {
        return GlobalToken::where('token', $token)->first();
    }

    /**
     * Фиксация последней активности токена
     *
     * @param string $token
     *
     * @return bool
     */
    public function setLastUseGlobalToken(string $token): bool
    {
        return (bool)GlobalToken::where('token', $token)->update([
            'last_use' => Carbon::now()->subMinutes(),
        ]);
    }
}
