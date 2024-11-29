<?php

declare(strict_types=1);

namespace App\Resource\Api\User;

use App\Constants\Enum\ActivityType;
use App\Kernel\ResourceInterface;
use Hyperf\Database\Model\Collection;

class UserCoinsResource implements ResourceInterface
{

    public function __construct(protected Collection $collection)
    {
    }

    /**
     * @noinspection PhpDynamicAsStaticMethodCallInspection
     */
    public function toArray(): array
    {
        return $this->collection->map(function ($item) {
            return [
                'coin' => $item->coin,
                'activity_type' => ActivityType::getMessage($item->activity_type),
                'created_at' => $item->created_at->toDateTimeString(),
            ];
        })->toArray();
    }
}