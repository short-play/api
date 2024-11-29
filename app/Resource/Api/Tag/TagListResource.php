<?php

declare(strict_types=1);

namespace App\Resource\Api\Tag;

use App\Kernel\ResourceInterface;

class TagListResource implements ResourceInterface
{

    public function __construct(protected array $collection)
    {
    }

    public function toArray(): array
    {
        return array_map(function ($item) {
            return [
                'id' => (string)$item['id'],
                'value' => $item['value'],
                'search_count' => $item['search_count'],
                'sort' => $item['sort'],
            ];
        }, $this->collection);
    }
}