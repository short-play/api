<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\ErrorCode;
use App\Constants\RedisKey;
use App\Exception\ShortPlayException;
use App\Model\Admin;
use Hyperf\Cache\Listener\DeleteListenerEvent;
use Hyperf\Database\Model\Model;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;
use Qbhy\HyperfAuth\Authenticatable;

class AdminService
{

    #[Inject]
    protected EventDispatcherInterface $dispatcher;

    /**
     * 根据id获取信息
     * @param int $id
     * @return Model
     */
    public function getById(int $id): Model
    {
        return Admin::withTrashed()->findOrFail($id);
    }

    /**
     * 修改密码
     * @param Authenticatable $admin
     * @param string $old
     * @param string $password
     * @return void
     * @noinspection PhpDynamicFieldDeclarationInspection
     */
    public function changePassword(Authenticatable $admin, string $old, string $password): void
    {
        if (!password_verify($old, $admin->password)) {
            throw new ShortPlayException(ErrorCode::PASSWORD_ERROR->value);
        }
        Db::transaction(function () use ($admin, $password) {
            $admin->password = password_hash($password, PASSWORD_BCRYPT);
            $admin->save();
            $this->delAdminCache($admin->id);
        });
    }

    /**
     * 修改管理员信息
     * @param Authenticatable $admin
     * @param string $mail
     * @param string $name
     * @return void
     */
    public function updateAdmin(Authenticatable $admin, string $mail, string $name): void
    {
        $adminInfo = Admin::withTrashed()->where(compact('mail'))
            ->where('id', '!=', $admin->id)
            ->first(['id']);
        if ($adminInfo) {
            throw new ShortPlayException(ErrorCode::MAIL_EXISTS->value);
        }
        Db::transaction(function () use ($admin, $mail, $name) {
            $admin->fill(compact('name', 'mail'))->save();
            $this->delAdminCache($admin->id);
        });
    }

    /**
     * 退出登录
     * @param int $adminId
     * @return void
     */
    public function logout(int $adminId): void
    {
        auth('admin')->logout();
        $this->delAdminCache($adminId);
    }

    /**
     * 删除用户缓存
     * @param int $id
     * @return void
     */
    public function delAdminCache(int $id): void
    {
        $this->dispatcher->dispatch(new DeleteListenerEvent(RedisKey::ADMIN['listener'], ['key' => $id]));
    }
}