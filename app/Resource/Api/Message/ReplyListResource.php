<?php

declare(strict_types=1);

namespace App\Resource\Api\Message;

use App\Kernel\ResourceInterface;
use Hyperf\Collection\Collection;

class ReplyListResource implements ResourceInterface
{

    public function __construct(protected Collection $collection)
    {
    }

    public function toArray(): array
    {
        return $this->collection->map(function ($item) {
            return [
                'id' => (string)$item->id,
                'status' => $item->status,
                'time' => $item->created_at->toDateTimeString(),
                'replied_content' => $item->deleted_at ? null : $item->replied_content,
                'reply_content' => $item->reply_content,
                'comment' => $item->comment?->toArray(),
                'replied_user' => [
                    'name' => $item->repliedUser->name,
                ],
                'reply_user' => [
                    'name' => $item->replyUser->name,
                    'profile' => $item->replyUser->profile,
                ]
            ];
        })->toArray();
    }
}