<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Traits;

use DateTime;
use Garbuzivan\Laraveltokens\Exceptions\UserNotExistsException;
use Garbuzivan\Laraveltokens\Models\Token;
use Garbuzivan\Laraveltokens\TokenManager;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait UserTrait
{
    /**
     * @return HasMany
     */
    public function tokens()
    {
        return $this->/** @scrutinizer ignore-call */ hasMany(Token::class);
    }

    /**
     * Создать новый токен пользователя
     * @param string $title
     * @param DateTime|null $expiration
     * @return Token
     * @throws UserNotExistsException
     */
    public function tokenCreate(string $title = '', ?DateTime $expiration = null): Token
    {
        return app(TokenManager::class)->create($title, $expiration, $this->id);
    }

    /**
     * Удалить все токены пользователя
     * @return bool
     */
    public function tokensDelete(): bool
    {
        return app(TokenManager::class)->deleteByUser($this->id);
    }
}
