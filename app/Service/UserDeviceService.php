<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\ProjectConfig;
use App\Model\User;
use App\Model\UserDevice;
use App\Model\Video;
use App\Model\VideoCollect;
use App\Model\VideoItem;
use App\Model\VideoLike;
use App\Model\VideoView;
use Carbon\Carbon;
use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Query\Builder as QueryBuilder;
use Hyperf\DbConnection\Db;
use function Hyperf\Support\now;

class UserDeviceService
{

    public function __construct(protected int $deviceId, protected User $user)
    {
    }

    /**
     * 开始执行数据同步
     * @return void
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function run(): void
    {
        $endTime = now();
        $startTime = now()->subDays(ProjectConfig::SYNC_SUB_DAY);
        if ($userDevice = $this->getUserDeviceFirst()) {
            $startTime = $userDevice->merge_time;
        }
        // 同步浏览记录、收藏、点赞
        Db::transaction(function () use ($startTime, $endTime) {
            $this->syncVideoView($startTime, $endTime);
            $this->syncupdateVideoView($startTime, $endTime);
            $this->syncVideoFollow($startTime, $endTime);
            $this->syncVideoLikes($startTime, $endTime);
            UserDevice::create([
                'user_id' => $this->user->id,
                'device_id' => $this->deviceId,
                'merge_time' => $endTime,
            ]);
        });
    }

    /**
     * 同步没有的浏览记录
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @return void
     */
    protected function syncVideoView(Carbon $startTime, Carbon $endTime): void
    {
        $joinFun = fn($j) => $j->on('a.video_id', 'video_views.video_id')
            ->where('a.unique_id', $this->user->id);
        // 初始化搜索条件
        $query = VideoView::leftJoin('video_views as a', $joinFun)
            ->whereBetween('video_views.created_at', [$startTime, $endTime])
            ->where('video_views.unique_id', $this->deviceId)
            ->whereNull('a.id')
            ->select(['video_views.*']);
        // 执行同步
        if ($views = $this->handleOrmResData($query)) {
            VideoView::insert($views);
            $videoIds = array_column($views, 'video_id');
            Video::whereIn('id', $videoIds)->increment('play_count');
        }
    }

    /**
     * 同步浏览记录更新观看，如果设备的观看时间大于用户的观看时间则更新
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @return void
     */
    protected function syncUpdateVideoView(Carbon $startTime, Carbon $endTime): void
    {
        $joinFun = function ($j) use ($startTime, $endTime) {
            $j->where('a.updated_at', '>', Db::raw('video_views.updated_at'))
                //->whereBetween('a.updated_at', [$startTime, $endTime])
                ->on('a.video_id', 'video_views.video_id')
                ->where('a.unique_id', $this->deviceId);
        };
        // 初始化搜索条件
        Db::table('video_views')->join('video_views as a', $joinFun)
            ->where('video_views.unique_id', $this->user->id)
            ->update([
                'video_views.num' => Db::raw('a.num'),
                'video_views.item_id' => Db::raw('a.item_id'),
                'video_views.duration' => Db::raw('a.duration'),
                'video_views.updated_at' => Db::raw('a.updated_at'),
                'video_views.play_duration' => Db::raw('a.play_duration')
            ]);
    }

    /**
     * 同步追剧
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @return void
     */
    protected function syncVideoFollow(Carbon $startTime, Carbon $endTime): void
    {
        $joinFun = fn($j) => $j->on('a.video_id', 'video_collects.video_id')
            ->where('a.unique_id', $this->user->id);
        // 初始化搜索条件
        $query = VideoCollect::leftJoin('video_collects as a', $joinFun)
            ->whereBetween('video_collects.created_at', [$startTime, $endTime])
            ->where('video_collects.unique_id', $this->deviceId)
            ->whereNull('a.id')
            ->select(['video_collects.*']);
        // 执行同步
        if ($follows = $this->handleOrmResData($query)) {
            VideoCollect::insert($follows);
            $videoIds = array_column($follows, 'video_id');
            Video::whereIn('id', $videoIds)->incrementEach(
                ['collect_count' => 1, 'interact_count' => 1]
            );
        }
    }

    /**
     * 同步点赞数据
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @return void
     */
    protected function syncVideoLikes(Carbon $startTime, Carbon $endTime): void
    {
        $joinFun = fn($j) => $j->on('a.video_id', 'video_likes.video_id')
            ->where('a.item_id', Db::raw('video_likes.item_id'))
            ->where('a.unique_id', $this->user->id);
        // 初始化搜索条件
        $query = VideoLike::leftJoin('video_likes as a', $joinFun)
            ->whereBetween('video_likes.created_at', [$startTime, $endTime])
            ->where('video_likes.unique_id', $this->deviceId)
            ->whereNull('a.id')
            ->select(['video_likes.*']);
        // 执行同步
        if ($insertLikes = $this->handleOrmResData($query)) {
            // 总互动数+1,点赞数+1 写入数据
            VideoLike::insert($insertLikes);
            $itemIds = array_column($insertLikes, 'item_id');
            $videoIds = array_column($insertLikes, 'video_id');
            VideoItem::whereIn('id', $itemIds)->increment('short_count');
            Video::whereIn('id', $videoIds)->increment('interact_count');
        }
    }

    /**
     * 拼接条件,处理获取的数据
     * @param Builder|QueryBuilder $query
     * @return array
     */
    protected function handleOrmResData(Builder|QueryBuilder $query): array
    {
        // 获取数据,处理id和唯一id
        return array_map(function ($item) {
            $item['id'] = snowflakeId();
            $item['unique_id'] = $this->user->id;
            return $item;
        }, $query->get()->toArray());
    }

    /**
     * 根据创建时间降序获取一条
     * @return Model|null
     */
    public function getUserDeviceFirst(): ?Model
    {
        return UserDevice::where('user_id', $this->user->id)
            ->where('device_id', $this->deviceId)
            ->orderByDesc('created_at')
            ->first(['merge_time']);
    }
}