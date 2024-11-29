<?php

declare(strict_types=1);

namespace App\Resource\Backend\Activity;

use App\Constants\Enum\ActivityStatus;
use App\Constants\Enum\ActivityType;
use App\Kernel\ResourceInterface;
use App\Model\Activity;

class ActivityResource implements ResourceInterface
{

    public function __construct(protected Activity $activity, protected bool $isConfig = false)
    {
    }

    /**
     * @noinspection PhpDynamicAsStaticMethodCallInspection
     * @noinspection PhpParamsInspection
     */
    public function toArray(): array
    {
        $responseData = [
            'id' => (string)$this->activity->id,
            'name' => $this->activity->name,
            'desc' => $this->activity->desc,
            'type' => $this->activity->type,
            'type_name' => ActivityType::getMessage($this->activity->type),
            'status' => $this->activity->status,
            'status_name' => ActivityStatus::getMessage($this->activity->status),
            'created_at' => $this->activity->created_at->toDateTimeString(),
            'updated_at' => $this->activity->updated_at->toDateTimeString(),
        ];
        if ($this->isConfig) {
            $responseData['config'] = $this->activity->config;
        }
        return $responseData;
    }
}