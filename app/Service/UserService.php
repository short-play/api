<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\Enum\Preference;
use App\Constants\ErrorCode;
use App\Constants\RedisKey;
use App\Exception\ShortPlayException;
use App\Model\User;
use App\Model\UserCoin;
use App\Model\UserCoinDetail;
use Hyperf\Cache\Listener\DeleteListenerEvent;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Collection;
use Hyperf\Di\Annotation\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;

class UserService
{

    #[Inject]
    protected EventDispatcherInterface $dispatcher;

    /**
     * 获取用户列表
     * @param array $search
     * @param int $pageSize
     * @return LengthAwarePaginatorInterface
     */
    public function userList(array $search, int $pageSize): LengthAwarePaginatorInterface
    {
        $query = User::withTrashed()->latest();
        if (!empty($search['mail'])) {
            $query->where('mail', 'like', "%{$search['mail']}%");
        }
        return $query->paginate($pageSize);
    }

    /**
     * 根据id获取所有用户信息（包括已删除的）
     * @param int $userId
     * @param array $columns
     * @return User
     */
    public function getUserById(int $userId, array $columns = ['*']): User
    {
        return User::withTrashed()->findOrFail($userId, $columns);
    }

    /**
     * @param int $id
     * @param string $oldPassword
     * @param string $newPassword
     * @return void
     * @noinspection PhpDynamicFieldDeclarationInspection
     */
    public function changePassword(int $id, string $oldPassword, string $newPassword): void
    {
        $user = User::retrieveById($id);
        if (!password_verify($oldPassword, $user->password)) {
            throw new ShortPlayException(ErrorCode::PASSWORD_ERROR->value);
        }
        $user->password = password_hash($newPassword, PASSWORD_BCRYPT);
        $user->save();
        $this->delUserCache($id);
    }

    /**
     * 修改用户信息
     * @param int $id
     * @param array $data
     * @return void
     */
    public function updateUser(int $id, array $data): void
    {
        $user = User::retrieveById($id);
        $user->fill($data)->save();
        $this->delUserCache($id);
    }

    /**
     * 修改看剧偏好
     * @param int $id
     * @param Preference|null $preference
     * @return void
     */
    public function updatePreference(int $id, ?Preference $preference): void
    {
        User::where('id', $id)->update(['preference' => $preference?->value]);
        $this->delUserCache($id);
    }

    /**
     * 获取金币总数
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function getUserCoinCount(int $userId): int
    {
        return UserCoin::where('user_id', $userId)->firstOrFail(['coin_num'])->coin_num;
    }

    /**
     * 获取用户任务金币明细
     * @param int $userId
     * @param int $skip
     * @param int $take
     * @return Collection
     */
    public function getUserCoinDetail(int $userId, int $skip, int $take): Collection
    {
        return UserCoinDetail::where('user_id', $userId)
            ->select(['coin', 'activity_type', 'created_at'])
            ->latest('created_at')
            ->skip($skip)
            ->take($take)
            ->get();
    }

    /**
     * 禁用启用用户
     * @param bool $disable
     * @param array $ids
     * @return void
     */
    public function deleteUser(bool $disable, array $ids): void
    {
        match ($disable) {
            false => User::withTrashed()->whereIn('id', $ids)->restore(),
            default => User::whereIn('id', $ids)->delete()
        };
        array_map(fn($id) => $this->delUserCache(intval($id)), $ids);
    }

    /**
     * 删除用户缓存
     * @param int $id
     * @return void
     */
    public function delUserCache(int $id): void
    {
        $this->dispatcher->dispatch(new DeleteListenerEvent(RedisKey::USER['listener'], ['key' => $id]));
    }
}