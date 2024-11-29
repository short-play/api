<?php

declare(strict_types=1);

namespace App\Resource\Api\Search;

use App\Kernel\ResourceInterface;

class SearchHistoryResource implements ResourceInterface
{

    public function __construct(protected array $collection)
    {
    }

    public function toArray(): array
    {
        return array_map(function ($item) {
            return [
                'value' => $item['value'],
            ];
        }, $this->collection);
    }
}