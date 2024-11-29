<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\Enum\MessageStatus;
use App\Constants\ErrorCode;
use App\Constants\RedisKey;
use App\Exception\ShortPlayException;
use App\Model\Message;
use App\Model\MessageCount;
use App\Model\MessageLike;
use App\Model\MessageReplay;
use App\Model\MessageUser;
use App\Model\User;
use Hyperf\Collection\Collection;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Model;
use Hyperf\DbConnection\Db;
use function FriendsOfHyperf\Lock\lock;

class MessageService
{

    /**
     * 获取后台管理系统通知消息列表
     * @param int $pageSize
     * @return LengthAwarePaginatorInterface
     */
    public function adminMessageList(int $pageSize): LengthAwarePaginatorInterface
    {
        return Message::latest('updated_at')->paginate($pageSize);
    }

    /**
     * 添加通知消息
     * @param string $text
     * @return void
     */
    public function createNoticeMessage(string $text): void
    {
        Message::create(['content' => $text]);
    }

    /**
     * 修改通知消息内容
     * @param int $id
     * @param string $text
     * @return void
     */
    public function updateNoticeMessage(int $id, string $text): void
    {
        Message::where(compact('id'))->update(['content' => $text]);
    }

    /**
     * 删除通知消息内容
     * @param int $id
     * @return void
     */
    public function deleteNoticeMessage(int $id): void
    {
        $messageUser = MessageUser::where('message_id', $id)->first(['id']);
        if ($messageUser) {
            throw new ShortPlayException(ErrorCode::MESSAGE_SEND_USER->value);
        }
        Message::where(compact('id'))->delete();
    }

    /**
     * 批量发送通知消息给用户
     * @param int $id
     * @param array $userIds
     * @return void
     */
    public function sendNoticeMessage(int $id, array $userIds): void
    {
        $message = $this->validateMessageAndUsers($id, $userIds);
        $key = RedisKey::MESSAGE_SEND_LOCK . $id;
        $lockStatus = lock($key, 10)->get(function () use ($message, $userIds) {
            $pendingUserIds = $this->getPendingUserIds($message->id, $userIds);
            // 如果没有需要发送的用户，抛出异常
            if (empty($pendingUserIds)) {
                throw new ShortPlayException(ErrorCode::MESSAGE_SEND_USER->value);
            }
            Db::transaction(function () use ($message, $pendingUserIds) {
                MessageUser::batchSendMessageUser($message->id, $pendingUserIds);
                MessageCount::whereIn('user_id', $pendingUserIds)->increment('notice_unread_count');
            });
        });
        if ($lockStatus === false) {
            throw new ShortPlayException(ErrorCode::LOCKING->value);
        }
    }

    /**
     * 获取未读总数
     * @param int $userId
     * @return array
     */
    public function getUnreadMessageCount(int $userId): array
    {
        $m = MessageCount::where('user_id', $userId)
            ->select(MessageCount::$select)
            ->firstOrFail();
        $total = $m['reply_unread_count'] + $m['like_unread_count'] + $m['notice_unread_count'];
        return array_merge($m->toArray(), compact('total'));
    }

    /**
     * 获取消息回复列表
     * @param int $userId
     * @param int $skip
     * @param int $take
     * @return Collection
     */
    public function getReplyMessageList(int $userId, int $skip, int $take): Collection
    {
        return MessageReplay::withTrashed()->where('message_user_id', $userId)
            ->with(['repliedUser', 'replyUser'])
            ->orderByDesc('created_at')
            ->with('comment')
            ->skip($skip)
            ->take($take)
            ->get();
    }

    /**
     * 获取点赞消息列表
     * @param int $userId
     * @param int $skip
     * @param int $take
     * @return Collection
     */
    public function getLikeMessageList(int $userId, int $skip, int $take): Collection
    {
        $selectField = [
            'message_likes.*',
            Db::raw('GROUP_CONCAT(users.name) as user_name'),
            Db::raw('GROUP_CONCAT(users.profile) as user_profile'),
        ];
        return MessageLike::withTrashed()
            ->join('users', 'users.id', '=', 'message_likes.like_user_id')
            ->where('message_user_id', $userId)
            ->orderByDesc('created_at')
            ->select($selectField)
            ->groupBy('reply_id')
            ->with('comment')
            ->skip($skip)
            ->take($take)
            ->get();
    }

    /**
     * 获取通知消息列表
     * @param int $userId
     * @param int $skip
     * @param int $take
     * @return Collection
     */
    public function getNoticeMessageList(int $userId, int $skip, int $take): Collection
    {
        return MessageUser::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->with('message')
            ->skip($skip)
            ->take($take)
            ->get();
    }

    /**
     * 删除回复消息
     * @param int $userId
     * @param int $messageId
     * @return void
     */
    public function delReplyMessage(int $userId, int $messageId): void
    {
        MessageReplay::where('id', $messageId)
            ->where('status', MessageStatus::Read)
            ->where('message_user_id', $userId)
            ->forceDelete();
    }

    /**
     * 删除点赞消息
     * @param int $userId
     * @param int $messageId
     * @return void
     */
    public function delLikeMessage(int $userId, int $messageId): void
    {
        MessageLike::where('id', $messageId)
            ->where('status', MessageStatus::Read)
            ->where('message_user_id', $userId)
            ->forceDelete();
    }

    /**
     * 删除通知消息
     * @param int $userId
     * @param int $messageId
     * @return void
     */
    public function delNoticeMessage(int $userId, int $messageId): void
    {
        MessageUser::getQuery()->where('id', $messageId)
            ->where('status', MessageStatus::Read)
            ->where('user_id', $userId)
            ->delete();
    }

    /**
     * 已读回复消息
     * @param int $userId
     * @return void
     */
    public function readReplyMessage(int $userId): void
    {
        Db::transaction(function () use ($userId) {
            // 更新消息状态
            MessageReplay::withTrashed()->where('message_user_id', $userId)
                ->where('status', MessageStatus::Unread)
                ->update(['status' => MessageStatus::Read]);
            $this->updateMessageCount($userId, ['reply_unread_count' => 0]);
        });
    }

    /**
     * 已读点赞消息
     * @param int $userId
     * @return void
     */
    public function readLikeMessage(int $userId): void
    {
        Db::transaction(function () use ($userId) {
            // 更新消息状态
            MessageLike::withTrashed()->where('message_user_id', $userId)
                ->where('status', MessageStatus::Unread)
                ->update(['status' => MessageStatus::Read]);
            $this->updateMessageCount($userId, ['like_unread_count' => 0]);
        });
    }

    /**
     * 已读通知消息
     * @param int $userId
     * @return void
     */
    public function readNoticeMessage(int $userId): void
    {
        Db::transaction(function () use ($userId) {
            // 更新消息状态
            MessageUser::getQuery()->where('user_id', $userId)
                ->where('status', MessageStatus::Unread)
                ->update(['status' => MessageStatus::Read]);
            $this->updateMessageCount($userId, ['notice_unread_count' => 0]);
        });
    }

    /**
     * 修改消息总数
     * @param int $userId
     * @param array $updateData
     * @return void
     */
    public function updateMessageCount(int $userId, array $updateData): void
    {
        MessageCount::getQuery()->where('user_id', $userId)->update($updateData);
    }

    /**
     * 获取回复消息详情
     * @param int $userId
     * @param int $id
     * @param array $columns
     * @return Model
     */
    public function replyMessageDetail(int $userId, int $id, array $columns = []): Model
    {
        $columns = empty($columns) ? MessageReplay::$select : $columns;
        return MessageReplay::where('id', $id)
            ->where('message_user_id', $userId)
            ->firstOrFail($columns);
    }

    /**
     * 获取点赞消息详情
     * @param int $userId
     * @param int $id
     * @param array $columns
     * @return Model
     */
    public function likeMessageDetail(int $userId, int $id, array $columns = []): Model
    {
        $columns = empty($columns) ? MessageLike::$select : $columns;
        return MessageLike::where('id', $id)
            ->where('message_user_id', $userId)
            ->firstOrFail($columns);
    }

    /**
     * 校验消息和用户是否存在
     * @param int $messageId
     * @param array $userIds
     * @return Message
     */
    private function validateMessageAndUsers(int $messageId, array $userIds): Message
    {
        $message = Message::findOrFail($messageId, ['id']);
        $usersCount = User::whereIn('id', $userIds)->count();
        if ($usersCount !== count($userIds)) {
            throw new ShortPlayException(ErrorCode::DATA_INVALID->value);
        }
        return $message;
    }

    /**
     * 获取需要发送消息的用户ID列表
     * @param int $messageId
     * @param array $userIds
     * @return array
     */
    private function getPendingUserIds(int $messageId, array $userIds): array
    {
        $sentUserIds = MessageUser::whereIn('user_id', $userIds)
            ->where('message_id', $messageId)
            ->pluck('user_id')
            ->toArray();
        return array_diff($userIds, $sentUserIds);
    }
}