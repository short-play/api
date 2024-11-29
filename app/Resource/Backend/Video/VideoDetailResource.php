<?php

declare(strict_types=1);

namespace App\Resource\Backend\Video;

use App\Constants\Enum\VideoFinish;
use App\Kernel\ResourceInterface;
use App\Model\Video;

class VideoDetailResource implements ResourceInterface
{

    public function __construct(protected Video $video)
    {
    }

    /**
     * @noinspection PhpDynamicAsStaticMethodCallInspection
     * @noinspection PhpUndefinedFieldInspection
     * @noinspection PhpParamsInspection
     */
    public function toArray(): array
    {
        return [
            'id' => (string)$this->video->id,
            'title' => $this->video->title,
            'image_url' => $this->video->image_url,
            'type' => $this->video->type,
            'tag_type' => $this->video->tag_type,
            'desc' => $this->video->desc,
            'num' => $this->video->num,
            'play_count' => $this->video->play_count,
            'collect_count' => $this->video->collect_count,
            'search_count' => $this->video->search_count,
            'is_finish' => $this->video->is_finish,
            'is_finish_name' => VideoFinish::getMessage($this->video->is_finish),
            'rating' => $this->video->rating,
            'created_at' => $this->video->created_at->toDateTimeString(),
            'tags' => $this->video->tags->pluck('value')->toArray(),
            'actors' => $this->video->actor->pluck('value')->toArray(),
            'items' => $this->videoItem()
        ];
    }

    /**
     * 获取item
     * @return array
     * @noinspection PhpUndefinedFieldInspection
     */
    protected function videoItem(): array
    {
        if (!$this->video->relationLoaded('items')) {
            return [];
        }
        return (new VideoItemListResource($this->video->items))->toArray();
    }
}