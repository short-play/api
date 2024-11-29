<?php

declare(strict_types=1);

namespace App\Resource\Api\Video;

class VideoPlayListResource extends BaseVideoResource
{

    public function __construct(protected array $collection)
    {
    }

    public function toArray(): array
    {
        return array_map(function ($item) {
            return array_merge($this->defaultResponse($item), [
                'item' => [
                    'item_id' => (string)$item['item']['id'],
                    'sort' => $item['item']['sort'],
                    'url' => $item['item']['url'],
                    'short_count' => $item['item']['short_count'],
                    'comment_count' => $item['item']['comment_count'],
                    'is_view' => $item['item']['is_view'],
                ]
            ]);
        }, $this->collection);
    }
}