<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;

/**
 * @property int $id
 * @property string $type
 * @property string $language
 * @property string $value
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Agreement extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'agreements';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'type', 'language', 'value'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
