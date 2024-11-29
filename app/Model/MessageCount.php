<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $reply_unread_count
 * @property int $like_unread_count
 * @property int $notice_unread_count
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class MessageCount extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'message_counts';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'id', 'user_id', 'reply_unread_count', 'like_unread_count', 'notice_unread_count'
    ];

    /**
     * 查询字段
     * @var array|string[]
     */
    public static array $select = [
        'id', 'reply_unread_count', 'like_unread_count', 'notice_unread_count'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'like_unread_count' => 'integer',
        'reply_unread_count' => 'integer',
        'notice_unread_count' => 'integer',
    ];
}
