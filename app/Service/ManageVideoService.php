<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\Enum\ActivityType;
use App\Constants\Enum\VideoType;
use App\Constants\ErrorCode;
use App\Constants\RedisKey;
use App\Exception\ShortPlayException;
use App\Model\Activity;
use App\Model\Actor;
use App\Model\Ranking;
use App\Model\Tag;
use App\Model\Video;
use App\Model\VideoActor;
use App\Model\VideoCollect;
use App\Model\VideoItem;
use App\Model\VideoLike;
use App\Model\VideoTag;
use Hyperf\Cache\Annotation\CacheEvict;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Collection;
use Hyperf\DbConnection\Db;

class ManageVideoService
{

    /**
     * 获取视频列表
     * @param array $search
     * @param int $pageSize
     * @return LengthAwarePaginatorInterface
     */
    public function videoList(array $search, int $pageSize): LengthAwarePaginatorInterface
    {
        $query = Video::latest('created_at')->with('tags')->with('actor');
        // 标题条件
        if (!empty($search['title'])) {
            $query->where('title', 'like', "%{$search['title']}%");
        }
        if (!empty($search['type'])) {
            $query->where('type', $search['type']);
        }
        return $query->paginate($pageSize);
    }

    /**
     * 创建视频
     * @param array $req
     * @param array $tags
     * @param array $actors
     * @return void
     */
    public function createVideo(array $req, array $tags, array $actors): void
    {
        // 检查视频是否已存在
        $video = Video::where('title', $req['title'])
            ->where('type', $req['type'])
            ->first(['id']);

        if ($video) {
            throw new ShortPlayException(ErrorCode::VIDEO_TITLE_EXISTS->value);
        }

        // 对于电影类型，进行额外的验证,其他视频类型剔除评分字段
        $req = $this->validateMovieRequirements($req);

        // 验证标签和演员数据
        $this->checkTagData($tags);
        $this->checkActorData($actors);

        // 执行数据库事务
        Db::transaction(function () use ($req, $tags, $actors) {
            $create = Video::create($req);
            VideoTag::batchInsertVideoTags($create->id, $tags);
            VideoActor::batchInsertVideoActors($create->id, $actors);
        });
    }

    /**
     * 修改视频信息
     * @param int $id
     * @param array $req
     * @param array $tags
     * @param array $actors
     * @return void
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function updateVideo(int $id, array $req, array $tags, array $actors): void
    {
        // 查找视频信息并验证
        $video = Video::findOrFail($id, ['type']);

        // 对于电影类型，进行额外的验证,其他视频类型剔除评分字段
        $req = $this->validateMovieRequirements($req, $video);

        // 校验标题是否重复
        $repeatVideo = Video::where('title', $req['title'])
            ->where('type', $video->type)
            ->first(['id']);
        if ($repeatVideo && $repeatVideo->id != $id) {
            throw new ShortPlayException(ErrorCode::VIDEO_TITLE_EXISTS->value);
        }

        // 校验修改的视频集数是否小于已上传的视频
        $itemCount = VideoItem::where('video_id', $id)->count();
        if ($itemCount > $req['num']) {
            throw new ShortPlayException(ErrorCode::VIDEO_NUM_LT_UPDATE->value);
        }

        // 验证标签和演员数据
        $this->checkTagData($tags);
        $this->checkActorData($actors);

        // 执行数据库事务
        Db::transaction(function () use ($id, $req, $tags, $actors) {
            Video::where('id', $id)->update($req);
            VideoTag::where('video_id', $id)->delete();
            VideoActor::where('video_id', $id)->delete();
            VideoTag::batchInsertVideoTags($id, $tags);
            VideoActor::batchInsertVideoActors($id, $actors);
        });
        $this->delVideoDetailCache($id);
    }

    /**
     * 删除视频
     * @param int $id
     * @return void
     */
    public function deleteVideo(int $id): void
    {
        if (Ranking::where('unique_id', $id)->count() > 0) {
            // 视频已添加到榜单,不可删除
            throw new ShortPlayException(ErrorCode::VIDEO_ADD_RANKING->value);
        }
        // 校验活动模版是否添加视频
        $activity = Activity::where('type', ActivityType::AppointVideo->value)->first();
        if ($activity && in_array($id, $activity->config['appoint']['video_ids'] ?? [])) {
            // 视频已添加到活动模版不可删除
            throw new ShortPlayException(ErrorCode::VIDEO_ADD_ACTIVITY->value);
        }
        Video::where('id', $id)->delete();
        $this->delVideoDetailCache($id);
    }

    /**
     * 获取视频详情
     * @param int $videoId
     * @return Video
     */
    public function videoDetail(int $videoId): Video
    {
        return Video::with(['tags', 'actor', 'items'])->findOrFail($videoId);
    }

    /**
     * 设置视频是否完结
     * @param int $id
     * @param int $finish
     * @return void
     */
    public function videoFinish(int $id, int $finish): void
    {
        Video::where('id', $id)->update(['is_finish' => $finish]);
        $this->delVideoDetailCache($id);
    }

    /**
     * 获取item列表数据
     * @param int $videoId
     * @return Collection
     */
    public function getVideoItems(int $videoId): Collection
    {
        return VideoItem::where('video_id', $videoId)->orderBy('sort')->get();
    }

    /**
     * 批量添加视频集合
     * @param int $videoId
     * @param array $reqItems
     * @return void
     */
    public function videoItemCreate(int $videoId, array $reqItems): void
    {
        $video = Video::findOrFail($videoId);
        // 获取已添加的item
        $items = VideoItem::where('video_id', $videoId)
            ->select(VideoItem::$select)
            ->orderBy('sort')
            ->get();

        // 电影只能添加一个视频
        $total = count($reqItems) + $items->count();
        if ($video->type == VideoType::Movie->value && $total > 1) {
            throw new ShortPlayException(ErrorCode::VIDEO_MOVIE_NUM_LIMIT->value);
        }
        // 视频集合不能超过总集数
        if ($total > $video->num) {
            throw new ShortPlayException(ErrorCode::VIDEO_UPDATE_GT_NUM->value);
        }

        // 处理要上传视频的集数
        $sort = $items->isNotEmpty() ? $items->last()->sort + 1 : 1;

        // 执行数据库事务
        Db::transaction(function () use ($video, $sort, $reqItems) {
            $items = VideoItem::batchInsertVideoItems($video->id, $sort, $reqItems);
            if (empty($video->item_id)) {
                $video->item_id = $items[0]['id'];
                $video->save();
            }
        });
        $this->delVideoDetailCache($video->id);
    }

    /**
     * 删除视频集合数据
     * @param int $id
     * @return void
     */
    public function deleteVideoItem(int $id): void
    {
        $item = VideoItem::findOrFail($id);
        // 获取与当前视频 ID 相关的其他项目数量
        $count = VideoItem::where('video_id', $item->video_id)
            ->where('id', '!=', $item->id)
            ->count();
        Db::transaction(function () use ($item, $count) {
            // 如果没有其他项目，则将视频的 item_id 设为 null
            if ($count == 0) {
                Video::where('id', $item->video_id)->update(['item_id' => null]);
                $item->delete();
            } else {
                // 否则，更新排序值，减少大于当前排序值的项目
                VideoItem::where('video_id', $item->video_id)
                    ->where('sort', '>', $item->sort)
                    ->decrement('sort');
            }
            $item->delete();
        });
        $this->delVideoDetailCache($item->video_id);
    }

    /**
     * 编辑视频状态,是否可看
     * @param int $id
     * @param int $viewType
     * @return void
     */
    public function videoItemView(int $id, int $viewType): void
    {
        $item = VideoItem::findOrFail($id);
        $item->is_view = $viewType;
        $item->save();
        $this->delVideoDetailCache($item->video_id);
    }

    /**
     * 获取视频点赞用户列表
     * @param int $itemId
     * @param int $pageSize
     * @return LengthAwarePaginatorInterface
     */
    public function videoItemShortUsers(int $itemId, int $pageSize): LengthAwarePaginatorInterface
    {
        $videoItem = VideoItem::findOrFail($itemId, ['id', 'video_id']);
        return VideoLike::where('video_id', $videoItem->video_id)
            ->where('item_id', $videoItem->id)
            ->latest('created_at')
            ->with(['user', 'device'])
            ->paginate($pageSize);
    }

    /**
     * 视频收藏用户列表
     * @param int $videoId
     * @param int $pageSize
     * @return LengthAwarePaginatorInterface
     */
    public function videoCollectUsers(int $videoId, int $pageSize): LengthAwarePaginatorInterface
    {
        return VideoCollect::where('video_id', $videoId)
            ->latest('created_at')
            ->with(['user', 'device'])
            ->paginate($pageSize);
    }

    /**
     * 校验标签数据真实性
     * @param array $tagIds
     * @return void
     */
    protected function checkTagData(array $tagIds): void
    {
        $tagModels = Tag::findMany($tagIds, ['id']);
        if ($tagModels->count() != count($tagIds)) {
            throw new ShortPlayException(ErrorCode::TAG_INVALID->value);
        }
    }

    /**
     * 校验演员数据真实性
     * @param array $actorIds
     * @return void
     */
    protected function checkActorData(array $actorIds): void
    {
        $actors = Actor::findMany($actorIds, ['id']);
        if ($actors->count() != count($actorIds)) {
            throw new ShortPlayException(ErrorCode::ACTOR_INVALID->value);
        }
    }

    /**
     * 验证电影的要求
     * @param array $req
     * @param Video|null $video
     * @return array
     */
    protected function validateMovieRequirements(array $req, ?Video $video = null): array
    {
        $videoType = $video?->type ?? $req['type'];
        if ($videoType != VideoType::Movie->value) {
            unset($req['rating']);
            return $req;
        }
        if (empty($req['rating'])) {
            throw new ShortPlayException(ErrorCode::VIDEO_RATING_EMPTY->value);
        }
        if ($req['num'] > 1) {
            throw new ShortPlayException(ErrorCode::VIDEO_MOVIE_NUM_LIMIT->value);
        }
        return $req;
    }

    /**
     * 删除视频详情缓存
     * @param int $id
     * @return int
     */
    #[CacheEvict(prefix: RedisKey::VIDEO_DETAIL['key'], value: RedisKey::VIDEO_DETAIL['val'])]
    protected function delVideoDetailCache(int $id): int
    {
        return $id;
    }
}