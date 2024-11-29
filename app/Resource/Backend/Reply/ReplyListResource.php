<?php

declare(strict_types=1);

namespace App\Resource\Backend\Reply;

use App\Kernel\AbstractAdminResource;
use Hyperf\Contract\LengthAwarePaginatorInterface;

class ReplyListResource extends AbstractAdminResource
{

    public function __construct(protected LengthAwarePaginatorInterface $paginator)
    {
    }

    public function getResources(): array
    {
        return array_map(function ($item) {
            return [
                'id' => (string)$item->id,
                'reply_user_name' => $item->replyUser->name,
                'time' => $item->created_at->toDateTimeString(),
                'like_count' => $item->like_count,
                'content' => $item->content,
                'user' => [
                    'name' => $item->user->name,
                    'profile' => $item->user->profile
                ],
            ];
        }, $this->paginator->items());
    }
}