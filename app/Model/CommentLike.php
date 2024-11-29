<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $cr_id
 * @property int $comment_id
 * @property string $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class CommentLike extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'comment_likes';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'cr_id', 'comment_id', 'type'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'cr_id' => 'integer',
        'user_id' => 'integer',
        'comment_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
