<?php

declare(strict_types=1);

namespace App\Resource\Api\Reply;

use App\Kernel\ResourceInterface;
use App\Model\CommentReplay;

class ReplyResource implements ResourceInterface
{

    public function __construct(protected CommentReplay $replay)
    {
    }

    public function toArray(): array
    {
        return [
            'reply_id' => (string)$this->replay->id,
            'time' => $this->replay->created_at->toDateTimeString(),
            'like_count' => $this->replay->like_count ?? 0,
            'content' => $this->replay->content
        ];
    }
}