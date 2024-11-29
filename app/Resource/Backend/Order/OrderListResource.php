<?php

declare(strict_types=1);

namespace App\Resource\Backend\Order;

use App\Constants\Enum\OrderStatus;
use App\Kernel\AbstractAdminResource;
use Hyperf\Contract\LengthAwarePaginatorInterface;

class OrderListResource extends AbstractAdminResource
{

    public function __construct(protected LengthAwarePaginatorInterface $paginator)
    {
    }

    public function getResources(): array
    {
        return array_map(function ($item) {
            return [
                'no' => $item->no,
                'amount' => $item->amount,
                'status' => $item->status,
                'status_name' => OrderStatus::getMessage($item->status),
                'pay_time' => $item->pay_time?->toDateTimeString(),
                'created_at' => $item->created_at->toDateTimeString(),
                'user' => [
                    'id' => (string)$item->user->id,
                    'name' => $item->user->name,
                ]
            ];
        }, $this->paginator->items());
    }
}