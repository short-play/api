<?php

declare(strict_types=1);

namespace App\Resource\Api\Message;

use App\Kernel\ResourceInterface;

class UnReadCountResource implements ResourceInterface
{

    public function __construct(protected array $collection)
    {
    }

    public function toArray(): array
    {
        return [
            'total' => $this->collection['total'],
            'reply_unread_count' => $this->collection['reply_unread_count'],
            'like_unread_count' => $this->collection['like_unread_count'],
            'notice_unread_count' => $this->collection['notice_unread_count'],
        ];
    }
}