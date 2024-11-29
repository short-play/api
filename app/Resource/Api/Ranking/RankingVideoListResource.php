<?php

declare(strict_types=1);

namespace App\Resource\Api\Ranking;

use App\Resource\Api\Video\BaseVideoResource;

class RankingVideoListResource extends BaseVideoResource
{
    public function __construct(protected array $collection)
    {
    }

    public function toArray(): array
    {
        return array_map(function ($item) {
            return array_merge($this->defaultResponse($item['video']), [
                'tags' => array_column($item['video']['tags'], 'value'),
            ]);
        }, $this->collection);
    }
}