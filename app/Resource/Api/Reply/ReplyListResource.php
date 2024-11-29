<?php

declare(strict_types=1);

namespace App\Resource\Api\Reply;

use App\Kernel\ResourceInterface;
use Hyperf\Database\Model\Collection;

class ReplyListResource implements ResourceInterface
{

    public function __construct(protected Collection $collection)
    {
    }

    public function toArray(): array
    {
        return $this->collection->map(function ($item) {
            return [
                'reply_id' => (string)$item->id,
                'reply_user_name' => $item->replyUser->name,
                'time' => $item->created_at->toDateTimeString(),
                'like_count' => $item->like_count,
                'content' => $item->content,
                'like' => $this->isLiked($item),
                'user' => [
                    'id' => (string)$item->user->id,
                    'name' => $item->user->name,
                    'profile' => $item->user->profile
                ],
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