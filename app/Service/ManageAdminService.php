<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\ErrorCode;
use App\Constants\RedisKey;
use App\Exception\ShortPlayException;
use App\Model\Admin;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use function FriendsOfHyperf\Lock\lock;

class ManageAdminService
{

    /**
     * 获取管理员列表
     * @param array $search
     * @param int $pageSize
     * @return LengthAwarePaginatorInterface
     */
    public function adminList(array $search, int $pageSize): LengthAwarePaginatorInterface
    {
        $query = Admin::withTrashed()->latest('updated_at');
        if (!empty($search['mail'])) {
            $query->where('mail', 'like', "%{$search['mail']}%");
        }
        return $query->paginate($pageSize);
    }

    /**
     * 添加管理员
     * @param array $data
     * @return void
     */
    public function createAdmin(array $data): void
    {
        $lockKey = RedisKey::ADMIN_CREATE_LOCK . $data['mail'];
        $lockStatus = lock($lockKey, 10)->get(function () use ($data) {
            $admin = Admin::withTrashed()->where('mail', $data['mail'])->first(['id']);
            if ($admin) {
                throw new ShortPlayException(ErrorCode::ADMIN_EXISTS->value);
            }
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            Admin::create($data);
        });
        if ($lockStatus === false) {
            throw new ShortPlayException(ErrorCode::LOCKING->value);
        }
    }

    /**
     * 修改管理员信息
     * @param int $id
     * @param array $data
     * @return void
     */
    public function updateAdmin(int $id, array $data): void
    {
        // 如果存在邮箱则校验是否已存在
        $admin = Admin::withTrashed()->where('mail', $data['mail'])
            ->where('id', '!=', $id)
            ->first(['id']);
        if ($admin) {
            throw new ShortPlayException(ErrorCode::MAIL_EXISTS->value);
        }
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        Admin::where('id', $id)->update($data);
    }

    /**
     * 启用禁用管理员
     * @param int $id
     * @param bool $disable
     * @param array $ids
     * @return void
     */
    public function deleteAdmin(int $id, bool $disable, array $ids): void
    {
        if (array_filter($ids, fn($i) => $i == $id)) {
            throw new ShortPlayException(ErrorCode::ADMIN_NO_OPERATE_SELF->value);
        }
        match ($disable) {
            false => Admin::withTrashed()->whereIn('id', $ids)->restore(),
            default => Admin::whereIn('id', $ids)->delete()
        };
    }
}