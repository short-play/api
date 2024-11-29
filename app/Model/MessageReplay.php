<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\SoftDeletes;

/**
 * @property int $id
 * @property int $message_user_id
 * @property int $replied_user_id
 * @property int $reply_user_id
 * @property int $comment_id
 * @property int $reply_id
 * @property int $replied_id
 * @property string $replied_content
 * @property string $reply_content
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class MessageReplay extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'message_replays';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'id', 'message_user_id', 'replied_user_id', 'reply_user_id', 'comment_id',
        'reply_id', 'replied_id', 'replied_content', 'reply_content', 'status',
    ];

    /**
     * 查询字段
     * @var array|string[]
     */
    public static array $select = [
        'id', 'message_user_id', 'replied_user_id', 'reply_user_id', 'comment_id',
        'reply_id', 'replied_id', 'status'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'status' => 'integer',
        'reply_id' => 'integer',
        'comment_id' => 'integer',
        'replied_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'reply_user_id' => 'integer',
        'message_user_id' => 'integer',
        'replied_user_id' => 'integer',
    ];

    /**
     * 评论
     * @return BelongsTo
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class)->select(['id', 'video_id', 'item_id']);
    }

    /**
     * 被回复用户
     * @return BelongsTo
     * @noinspection PhpUnused
     * @noinspection PhpUndefinedMethodInspection
     */
    public function repliedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_user_id')
            ->select(['id', 'name'])
            ->withTrashed();
    }

    /**
     * 回复用户
     * @return BelongsTo
     * @noinspection PhpUnused
     * @noinspection PhpUndefinedMethodInspection
     */
    public function replyUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reply_user_id')
            ->select(['id', 'name', 'profile'])
            ->withTrashed();
    }

    /**
     * 在外面记得开（事务）
     * @param int $commentUid
     * @param array $replyMessage
     * [
     *   'message_user_id' => $replyUser->id,
     *   'replied_user_id' => $replyUser->id,
     *   'reply_user_id' => $uid,
     *   'comment_id' => $comment->id,
     *   'reply_id' => $commentReplay->id,
     *   'replied_id' => $replyId,
     *   'replied_content' => $repliedContent,
     *   'reply_content' => $content
     * * ]
     * @return void
     */
    public static function insertMessageReplay(int $commentUid, array $replyMessage): void
    {
        $userCount = [$replyMessage['message_user_id']];
        if ($replyMessage['replied_id'] == $replyMessage['comment_id']) {
            MessageReplay::create($replyMessage);
        } else {
            $userCount[] = $commentUid;
            $nowDateTime = Carbon::now();
            $replyMessage['id'] = snowflakeId();
            $replyMessage['created_at'] = $nowDateTime;
            $replyMessage['updated_at'] = $nowDateTime;
            // 这块处理是因为一级评论下回复回复的评论需要给一级评论的者发送消息
            $message = array_merge($replyMessage, ['id' => snowflakeId(), 'message_user_id' => $commentUid]);
            self::insert([$message, $replyMessage]);
        }
        // 消息总数+1
        MessageCount::whereIn('user_id', $userCount)->increment('reply_unread_count');
    }
}
