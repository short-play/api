<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Relations\HasOne;

/**
 * @property int $id
 * @property int $user_id
 * @property int $comment_id
 * @property int $parent_id
 * @property int $reply_user_id
 * @property string $content
 * @property int $like_count
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class CommentReplay extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'comment_replays';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'id', 'user_id', 'comment_id', 'parent_id', 'content', 'like_count', 'reply_user_id'
    ];

    /**
     * 查询字段
     * @var array|string[]
     */
    public static array $select = [
        'id', 'user_id', 'created_at', 'reply_user_id', 'content', 'like_count'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'parent_id' => 'integer',
        'comment_id' => 'integer',
        'like_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'reply_user_id' => 'integer',
    ];

    /**
     * 评论
     * @return BelongsTo
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
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

    /**
     * 回复者
     * @noinspection PhpUnused
     */
    public function replyUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reply_user_id')->select(['id', 'name']);
    }

    /**
     * 点赞
     * @return HasOne
     */
    public function like(): HasOne
    {
        return $this->hasOne(CommentLike::class, 'cr_id')->select(['id', 'cr_id', 'type']);
    }
}
