<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\SoftDeletes;

/**
 * @property int $id
 * @property int $message_user_id
 * @property int $like_user_id
 * @property int $comment_id
 * @property int $reply_id
 * @property string $content
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class MessageLike extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'message_likes';

    /**
     * 查询字段
     * @var array|string[]
     */
    public static array $select = [
        'id', 'message_user_id', 'like_user_id', 'comment_id', 'reply_id', 'status'
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'id', 'message_user_id', 'like_user_id', 'comment_id', 'reply_id', 'content', 'status'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'status' => 'integer',
        'reply_id' => 'integer',
        'comment_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'like_user_id' => 'integer',
        'message_user_id' => 'integer',
    ];

    /**
     * 点赞用户
     * @noinspection PhpUnused
     * @noinspection PhpUndefinedMethodInspection
     */
    public function likeUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'like_user_id')
            ->select(['id', 'name', 'profile'])
            ->withTrashed();
    }

    /**
     * 评论
     * @return BelongsTo
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class)->select(['id', 'video_id', 'item_id']);
    }
}
