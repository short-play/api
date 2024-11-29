<?php

declare(strict_types=1);

namespace App\Resource\Api\Message;

use App\Kernel\ResourceInterface;
use App\Model\MessageReplay;
use Hyperf\Collection\Collection;
use Hyperf\Database\Model\Model;

class MessageReplyResource implements ResourceInterface
{

    public function __construct(protected Model $msg, protected Model $comment, protected ?Collection $reply)
    {
    }

    /**
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function toArray(): array
    {
        $data = [
            'id' => (string)$this->msg->id,
            'comment_id' => (string)$this->msg->comment_id,
            'reply_id' => (string)$this->msg->reply_id,
            'status' => $this->msg->status,
            'comment' => $this->handleComment(),
            'replies' => $this->handleReply(),
        ];
        if ($this->msg instanceof MessageReplay) {
            $data['replied_id'] = (string)$this->msg->replied_id;
        }
        return $data;
    }

    /**
     * 处理评论
     * @return array
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function handleComment(): array
    {
        return [
            'id' => (string)$this->comment->id,
            'content' => $this->comment->content,
            'time' => $this->comment->created_at->toDateTimeString(),
            'like_count' => $this->comment->like_count,
            'reply_count' => $this->comment->reply_count,
            'like' => $this->isLiked($this->comment),
            'user' => empty($this->comment->user) ? null : [
                'name' => $this->comment->user->name,
                'profile' => $this->comment->user->profile
            ],
        ];
    }

    /**
     * 处理回复消息
     * @return array
     */
    protected function handleReply(): array
    {
        if (empty($this->reply)) {
            return [];
        }
        return $this->reply->map(function ($item) {
            return [
                'id' => (string)$item->id,
                'reply_user_name' => $item->replyUser->name,
                'time' => $item->created_at->toDateTimeString(),
                'like_count' => $item->like_count,
                'comment_id' => (string)$item->comment_id,
                'parent_id' => (string)$item->parent_id,
                'content' => $item->content,
                'like' => $this->isLiked($item),
                'user' => [
                    'name' => $item->user->name,
                    'profile' => $item->user->profile
                ]
            ];
        })->toArray();
    }

    protected function isLiked($item): ?array
    {
        if (!$item->relationLoaded('like') || empty($item->like)) {
            return null;
        }
        return [
            'id' => (string)$item->like->id,
            'type' => $item->like->type,
        ];
    }
}