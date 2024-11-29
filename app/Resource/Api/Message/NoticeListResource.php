<?php

declare(strict_types=1);

namespace App\Resource\Api\Message;

use App\Kernel\ResourceInterface;
use Hyperf\Collection\Collection;

class NoticeListResource implements ResourceInterface
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
                'content' => $item->message?->content ?? '',
            ];
        })->toArray();
    }
}