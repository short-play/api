<?php

declare(strict_types=1);

namespace App\Resource\Backend\User;

use App\Kernel\AbstractAdminResource;
use Hyperf\Contract\LengthAwarePaginatorInterface;

class UserListResource extends AbstractAdminResource
{

    public function __construct(protected LengthAwarePaginatorInterface $paginator)
    {
    }

    public function getResources(): array
    {
        return array_map(function ($item) {
            return (new UserDetailResource($item))->toArray();
        }, $this->paginator->items());
    }
}