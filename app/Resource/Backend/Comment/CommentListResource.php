<?php

declare(strict_types=1);

namespace App\Resource\Backend\Comment;

use App\Kernel\AbstractAdminResource;
use Hyperf\Contract\LengthAwarePaginatorInterface;

class CommentListResource extends AbstractAdminResource
{

    public function __construct(protected LengthAwarePaginatorInterface $paginator)
    {
    }

    public function getResources(): array
    {
        return array_map(function ($item) {
            return [
                'id' => (string)$item->id,
                'content' => $item->content,
                'created_at' => $item->created_at->toDateTimeString(),
                'like_count' => $item->like_count,
                'reply_count' => $item->reply_count,
                'user' => empty($item->user) ? null : [
                    'name' => $item->user->name,
                    'profile' => $item->user->profile
                ],
            ];
        }, $this->paginator->items());
    }
}