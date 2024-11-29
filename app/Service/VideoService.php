<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\Enum\RankingType;
use App\Constants\Enum\VideoType;
use App\Constants\ErrorCode;
use App\Constants\ProjectConfig;
use App\Constants\RedisKey;
use App\Exception\ShortPlayException;
use App\Kernel\UniqueData;
use App\Model\Ranking;
use App\Model\Video;
use App\Model\VideoItem;
use Hyperf\Cache\Annotation\CacheAhead;

class VideoService
{

    /**
     * 获取首页播放列表
     * @param int $skip
     * @param int $take
     * @return array
     */
    public function videoPlayList(int $skip, int $take): array
    {
        return Video::withoutTrashed()->where('type', VideoType::Short->value)
            ->orderBy('interact_count', 'desc')
            ->orderBy('play_count', 'desc')
            ->whereNotNull('item_id')
            ->with('item')
            ->skip($skip)
            ->take($take)
            ->get()
            ->toArray();
    }

    /**
     * 获取猜你喜欢列表(播放量最高排序)猜你喜欢只获取短剧类别下的
     * @return array
     */
    public function videoLikeList(): array
    {
        return Video::withoutTrashed()->where('type', VideoType::Short->value)
            ->take(ProjectConfig::VIDEO_LIKE_LIST_SIZE)
            ->orderByDesc('play_count')
            ->with('tags')
            ->get()
            ->toArray();
    }

    /**
     * 获取视频详情
     * @param int $id
     * @return array
     */
    public function videoDetail(int $id): array
    {
        $video = $this->getVideoDetailCache($id);
        if (empty($video)) {
            throw new ShortPlayException(ErrorCode::VIDEO_NOT_FOUND->value);
        }
        // 视频已下架
        if ($video['deleted_at'] != null) {
            throw new ShortPlayException(ErrorCode::VIDEO_REMOVAL->value);
        }
        return $video;
    }

    /**
     * 更多好剧推荐是获取互动量高的视频 同类别下视频
     * @param VideoType $type
     * @return array
     */
    public function moreVideo(VideoType $type): array
    {
        return Video::withoutTrashed()->where('type', $type->value)
            ->take(ProjectConfig::VIDEO_GOOD_LIST_SIZE)
            ->orderBy('interact_count', 'desc')
            ->with('tags')
            ->get()
            ->toArray();
    }

    /**
     * 找剧列表
     * @param int $skip
     * @param int $take
     * @return array
     */
    public function findVideoList(int $skip, int $take): array
    {
        return Video::withoutTrashed()
            ->orderByDesc('play_count')
            ->orderByDesc('interact_count')
            ->with('tags')
            ->skip($skip)
            ->take($take)
            ->get()
            ->toArray();
    }

    /**
     * 根据tagId获取视频列表
     * @param int $tagId
     * @param int $skip
     * @param int $take
     * @return array
     */
    public function tagVideoList(int $tagId, int $skip, int $take): array
    {
        return Video::withoutTrashed()
            ->withWhereHas('tags', fn($q) => $q->where('tag_id', $tagId))
            ->orderByDesc('play_count')
            ->orderByDesc('interact_count')
            ->skip($skip)
            ->take($take)
            ->get()
            ->toArray();
    }

    /**
     * 获取电影或电视剧列表
     * @param VideoType $type
     * @param int $skip
     * @param int $take
     * @return array
     */
    public function movieOrTvList(VideoType $type, int $skip, int $take): array
    {
        return Video::withoutTrashed()
            ->where('type', $type->value)
            ->orderByDesc('play_count')
            ->orderByDesc('interact_count')
            ->with('tags')
            ->with('actor')
            ->skip($skip)
            ->take($take)
            ->get()
            ->toArray();
    }

    /**
     * 榜单列表
     * @param RankingType $type
     * @return array
     */
    public function rankingVideoList(RankingType $type): array
    {
        return Ranking::where('ranking_type', $type->value)
            ->with('video')
            ->with('video.tags')
            ->orderByDesc('sort')
            ->get()
            ->toArray();
    }

    /**
     * 新剧
     * @param int $skip
     * @param int $take
     * @return array
     */
    public function newVideoList(int $skip, int $take): array
    {
        return Ranking::where('ranking_type', RankingType::New->value)
            ->with('video')
            ->with('video.tags')
            ->orderByDesc('sort')
            ->take($skip)
            ->take($take)
            ->get()
            ->toArray();
    }

    /**
     * 搜索视频
     * @param string $keyword
     * @param int $skip
     * @param int $take
     * @return array
     */
    public function searchVideo(string $keyword, int $skip, int $take): array
    {
        return Video::withoutTrashed()
            ->where('title', 'like', '%' . $keyword . '%')
            ->orderByDesc('play_count')
            ->orderByDesc('interact_count')
            ->with('tags')
            ->skip($skip)
            ->take($take)
            ->get()
            ->toArray();
    }

    /**
     * 获取播放视频url
     * @param UniqueData $unique
     * @param int $videoId
     * @param int $itemId
     * @return string
     */
    public function getPlayUrl(UniqueData $unique, int $videoId, int $itemId): string
    {
        $item = $this->checkItem($videoId, $itemId);
        if (!$item['is_view']) {
            return $item['url'];
        }
        // 如果是会员则校验是否登录是否开通会员
        if (!$unique->isUser()) {
            throw new ShortPlayException(ErrorCode::NOT_LOGGED->value);
        }
        if (!$unique->getUser()->is_member) {
            throw new ShortPlayException(ErrorCode::USER_NOT_ACTIVATED->value);
        }
        return $item['url'];
    }

    /**
     * 校验item数据真实性
     * @param int $videoId
     * @param int $itemId
     * @param bool $isResVideo
     * @return array
     */
    public function checkItem(int $videoId, int $itemId, bool $isResVideo = false): array
    {
        $video = $this->videoDetail($videoId);
        $item = array_filter($video['items'], fn($item) => $item['id'] == $itemId);
        if (empty($item)) {
            throw new ShortPlayException(ErrorCode::VIDEO_ITEM_NOT_FOUND->value);
        }
        $singleItem = array_pop($item);
        return $isResVideo ? array_merge($video, ['item' => $singleItem]) : $singleItem;
    }

    /**
     * 获取总数
     * @param int $id
     * @return Video
     */
    public function videoCount(int $id): Video
    {
        return Video::findOrFail($id, ['play_count', 'collect_count', 'search_count']);
    }

    /**
     * 获取点赞总数
     * @param int $id
     * @return VideoItem
     */
    public function itemCount(int $id): VideoItem
    {
        return VideoItem::findOrFail($id, ['short_count', 'comment_count']);
    }

    /**
     * 获取缓存视频详情
     * @param int $id
     * @return array
     */
    #[CacheAhead(
        prefix: RedisKey::VIDEO_DETAIL['key'],
        value: RedisKey::VIDEO_DETAIL['val'],
        ttl: RedisKey::VIDEO_DETAIL['ttl'],
        aheadSeconds: RedisKey::VIDEO_DETAIL['aheadSeconds']
    )]
    private function getVideoDetailCache(int $id): array
    {
        // 该缓存的统计数据量是不对的,其他字段是可以的
        $video = Video::withTrashed()->with(['items', 'tags'])->find($id);
        if (empty($video)) {
            return [];
        }
        return $video->toArray();
    }
}