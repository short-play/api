<?php

declare(strict_types=1);

namespace App\Resource\Backend\User;

use App\Constants\Enum\Preference;
use App\Kernel\ResourceInterface;
use App\Model\User;
use Hyperf\Database\Model\Model;
use Qbhy\HyperfAuth\Authenticatable;

class UserDetailResource implements ResourceInterface
{

    public function __construct(protected User|Model|Authenticatable $user)
    {
    }

    /**
     * @noinspection PhpDynamicAsStaticMethodCallInspection
     */
    public function toArray(): array
    {
        return [
            'id' => (string)$this->user->id,
            'mail' => $this->user->mail,
            'name' => $this->user->name,
            'sex' => $this->user->sex,
            'profile' => $this->user->profile,
            'personal_sign' => $this->user->personal_sign,
            'is_member' => $this->user->is_member,
            'member_time' => $this->user->member_time,
            'birthday' => $this->user->birthday,
            'preference' => $this->user->preference,
            'preference_name' => Preference::getMessage($this->user->preference),
            'created_at' => $this->user->created_at->toDateTimeString(),
            'is_deleted' => !empty($this->user->deleted_at)
        ];
    }
}