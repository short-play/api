<?php

declare(strict_types=1);

namespace App\Resource\Backend\Admin;

use App\Kernel\AbstractAdminResource;
use Hyperf\Contract\LengthAwarePaginatorInterface;

class AdminListResource extends AbstractAdminResource
{

    public function __construct(protected LengthAwarePaginatorInterface $paginator)
    {
    }

    public function getResources(): array
    {
        return array_map(function ($item) {
            return (new AdminDetailResource($item))->toArray();
        }, $this->paginator->items());
    }
}