<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Relations\HasOne;

/**
 * @property int $id
 * @property int $video_id
 * @property int $item_id
 * @property int $user_id
 * @property string $content
 * @property int $reply_count
 * @property int $like_count
 * @property int $interaction_count
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Comment extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'comments';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'id', 'video_id', 'item_id', 'user_id', 'content',
        'reply_count', 'like_count', 'interaction_count'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'video_id' => 'integer',
        'item_id' => 'integer',
        'user_id' => 'integer',
        'like_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'reply_count' => 'integer',
        'interaction_count' => 'integer',
    ];

    /**
     * 点赞
     * @return HasOne
     */
    public function like(): HasOne
    {
        return $this->hasOne(CommentLike::class)->select(['id', 'comment_id', 'type']);
    }

    /**
     * 用户
     * @return BelongsTo
     * @noinspection PhpUndefinedMethodInspection
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed()->select(['id', 'name', 'profile']);
    }
}
