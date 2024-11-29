<?php

declare(strict_types=1);

namespace App\Resource\Api\Message;

use App\Kernel\ResourceInterface;
use Hyperf\Collection\Collection;

class LikeListResource implements ResourceInterface
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
                'content' => $item->deleted_at ? null : $item->content,
                'comment' => $item->comment?->toArray(),
                'user' => [
                    'name' => $item->user_name,
                    'profile' => $item->user_profile,
                ],
            ];
        })->toArray();
    }
}