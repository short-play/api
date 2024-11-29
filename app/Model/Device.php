<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;

/**
 * @property int $id
 * @property string $device
 * @property int $preference
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Device extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'devices';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'device', 'preference'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
