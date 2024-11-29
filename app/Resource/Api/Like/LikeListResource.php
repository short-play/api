<?php

declare(strict_types=1);

namespace App\Resource\Api\Like;

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
                'video_id' => (string)$item->video_id,
                'item_id' => (string)$item->item_id,
                'title' => $item->video->title,
                'time' => $item->created_at->toDateTimeString(),
                'image_url' => $item->video->image_url,
                'sort' => $item->item->sort,
                'short_count' => $item->item->short_count,
            ];
        })->toArray();
    }
}