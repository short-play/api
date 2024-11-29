<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;
use function Hyperf\Support\now;

/**
 * @property int $id
 * @property int $message_id
 * @property int $user_id
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class MessageUser extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'message_users';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'message_id', 'user_id', 'status'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'status' => 'integer',
        'user_id' => 'integer',
        'message_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 消息内容
     * @return BelongsTo
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class)->select(['id', 'content']);
    }

    /**
     * 批量发送消息给用户
     * @param int $messageId
     * @param array $userIds
     * @return void
     */
    public static function batchSendMessageUser(int $messageId, array $userIds): void
    {
        $batchSendUsers = array_map(function ($userId) use ($messageId) {
            return [
                'id' => snowflakeId(),
                'message_id' => $messageId,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }, $userIds);
        self::insert($batchSendUsers);
    }
}
