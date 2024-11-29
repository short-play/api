<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;

/**
 * @property int $id
 * @property string $value
 * @property int $search_count
 * @property int $sort
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Tag extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'tags';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'value', 'search_count', 'sort'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'sort' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'search_count' => 'integer',
    ];
}
