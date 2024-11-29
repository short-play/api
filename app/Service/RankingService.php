<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\Enum\RankingType;
use App\Constants\Enum\TagType;
use App\Constants\ErrorCode;
use App\Constants\ProjectConfig;
use App\Constants\RedisKey;
use App\Exception\ShortPlayException;
use App\Model\Ranking;
use App\Model\Tag;
use App\Model\Video;
use Hyperf\Collection\Collection;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use function FriendsOfHyperf\Lock\lock;

class RankingService
{

    /**
     * 获取榜单列表
     * @param int $type
     * @param int $pageSize
     * @return LengthAwarePaginatorInterface
     */
    public function rankingList(int $type, int $pageSize): LengthAwarePaginatorInterface
    {
        if (RankingType::tryFrom($type) == null) {
            throw new ShortPlayException(ErrorCode::DATA_INVALID->value);
        }
        $query = Ranking::where('ranking_type', $type)->latest('sort');
        $query = match (RankingType::tryFrom($type)) {
            RankingType::TagSearch => $query->with('tag'),
            default => $query->with('video')
        };
        return $query->paginate($pageSize);
    }

    /**
     * 添加榜单数据
     * @param int $type
     * @param array $items
     * @return void
     */
    public function create(int $type, array $items): void
    {
        $key = RedisKey::RANKING_LOCK . $type;
        $items = $this->handleRequestData($items);
        $lockStatus = lock($key, 10)->get(function () use ($type, $items) {
            $diffItems = match ($type) {
                RankingType::Recommended->value => $this->addRecommended($type, $items),
                RankingType::New->value => $this->addNew($type, $items),
                RankingType::ShortSearch->value => $this->addShortSearch($type, $items),
                RankingType::TagSearch->value => $this->addTagSearch($type, $items),
                RankingType::NewShortSearch->value => $this->addNewShortSearch($type, $items),
                default => throw new ShortPlayException(ErrorCode::DATA_INVALID->value)
            };
            Ranking::insertRankings($type, $diffItems);
        });
        if ($lockStatus === false) {
            throw new ShortPlayException(ErrorCode::LOCKING->value);
        }
    }

    /**
     * 修改榜单排序
     * @param int $id
     * @param int $sort
     * @return void
     */
    public function updateRanking(int $id, int $sort): void
    {
        $ranking = Ranking::findOrFail($id);
        $ranking->sort = $sort;
        $ranking->save();
    }

    /**
     * 删除榜单信息
     * @param array $ids
     * @return void
     */
    public function deleteRanking(array $ids): void
    {
        Ranking::whereIn('id', $ids)->delete();
    }

    /**
     * 添加推荐榜单
     * @param int $type
     * @param array $items
     * @return array
     */
    protected function addRecommended(int $type, array $items): array
    {
        $this->checkVideoExist($items);
        $size = ProjectConfig::RECOMMENDED_SIZE;
        return $this->checkRankingExist($type, $items, $size);
    }

    /**
     * 添加新剧榜单
     * @param int $type
     * @param array $items
     * @return array
     */
    protected function addNew(int $type, array $items): array
    {
        $videos = $this->checkVideoExist($items, fn($item) => $item->tag_type != TagType::New->value);
        if ($videos->isNotEmpty()) {
            throw new ShortPlayException(
                ErrorCode::TAG_TYPE_LIMIT->value, ['type' => TagType::New->getMessage()]
            );
        }
        return $this->checkRankingExist($type, $items);
    }

    /**
     * 短剧热搜榜
     * @param int $type
     * @param array $items
     * @return array
     */
    protected function addShortSearch(int $type, array $items): array
    {
        $this->checkVideoExist($items);
        $limitSize = ProjectConfig::RANKING_SEARCH_SIZE;
        return $this->checkRankingExist($type, $items, $limitSize);
    }

    /**
     * 分类热搜榜单
     * @param int $type
     * @param array $items
     * @return array
     */
    protected function addTagSearch(int $type, array $items): array
    {
        $this->checkTagExist($items);
        $limitSize = ProjectConfig::RANKING_SEARCH_SIZE;
        return $this->checkRankingExist($type, $items, $limitSize);
    }

    /**
     * 短剧新剧榜
     * @param int $type
     * @param array $items
     * @return array
     */
    protected function addNewShortSearch(int $type, array $items): array
    {
        $videos = $this->checkVideoExist($items, fn($item) => $item->tag_type != TagType::New->value);
        if ($videos->isNotEmpty()) {
            throw new ShortPlayException(
                ErrorCode::TAG_TYPE_LIMIT->value, ['type' => TagType::New->getMessage()]
            );
        }
        $limitSize = ProjectConfig::RANKING_SEARCH_SIZE;
        return $this->checkRankingExist($type, $items, $limitSize);
    }

    /**
     * 处理重复的请求数据id
     * @param array $items
     * @return mixed
     */
    protected function handleRequestData(array $items): mixed
    {
        // 处理item
        $newItems = array_reduce($items, function ($carry, $item) {
            if (!isset($carry[$item['id']])) {
                $carry[$item['id']] = $item;
            }
            return $carry;
        }, []);
        return array_values($newItems);
    }

    /**
     * 校验视频数据真实性
     * @param array $items
     * @param callable|null $callback
     * @return Collection
     */
    protected function checkVideoExist(array $items, ?callable $callback = null): Collection
    {
        $videIds = array_column($items, 'id');
        $videos = Video::whereIn('id', $videIds)->select(Video::$select)->get();
        if ($videos->count() != count($videIds)) {
            throw new ShortPlayException(ErrorCode::DATA_INVALID->value);
        }
        if ($callback) {
            return $videos->filter($callback);
        }
        return $videos;
    }

    /**
     * 校验标签tag数据真实性
     * @param array $items
     * @return Collection
     */
    protected function checkTagExist(array $items): Collection
    {
        $tagIds = array_column($items, 'id');
        $tags = Tag::whereIn('id', $tagIds)->get();
        if ($tags->count() != count($tagIds)) {
            throw new ShortPlayException(ErrorCode::DATA_INVALID->value);
        }
        return $tags;
    }

    /**
     * 校验榜单数据是否已添加或超过条数限制
     * @param int $type
     * @param array $items
     * @param int $limitSize
     * @return array
     */
    protected function checkRankingExist(int $type, array $items, int $limitSize = 0): array
    {
        $uniqueIds = array_column($items, 'id');
        $rankings = Ranking::where('ranking_type', $type)->get();
        $diff = array_diff($uniqueIds, $rankings->pluck('unique_id')->toArray());
        // 校验是否大于限制条数
        if ($limitSize > 0 && ($rankings->count() + count($diff) > $limitSize)) {
            throw new ShortPlayException(ErrorCode::DATA_LIMIT_SIZE->value, ['size' => $limitSize]);
        }
        $filterItems = array_filter($items, fn($item) => in_array($item['id'], $diff));
        if (empty($filterItems)) {
            throw new ShortPlayException(ErrorCode::EXISTS->value);
        }
        return $filterItems;
    }
}