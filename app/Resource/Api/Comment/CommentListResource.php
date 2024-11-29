<?php

declare(strict_types=1);

namespace App\Resource\Api\Comment;

use App\Kernel\ResourceInterface;
use Hyperf\Database\Model\Collection;

class CommentListResource implements ResourceInterface
{

    public function __construct(protected Collection $collection)
    {
    }

    public function toArray(): array
    {
        return $this->collection->map(function ($item) {
            return [
                'comment_id' => (string)$item->id,
                'content' => $item->content,
                'time' => $item->created_at->toDateTimeString(),
                'like_count' => $item->like_count,
                'reply_count' => $item->reply_count,
                'like' => $this->isLiked($item),
                'user' => empty($item->user) ? null : [
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