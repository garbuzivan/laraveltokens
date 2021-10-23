<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Traits;

use DateTime;
use Garbuzivan\Laraveltokens\Exceptions\UserNotExistsException;
use Garbuzivan\Laraveltokens\Models\AccessToken;
use Garbuzivan\Laraveltokens\Models\RefreshToken;
use Garbuzivan\Laraveltokens\TokenManager;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait UserTrait
{
    /**
     * @return MorphMany
     */
    public function accessTokens(): MorphMany
    {
        return $this->morphMany(AccessToken::class, 'user');
    }

    /**
     * @return MorphMany
     */
    public function refreshTokens(): MorphMany
    {
        return $this->/** @scrutinizer ignore-call */ morphMany(RefreshToken::class, 'user');
    }

    /**
     * Создать новый токен пользователя
     *
     * @param string        $title
     * @param DateTime|null $expiration
     *
     * @return AccessToken
     */
    public function accessTokenCreate(string $title = '', ?DateTime $expiration = null): AccessToken
    {
        return app(TokenManager::class)->createAccessToken($title, $expiration, $this->id, static::class);
    }

    /**
     * Удалить все токены пользователя
     *
     * @return bool
     */
    public function accessTokensDelete(): bool
    {
        return app(TokenManager::class)->deleteAccessTokenByUser($this->id, static::class);
    }
}
