<?php

declare(strict_types=1);

namespace App\Resource\Api\Follow;

use App\Constants\Enum\VideoType;
use App\Kernel\ResourceInterface;
use Hyperf\Database\Model\Collection;

class FollowListResource implements ResourceInterface
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
                'follow_id' => (string)$item->id,
                'video_id' => (string)$item->video_id,
                'time' => $item->created_at->toDateTimeString(),
                'title' => $item->video->title,
                'image_url' => $item->video->image_url,
                'num' => $item->video->num,
                'type' => $item->video->type,
                'is_finish' => $item->video->is_finish,
                'type_name' => VideoType::getMessage($item->video->type),
                'view' => empty($item->view) ? null : [
                    'play_duration' => $item->view->play_duration,
                    'duration' => $item->view->duration,
                    'watch_num' => $item->view->num,
                ],
            ];
        })->toArray();
    }
}