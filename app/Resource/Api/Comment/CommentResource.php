<?php

declare(strict_types=1);

namespace App\Resource\Api\Comment;

use App\Kernel\ResourceInterface;
use App\Model\Comment;

class CommentResource implements ResourceInterface
{

    public function __construct(protected Comment $comment)
    {
    }

    public function toArray(): array
    {
        return [
            'comment_id' => (string)$this->comment->id,
            'content' => $this->comment->content,
            'time' => $this->comment->created_at->toDateTimeString(),
            'like_count' => $this->comment->like_count ?? 0,
            'reply_count' => $this->comment->reply_count ?? 0,
        ];
    }
}