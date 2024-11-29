<?php

declare(strict_types=1);

namespace App\Resource\Backend\Video;

use App\Constants\Enum\UserType;
use App\Kernel\AbstractAdminResource;
use Hyperf\Contract\LengthAwarePaginatorInterface;

class ShortUserListResource extends AbstractAdminResource
{

    public function __construct(protected LengthAwarePaginatorInterface $paginator)
    {
    }

    /**
     * @return array
     */
    public function getResources(): array
    {
        return array_map(function ($item) {
            $userType = empty($item->user) ? UserType::Device : UserType::USER;
            return [
                'id' => (string)$item->id,
                'user' => $item->user,
                'device' => $item->device,
                'type' => $userType->value,
                'type_name' => $userType->getMessage(),
            ];
        }, $this->paginator->items());
    }
}