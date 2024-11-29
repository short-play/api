<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\Enum\ActivityType;
use App\Constants\Enum\VideoType;
use App\Constants\ErrorCode;
use App\Constants\RedisKey;
use App\Exception\ShortPlayException;
use App\Kernel\UniqueData;
use App\Model\Video;
use App\Model\VideoView;
use Hyperf\Collection\Collection;
use Hyperf\Database\Model\Model;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use function FriendsOfHyperf\Lock\lock;
use function Hyperf\Support\now;

class ViewService
{

    #[Inject]
    protected ActivityService $activityService;

    /**
     * 获取播放历史详情
     * @param int $uniqueId
     * @param int $id
     * @return Model
     */
    public function detail(int $uniqueId, int $id): Model
    {
        return VideoView::where(['id' => $id, 'unique_id' => $uniqueId])
            ->firstOrFail(VideoView::$select);
    }

    /**
     * 保存浏览记录
     * @param UniqueData $unique
     * @param array $video
     * @param int $duration
     * @return void
     */
    public function viewStore(UniqueData $unique, array $video, int $duration): void
    {
        $key = RedisKey::USER_VIEW_HISTORY . $unique->getUnique();
        lock($key, 10)->get(function () use ($unique, $video, $duration) {
            $videoView = VideoView::where('unique_id', $unique->getUnique())
                ->where('video_id', $video['id'])
                ->first('id');
            // 如果为用户,则统计当天观看时长
            if ($unique->isUser()) {
                $this->countViewingTime($unique->getUnique(), $video, $duration);
            }
            // 更新观看记录
            if ($videoView) {
                $this->updateView($videoView, $video, $duration);
                return;
            }
            // 添加观看历史
            $this->createView($unique, $video, $duration);
        });
    }

    /**
     * 删除浏览记录（不减少播放量）
     * @param UniqueData $unique
     * @param array $viewIds
     * @return void
     */
    public function viewDelete(UniqueData $unique, array $viewIds): void
    {
        VideoView::where('unique_id', $unique->getUnique())
            ->whereIn('id', $viewIds)
            ->delete();
    }

    /**
     * 获取浏览记录列表
     * @param int $uniqueId
     * @param bool|null $isMovies
     * @param int $skip
     * @param int $take
     * @return Collection
     */
    public function viewList(int $uniqueId, ?bool $isMovies, int $skip, int $take): Collection
    {
        $videoType = $this->getVideoType($isMovies);
        return VideoView::where('unique_id', $uniqueId)
            ->withWhereHas('video', fn($q) => $q->whereIn('type', $videoType))
            ->orderByDesc('updated_at')
            ->with('video')
            ->skip($skip)
            ->take($take)
            ->get();
    }

    /**
     * 获取视频类型
     * @param bool|null $isMovies
     * @return array
     */
    private function getVideoType(?bool $isMovies): array
    {
        return match ($isMovies) {
            false => [VideoType::Short->value],
            true => [VideoType::Movie->value, VideoType::Tv->value],
            default => [VideoType::Short->value, VideoType::Movie->value, VideoType::Tv->value]
        };
    }

    /**
     * 增加用户的观看时长
     * @param int $userId
     * @param int $duration
     * @param array $video
     * @return void
     * @noinspection PhpComposerExtensionStubsInspection
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    private function countViewingTime(int $userId, array $video, int $duration): void
    {
        $redisKey = RedisKey::USER_WATCH_TIME . $userId;
        $secondsUntilMidnight = now()->diffInSeconds(now()->endOfDay());
        try {
            $redis = redis();
            // 如果键不存在，则初始化键并设置到午夜的过期时间
            if (!$redis->exists($redisKey)) {
                $redis->hmset($redisKey, ['total' => 0, 'appoint' => 0]);
                $redis->expire($redisKey, $secondsUntilMidnight);
            }
            // 增加当天观看总时长
            $redis->hIncrBy($redisKey, 'total', $duration);
            // 如果观看指定剧活动没开启则不记录时长
            $activity = $this->activityService->getActivityByType(
                ActivityType::AppointVideo, false
            );
            if (empty($activity)) {
                return;
            }
            if (in_array($video['id'], $activity->config['appoint']['video_ids'])) {
                $redis->hIncrBy($redisKey, 'appoint', $duration);
            }
        } catch (\RedisException $e) {
            logger()->error('用户观看时长错误', [$e->getMessage(), $e]);
            throw new ShortPlayException(ErrorCode::REDIS_ERROR->value);
        }
    }

    /**
     * 更新观看记录
     * @param Model $videoView
     * @param array $video
     * @param int $duration
     */
    private function updateView(Model $videoView, array $video, int $duration): void
    {
        $videoView->update([
            'num' => $video['item']['sort'],
            'item_id' => $video['item']['id'],
            'play_duration' => $duration,
            'duration' => $video['item']['duration'],
        ]);
    }

    /**
     * 创建观看记录
     * @param UniqueData $unique
     * @param array $video
     * @param int $duration
     */
    private function createView(UniqueData $unique, array $video, int $duration): void
    {
        Db::transaction(function () use ($unique, $video, $duration) {
            VideoView::create([
                'video_id' => $video['id'],
                'play_duration' => $duration,
                'num' => $video['item']['sort'],
                'item_id' => $video['item']['id'],
                'unique_id' => $unique->getUnique(),
                'duration' => $video['item']['duration'],
            ]);
            Video::where('id', $video['id'])->increment('play_count');
        });
    }
}