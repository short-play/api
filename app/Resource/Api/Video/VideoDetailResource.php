<?php

declare(strict_types=1);

namespace App\Resource\Api\Video;

class VideoDetailResource extends BaseVideoResource
{

    public function __construct(protected array $video)
    {
    }

    public function toArray(): array
    {
        return array_merge($this->defaultResponse($this->video), [
            'tags' => array_column($this->video['tags'], 'value'),
            'items' => $this->items(),
        ]);
    }

    public function items(): array
    {
        return array_map(function ($item) {
            return [
                'item_id' => (string)$item['id'],
                'sort' => $item['sort'],
                'short_count' => $item['short_count'],
                'comment_count' => $item['comment_count'],
                'is_view' => $item['is_view'],
            ];
        }, $this->video['items']);
    }
}