<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;

/**
 * @property int $id
 * @property int $unique_id
 * @property string $value
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class SearchHistory extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'search_histories';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'unique_id', 'value'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer', 'unique_id' => 'integer',
        'created_at' => 'datetime', 'updated_at' => 'datetime'
    ];
}
