<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $desc
 * @property int $type
 * @property int $status
 * @property array $config
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Activity extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'activities';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'name', 'desc', 'type', 'config', 'status'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'config' => 'json',
        'type' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
