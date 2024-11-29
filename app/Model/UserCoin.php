<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $coin_num
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class UserCoin extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_coins';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'coin_num'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer', 'user_id' => 'integer', 'coin_num' => 'integer',
        'created_at' => 'datetime', 'updated_at' => 'datetime'
    ];
}
