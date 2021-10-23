<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Models;

use Carbon\Carbon;
use Garbuzivan\Laraveltokens\Interfaces\ModelToken;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RefreshToken extends Model implements ModelToken
{
    use HasFactory;

    protected $table = 'refresh_tokens';

    /**
     * @var string[]
     */
    protected $fillable = [
        'token',
        'user_id',
        'user_type',
        'access_token_id',
        'expiration',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'expiration',
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
        'access_token_id' => 'integer',
        'expiration' => 'datetime',
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
     * @return BelongsTo
     */
    public function accessToken(): BelongsTo
    {
        return $this->belongsTo(AccessToken::class, 'access_token_id', 'id');
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
