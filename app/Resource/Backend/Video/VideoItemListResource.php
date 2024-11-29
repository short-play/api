<?php

declare(strict_types=1);

namespace App\Resource\Backend\Video;

use App\Kernel\ResourceInterface;
use App\Model\VideoItem;
use Hyperf\Database\Model\Collection;

class VideoItemListResource implements ResourceInterface
{

    public function __construct(protected Collection $collection)
    {

    }

    public function toArray(): array
    {
        return $this->collection->map(function (VideoItem $videoItem) {
            return [
                'id' => (string)$videoItem->id,
                'sort' => $videoItem->sort,
                'url' => $videoItem->url,
                'duration' => $videoItem->duration,
                'short_count' => $videoItem->short_count,
                'comment_count' => $videoItem->comment_count,
                'is_view' => $videoItem->is_view,
                'created_at' => $videoItem->created_at->toDateTimeString(),
            ];
        })->toArray();
    }
}