<?php

declare(strict_types=1);

namespace App\Resource\Backend\Actor;

use App\Kernel\AbstractAdminResource;
use Hyperf\Contract\LengthAwarePaginatorInterface;

class ActorListResource extends AbstractAdminResource
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
                'created_at' => $item->created_at->toDateTimeString(),
            ];
        }, $this->paginator->items());
    }
}