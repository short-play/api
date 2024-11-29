<?php

declare(strict_types=1);

namespace App\Resource\Api\View;

use App\Constants\Enum\VideoType;
use App\Kernel\ResourceInterface;
use Hyperf\Collection\Collection;

class ViewListResource implements ResourceInterface
{

    public function __construct(protected Collection $collection)
    {
    }

    /**
     * @noinspection PhpDynamicAsStaticMethodCallInspection
     */
    public function toArray(): array
    {
        return $this->collection->map(function ($item) {
            return [
                'view_id' => (string)$item->id,
                'video_id' => (string)$item->video_id,
                'item_id' => (string)$item->item_id,
                'time' => $item->updated_at->toDateTimeString(),
                'play_duration' => $item->play_duration,
                'duration' => $item->duration,
                'watch_num' => $item->num,
                'title' => $item->video->title,
                'image_url' => $item->video->image_url,
                'num' => $item->video->num,
                'type' => $item->video->type,
                'is_finish' => $item->video->is_finish,
                'type_name' => VideoType::getMessage($item->video->type),
            ];
        })->toArray();
    }
}