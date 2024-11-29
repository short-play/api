<?php

declare(strict_types=1);

namespace App\Resource\Api\Video;

class TvListResource extends BaseVideoResource
{

    public function __construct(protected array $collection, protected array $followed)
    {
    }

    public function toArray(): array
    {
        return array_map(function ($item) {
            return array_merge($this->defaultResponse($item), [
                'is_followed' => in_array($item['id'], $this->followed),
                'tags' => array_column($item['tags'], 'value'),
                'actors' => array_column($item['actor'], 'value'),
            ]);
        }, $this->collection);
    }
}