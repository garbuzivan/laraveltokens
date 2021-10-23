<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Models;

use Carbon\Carbon;
use Garbuzivan\Laraveltokens\Interfaces\ModelToken;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalToken extends Model implements ModelToken
{
    use HasFactory;

    protected $table = 'global_tokens';

    /**
     * @var string[]
     */
    protected $fillable = [
        'token',
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
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'token' => 'string',
        'title' => 'string',
        'expiration' => 'datetime',
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
     * Проверка валидности токена по дате
     * @return bool
     */
    public function isValid(): bool
    {
        return is_null($this->expiration) || $this->expiration > Carbon::now();
    }
}
