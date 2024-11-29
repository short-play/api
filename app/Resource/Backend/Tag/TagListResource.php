<?php

declare(strict_types=1);

namespace App\Resource\Backend\Tag;

use App\Kernel\AbstractAdminResource;
use Hyperf\Contract\LengthAwarePaginatorInterface;

class TagListResource extends AbstractAdminResource
{

    public function __construct(protected LengthAwarePaginatorInterface $paginator)
    {
    }

    public function getResources(): array
    {
        return array_map(function ($item) {
            return [
                'id' => (string)$item->id,
                'value' => $item->value,
                'sort' => $item->sort,
                'search_count' => $item->search_count,
                'created_at' => $item->created_at->toDateTimeString(),
                'updated_at' => $item->updated_at->toDateTimeString(),
            ];
        }, $this->paginator->items());
    }
}