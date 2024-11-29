<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;

/**
 * @property int $id
 * @property string $content
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Message extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'messages';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'content'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'
    ];
}
