<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\Enum\RankingType;
use App\Constants\ErrorCode;
use App\Constants\RedisKey;
use App\Exception\ShortPlayException;
use App\Model\Ranking;
use App\Model\Tag;
use App\Model\VideoTag;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Listener\DeleteListenerEvent;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Di\Annotation\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;

class TagService
{

    #[Inject]
    protected EventDispatcherInterface $dispatcher;

    /**
     * 获取视频标签
     * @return array
     */
    #[Cacheable(
        prefix: RedisKey::TAG['key'],
        ttl: RedisKey::TAG['ttl'],
        listener: RedisKey::TAG['listener']
    )]
    public function getTags(): array
    {
        return Tag::orderByDesc('sort')->get()->toArray();
    }

    /**
     * 获取热搜标签榜单
     * @return array
     */
    public function getHotSearchTagList(): array
    {
        return Ranking::where('ranking_type', RankingType::TagSearch->value)
            ->orderByDesc('sort')
            ->with('tag')
            ->get()
            ->toArray();
    }

    /**
     * 标签列表
     * @param array $search
     * @param int $pageSize
     * @return LengthAwarePaginatorInterface
     */
    public function tagList(array $search, int $pageSize): LengthAwarePaginatorInterface
    {
        $query = Tag::latest('sort');
        if (!empty($search['name'])) {
            $query->where('value', 'like', "%{$search['name']}%");
        }
        return $query->paginate($pageSize);
    }

    /**
     * 创建标签,清除缓存
     * @param string $value
     * @param int $sort
     * @return void
     */
    public function createTag(string $value, int $sort): void
    {
        $tag = Tag::where(compact('value'))->first(['id']);
        if ($tag != null) {
            throw new ShortPlayException(ErrorCode::EXISTS->value);
        }
        Tag::create(compact('value', 'sort'));
        $this->delTagCache();
    }

    /**
     * 修改标签
     * @param int $id
     * @param array $update
     * @return void
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function updateTag(int $id, array $update): void
    {
        if (isset($update['value'])) {
            $tag = Tag::where('value', $update['value'])->first(['id']);
            if ($tag != null && $tag->id != $id) {
                throw new ShortPlayException(ErrorCode::EXISTS->value);
            }
        }
        Tag::where('id', $id)->update($update);
        $this->delTagCache();
    }

    /**
     * 删除标签
     * @param int $id
     * @return void
     */
    public function deleteTag(int $id): void
    {
        // 校验tag是否已应用video
        $videoTag = VideoTag::where('tag_id', $id)->first(['id']);
        if ($videoTag != null) {
            throw new ShortPlayException(ErrorCode::TAG_APPLY_VIDEO->value);
        }
        if (Ranking::where('unique_id', $id)->count() > 0) {
            // 标签已添加到榜单,不可删除
            throw new ShortPlayException(ErrorCode::TAG_APPLY_RANKING->value);
        }
        Tag::where(compact('id'))->delete();
        $this->delTagCache();
    }

    /**
     * 删除tags缓存
     * @return void
     */
    public function delTagCache(): void
    {
        $this->dispatcher->dispatch(new DeleteListenerEvent(RedisKey::TAG['listener'], []));
    }
}