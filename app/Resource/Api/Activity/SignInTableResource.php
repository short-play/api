<?php

declare(strict_types=1);

namespace App\Resource\Api\Activity;

use App\Constants\Enum\ActivityType;
use App\Kernel\ResourceInterface;
use App\Model\Activity;
use Hyperf\Database\Model\Model;

class SignInTableResource implements ResourceInterface
{

    public function __construct(protected Model|Activity $activity, protected array $signInTables)
    {
    }

    /**
     * @noinspection PhpDynamicAsStaticMethodCallInspection
     */
    public function toArray(): array
    {
        return [
            'id' => (string)$this->activity->id,
            'name' => $this->activity->name,
            'desc' => $this->activity->desc,
            'type' => $this->activity->type,
            'type_name' => ActivityType::getMessage($this->activity->type),
            'signInTables' => $this->signInTables,
        ];
    }
}