<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\ErrorCode;
use App\Exception\ShortPlayException;
use App\Model\Actor;
use App\Model\VideoActor;
use Hyperf\Contract\LengthAwarePaginatorInterface;

class ActorService
{

    /**
     * 获取演员列表
     * @param array $search
     * @param int $pageSize
     * @return LengthAwarePaginatorInterface
     */
    public function actorsList(array $search, int $pageSize): LengthAwarePaginatorInterface
    {
        $query = Actor::latest('updated_at');
        if (!empty($search['name'])) {
            $query->where('value', 'like', "%{$search['name']}%");
        }
        return $query->paginate($pageSize);
    }

    /**
     * 添加演员
     * @param string $value
     * @return void
     */
    public function createActors(string $value): void
    {
        if (Actor::where('value', $value)->first(['id'])) {
            throw new ShortPlayException(ErrorCode::EXISTS->value);
        }
        Actor::create(compact('value'));
    }

    /**
     * 修改演员
     * @param int $id
     * @param string $value
     * @return void
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function updateActors(int $id, string $value): void
    {
        $actor = Actor::where('value', $value)->first(['id']);
        if ($actor && $actor->id != $id) {
            throw new ShortPlayException(ErrorCode::EXISTS->value);
        }
        Actor::where('id', $id)->update(compact('value'));
    }

    /**
     * 删除演员
     * @param int $id
     * @return void
     */
    public function deleteActors(int $id): void
    {
        if (VideoActor::where('actor_id', $id)->first(['id'])) {
            throw new ShortPlayException(ErrorCode::ACTOR_APPLY_VIDEO->value);
        }
        Actor::where('id', $id)->delete();
    }
}