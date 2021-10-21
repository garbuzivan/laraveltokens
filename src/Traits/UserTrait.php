<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Traits;

use Garbuzivan\Laraveltokens\Models\Token;
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
}
