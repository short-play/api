<?php

declare(strict_types=1);

namespace App\Resource\Backend\Admin;

use App\Constants\Enum\AdminRole;
use App\Kernel\ResourceInterface;
use App\Model\Admin;
use Hyperf\Database\Model\Model;
use Qbhy\HyperfAuth\Authenticatable;

class AdminDetailResource implements ResourceInterface
{

    public function __construct(protected Admin|Model|Authenticatable $collection)
    {
    }

    /**
     * @noinspection PhpDynamicAsStaticMethodCallInspection
     */
    public function toArray(): array
    {
        return [
            'id' => (string)$this->collection->id,
            'mail' => $this->collection->mail,
            'name' => $this->collection->name,
            'role' => $this->collection->role,
            'roleName' => AdminRole::getMessage($this->collection->role),
            'created_at' => $this->collection->created_at->toDateTimeString(),
            'is_deleted' => !empty($this->collection->deleted_at),
        ];
    }
}