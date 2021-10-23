<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Models;

use Carbon\Carbon;
use Garbuzivan\Laraveltokens\Interfaces\ModelToken;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AccessToken extends Model implements ModelToken
{
    use HasFactory;

    protected $table = 'access_tokens';

    /**
     * @var string[]
     */
    protected $fillable = [
        'token',
        'user_id',
        'user_type',
        'title',
        'expiration',
        'last_use',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'expiration',
        'last_use',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'token' => 'string',
        'user_id' => 'integer',
        'user_type' => 'string',
        'title' => 'string',
        'last_use' => 'datetime',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static array $rules = [
        'token' => 'required',
    ];

    /**
     * @return MorphTo
     */
    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Проверка валидности токена по дате
     * @return bool
     */
    public function isValid(): bool
    {
        return is_null($this->expiration) || $this->expiration > Carbon::now();
    }
}
