<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Token extends Model
{
    use HasFactory;

    protected $table = 'tokens';

    /**
     * @var string[]
     */
    protected $fillable = [
        'token',
        'user_id',
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
        'user_id' => 'integer',
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
     * Relation to wallet
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Проверка валидности токена по дате
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->expiration > Carbon::now();
    }
}
