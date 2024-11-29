<?php

declare(strict_types=1);

namespace App\Resource\Api\User;

use App\Kernel\ResourceInterface;
use App\Model\User;
use Qbhy\HyperfAuth\Authenticatable;

class UserResource implements ResourceInterface
{

    public function __construct(protected User|Authenticatable $model)
    {
    }

    public function toArray(): array
    {
        return [
            'id' => (string)$this->model->id,
            'mail' => $this->model->mail,
            'name' => $this->model->name,
            'sex' => $this->model->sex,
            'profile' => $this->model->profile,
            'personal_sign' => $this->model->personal_sign,
            'is_member' => $this->model->is_member,
            'member_time' => $this->model->member_time,
            'birthday' => $this->model->birthday,
            'preference' => $this->model->preference
        ];
    }
}