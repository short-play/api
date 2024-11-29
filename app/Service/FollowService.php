<?php

declare(strict_types=1);

namespace App\Service;

use App\Kernel\UniqueData;
use App\Model\Video;
use App\Model\VideoCollect;
use Hyperf\Database\Model\Collection;
use Hyperf\DbConnection\Db;

class FollowService
{

    /**
     * 追剧保存接口
     * @param UniqueData $uniqueData
     * @param array $videoIds
     * @return void
     */
    public function follow(UniqueData $uniqueData, array $videoIds): void
    {
        // 校验数据真实性
        $dbVideos = Video::withoutTrashed()->find($videoIds, ['id'])->pluck('id')->toArray();
        // 判断是否已收藏
        $notFollowVideos = $this->isFollowed($uniqueData, $dbVideos);
        if (empty($notFollowVideos)) {
            return;
        }
        Db::transaction(function () use ($uniqueData, $notFollowVideos) {
            // 增加收藏数,// 批量插入数据
            Video::whereIn('id', $notFollowVideos)->incrementEach(
                ['collect_count' => 1, 'interact_count' => 1]
            );
            VideoCollect::insertVideoCollects($notFollowVideos, $uniqueData->getUnique());
        });
    }

    /**
     * 收藏列表
     * @param UniqueData $uniqueData
     * @param int $skip
     * @param int $take
     * @return Collection
     */
    public function followList(UniqueData $uniqueData, int $skip, int $take): Collection
    {
        return VideoCollect::where('unique_id', $uniqueData->getUnique())
            ->with(['view' => fn($q) => $q->where('unique_id', $uniqueData->getUnique())])
            ->orderByDesc('created_at')
            ->whereHas('video')
            ->with('video')
            ->skip($skip)
            ->take($take)
            ->get();
    }

    /**
     * 获取收藏总数
     * @param UniqueData $uniqueData
     * @return int
     */
    public function followsCount(UniqueData $uniqueData): int
    {
        return VideoCollect::where('unique_id', $uniqueData->getUnique())
            ->withWhereHas('video')
            ->count();
    }

    /**
     * 取消收藏
     * @param UniqueData $uniqueData
     * @param array $videoIds
     * @return void
     */
    public function unfollow(UniqueData $uniqueData, array $videoIds): void
    {
        $collects = VideoCollect::where('unique_id', $uniqueData->getUnique())
            ->whereIn('video_id', $videoIds)
            ->get(['id', 'video_id']);
        // 校验数据真实性，没有数据不执行操作
        if ($collects->isEmpty()) {
            return;
        }
        DB::transaction(function () use ($uniqueData, $collects) {
            // 删除追剧数据
            VideoCollect::whereIn('id', $collects->pluck('id'))->delete();
            // 更新统计量
            Video::whereIn('id', $collects->pluck('video_id'))->decrementEach(
                ['collect_count' => 1, 'interact_count' => 1]
            );
        });
    }

    /**
     * 判断是否已收藏
     * @param UniqueData $unique
     * @param array $videoIds
     * @param bool $isDiff
     * @return array
     */
    public function isFollowed(UniqueData $unique, array $videoIds, bool $isDiff = true): array
    {
        if (!empty($videoIds)) {
            $videoCollect = VideoCollect::where('unique_id', $unique->getUnique())
                ->whereIn('video_id', $videoIds)
                ->get(['video_id', 'id'])
                ->pluck('video_id')
                ->toArray();
            return $isDiff ? array_diff($videoIds, $videoCollect) : $videoCollect;
        }
        return [];
    }
}