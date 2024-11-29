<?php

declare(strict_types=1);

namespace App\Resource\Backend\Ranking;

use App\Kernel\AbstractAdminResource;
use Hyperf\Contract\LengthAwarePaginatorInterface;

class RankingListResource extends AbstractAdminResource
{

    public function __construct(protected LengthAwarePaginatorInterface $paginator)
    {
    }

    public function getResources(): array
    {
        return array_map(function ($item) {
            if ($item->relationLoaded('tag')) {
                $name = $item->tag->value;
            } else {
                $name = $item->video->title;
            }
            return [
                'id' => (string)$item->id,
                'unique_id' => (string)$item->unique_id,
                'sort' => $item->sort,
                'name' => $name,
                'created_at' => $item->created_at->toDateTimeString(),
            ];
        }, $this->paginator->items());
    }
}