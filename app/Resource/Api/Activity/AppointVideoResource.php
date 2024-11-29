<?php

declare(strict_types=1);

namespace App\Resource\Api\Activity;

use App\Constants\Enum\ActivityType;
use App\Kernel\ResourceInterface;
use App\Model\Activity;
use App\Model\Video;
use Hyperf\Collection\Collection;
use Hyperf\Database\Model\Model;

class AppointVideoResource implements ResourceInterface
{

    public function __construct(protected Model|Activity $activity,
                                protected Collection     $collection,
                                protected int            $watchTime,
                                protected bool           $isReceive)
    {
    }

    /**
     * @noinspection PhpDynamicAsStaticMethodCallInspection
     */
    public function toArray(): array
    {
        return [
            'id' => (string)$this->activity->id,
            'name' => $this->activity->name,
            'desc' => $this->activity->desc,
            'type' => $this->activity->type,
            'type_name' => ActivityType::getMessage($this->activity->type),
            'watch_time' => $this->watchTime,
            'is_receive' => $this->isReceive,
            'videos' => $this->collection->map(function (Video $video) {
                return [
                    'id' => (string)$video->id,
                    'title' => $video->title,
                    'image_url' => $video->image_url,
                    'type' => $video->type,
                ];
            })->toArray(),
        ];
    }
}