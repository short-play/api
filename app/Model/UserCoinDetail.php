<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $activity_id
 * @property int $activity_type
 * @property int $coin
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class UserCoinDetail extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_coin_detail';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'coin', 'user_id', 'activity_id', 'activity_type'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'coin' => 'integer',
        'user_id' => 'integer',
        'activity_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'activity_type' => 'integer',
    ];
}
