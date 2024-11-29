<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $device_id
 * @property Carbon $merge_time
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class UserDevice extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_devices';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'device_id', 'merge_time'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'device_id' => 'integer',
        'merge_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
