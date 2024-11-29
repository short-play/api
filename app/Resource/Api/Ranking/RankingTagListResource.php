<?php

declare(strict_types=1);

namespace App\Resource\Api\Ranking;

use App\Kernel\ResourceInterface;

class RankingTagListResource implements ResourceInterface
{

    public function __construct(protected array $collection)
    {
    }

    public function toArray(): array
    {
        return array_map(function ($item) {
            return [
                'id' => (string)$item['tag']['id'],
                'value' => $item['tag']['value'],
                'search_count' => $item['tag']['search_count'],
                'sort' => $item['tag']['sort'],
            ];
        }, $this->collection);
    }
}