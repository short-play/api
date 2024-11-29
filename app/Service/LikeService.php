<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\ErrorCode;
use App\Exception\ShortPlayException;
use App\Kernel\UniqueData;
use App\Model\Video;
use App\Model\VideoItem;
use App\Model\VideoLike;
use Hyperf\Collection\Collection;
use Hyperf\Database\Model\Model;
use Hyperf\DbConnection\Db;

class LikeService
{

    /**
     * 获取点赞列表
     * @param int $uniqueId
     * @param int $skip
     * @param int $take
     * @return Collection
     */
    public function getLikes(int $uniqueId, int $skip, int $take): Collection
    {
        return VideoLike::where('unique_id', $uniqueId)
            ->orderByDesc('created_at')
            ->withWhereHas('video')
            ->with('video')
            ->with('item')
            ->skip($skip)
            ->take($take)
            ->get();
    }

    /**
     * 根据视频id和itemId获取点赞
     * @param int $uId
     * @param int $vId
     * @param int $iId
     * @param array $col
     * @return Model|null
     */
    public function findVideoLike(int $uId, int $vId, int $iId, array $col = ['id']): ?Model
    {
        return VideoLike::where('unique_id', $uId)
            ->where('video_id', $vId)
            ->where('item_id', $iId)
            ->first($col);
    }

    /**
     * 点赞
     * @param array $item
     * @param UniqueData $uniqueData
     * @return void
     */
    public function likeStore(UniqueData $uniqueData, array $item): void
    {
        $uniqueId = $uniqueData->getUnique();
        // 处理点赞逻辑
        $like = $this->findVideoLike($uniqueId, intval($item['video_id']), intval($item['id']));
        if ($like !== null) {
            return;
        }
        Db::transaction(function () use ($uniqueId, $item) {
            VideoLike::create([
                'item_id' => $item['id'],
                'unique_id' => $uniqueId,
                'video_id' => $item['video_id'],
            ]);
            // 点赞数+1
            VideoItem::where('id', $item['id'])->increment('short_count');
            // 总互动数+1
            Video::where('id', $item['video_id'])->increment('interact_count');
        });
    }

    /**
     * 取消点赞
     * @param UniqueData $uniqueData
     * @param array $item
     * @return void
     */
    public function likeCancel(UniqueData $uniqueData, array $item): void
    {
        Db::transaction(function () use ($uniqueData, $item) {
            $result = VideoLike::where('unique_id', $uniqueData->getUnique())
                ->where('video_id', $item['video_id'])
                ->where('item_id', $item['id'])
                ->delete();
            if ($result == 0) {
                throw new ShortPlayException(ErrorCode::DATA_INVALID->value);
            }
            // 点赞数减1
            VideoItem::where('id', $item['id'])->decrement('short_count');
            // 总互动数-1
            Video::where('id', $item['video_id'])->decrement('interact_count');
        });
    }
}