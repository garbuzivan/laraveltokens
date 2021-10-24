<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens;

use DateTime;
use Garbuzivan\Laraveltokens\Interfaces\ModelToken;
use Garbuzivan\Laraveltokens\Models\AccessToken;
use Garbuzivan\Laraveltokens\Models\GlobalToken;
use Illuminate\Contracts\Auth\Authenticatable;

class Token
{
    /**
     * @var string|null
     */
    public ?string $type = null;

    /**
     * @var int
     */
    public int $id = 0;

    /**
     * @var string
     */
    public string $token;

    /**
     * @var int
     */
    public int $user_id;

    /**
     * @var string|null
     */
    public ?string $user_type = null;

    /**
     * @var string|null
     */
    public ?string $title = null;

    /**
     * @var DateTime|null
     */
    public ?DateTime $expiration = null;

    /**
     * @var DateTime|null
     */
    public ?DateTime $last_use = null;

    /**
     * @var bool
     */
    public bool $is_valid = false;

    /**
     * @var Authenticatable|null
     */
    public ?Authenticatable $user = null;

    /**
     * @var array
     */
    public array $header = [];

    /**
     * @param ModelToken|null $modelToken
     *
     * @return $this
     */
    public function load(?ModelToken $modelToken = null): self
    {
        if (is_null($modelToken)) {
            return $this;
        } elseif ($modelToken instanceof AccessToken) {
            return $this->loadAccessToken($modelToken);
        } elseif ($modelToken instanceof GlobalToken) {
            return $this->loadGlobalToken($modelToken);
        }
        return $this;
    }

    /**
     * Информация полученная из токена
     *
     * @param array $header
     *
     * @return $this
     */
    public function loadTokenHeader(array $header): self
    {
        $this->header = $header;
        return $this;
    }

    /**
     * @param AccessToken $token
     *
     * @return $this
     */
    public function loadAccessToken(AccessToken $token): self
    {
        $this->type = AccessToken::class;
        $this->loadDefault($token);
        $this->user_id = $token->user_id ?? $this->user_id;
        $this->user_type = $token->user_type ?? $this->user_type;
        $this->user = $token->user ?? $this->user;
        $this->is_valid = $token->isValid();
        return $this;
    }

    /**
     * @param GlobalToken $token
     *
     * @return $this
     */
    public function loadGlobalToken(GlobalToken $token): self
    {
        $this->type = GlobalToken::class;
        $this->loadDefault($token);
        return $this;
    }

    /**
     * @param GlobalToken $token
     *
     * @return void
     */
    public function loadDefault(ModelToken $token): void
    {
        $this->id = $token->id ?? $this->id;
        $this->token = $token->token ?? $this->token;
        $this->title = $token->title ?? $this->title;
        $this->expiration = $token->expiration ?? $this->expiration;
        $this->last_use = $token->last_use ?? $this->last_use;
        $this->is_valid = $token->isValid();
    }

    /**
     * Загружен ли токен
     *
     * @return bool
     */
    public function isToken(): bool
    {
        return !is_null($this->type);
    }

    /**
     * Проверка валидности токена по дате
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isToken() && $this->is_valid;
    }
}
