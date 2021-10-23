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
        return $this->morphMany(AccessToken::class, 'userable');
    }

    /**
     * @return MorphMany
     */
    public function refreshTokens(): MorphMany
    {
        return $this->morphMany(RefreshToken::class, 'userable');
    }

    /**
     * Создать новый токен пользователя
     * @param string $title
     * @param DateTime|null $expiration
     * @return AccessToken
     * @throws UserNotExistsException
     */
    public function accessTokenCreate(string $title = '', ?DateTime $expiration = null): AccessToken
    {
        return app(TokenManager::class)->createAccessToken($title, $expiration, $this->id);
    }

    /**
     * Удалить все токены пользователя
     * @return bool
     */
    public function accessTokensDelete(): bool
    {
        return app(TokenManager::class)->deleteByUserAccessToken($this->id);
    }
}
