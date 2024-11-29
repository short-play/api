<?php

declare(strict_types=1);

namespace App\Resource\Api\Video;

class MovieListResource extends BaseVideoResource
{

    public function __construct(protected array $collection)
    {
    }

    public function toArray(): array
    {
        return array_map(function ($item) {
            return array_merge($this->defaultResponse($item), [
                'tags' => array_column($item['tags'], 'value'),
                'actors' => array_column($item['actor'], 'value'),
            ]);
        }, $this->collection);
    }
}