<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\Enum\ActivityStatus;
use App\Constants\Enum\ActivityType;
use App\Constants\ErrorCode;
use App\Constants\RedisKey;
use App\Exception\ShortPlayException;
use App\Model\Activity;
use App\Model\UserAppointVideo;
use App\Model\UserCoin;
use App\Model\UserCoinDetail;
use App\Model\UserSignIn;
use App\Model\UserWatchTime;
use App\Model\Video;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Listener\DeleteListenerEvent;
use Hyperf\Collection\Collection;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Model;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;
use function FriendsOfHyperf\Lock\lock;
use function Hyperf\Support\now;

class ActivityService
{

    #[Inject]
    protected EventDispatcherInterface $dispatcher;

    /**
     * 活动详情
     * @param int $id
     * @return Activity
     */
    public function adminActivityDetail(int $id): Activity
    {
        return Activity::findOrFail($id);
    }

    /**
     * 获取活动列表
     * @param array $search
     * @param int $pageSize
     * @return LengthAwarePaginatorInterface
     */
    public function activityList(array $search, int $pageSize): LengthAwarePaginatorInterface
    {
        $query = Activity::query()->latest('updated_at');
        if (!empty($search['name'])) {
            $query->where('name', 'like', "%{$search['name']}%");
        }
        return $query->paginate($pageSize);
    }

    /**
     * 修改活动信息
     * @param int $id
     * @param string $name
     * @param string|null $desc
     * @return void
     */
    public function updateActivity(int $id, string $name, ?string $desc): void
    {
        $activity = $this->adminActivityDetail($id);
        $activity->name = $name;
        $activity->desc = $desc;
        $activity->save();
        $this->delActivityCache(ActivityType::from($activity->type)->name);
    }

    /**
     * 修改状态
     * @param int $id
     * @param ActivityStatus $status
     * @return void
     */
    public function statusActivity(int $id, ActivityStatus $status): void
    {
        $activity = $this->adminActivityDetail($id);
        // 如果没有配置模版则不允许开启活动
        if (empty($activity->config) && $status == ActivityStatus::Enable) {
            throw new ShortPlayException(ErrorCode::ACTIVITY_TEMPLATE_EMPTY_NO_ENABLED->value);
        }
        $activity->status = $status->value;
        $activity->save();
        $this->delActivityCache(ActivityType::from($activity->type)->name);
    }

    /**
     * 配置模版
     * @param Activity $activity
     * @param array $config
     * @return void
     */
    public function templateActivity(Activity $activity, array $config): void
    {
        if ($activity->type == ActivityType::AppointVideo->value) {
            // 校验视频id的真实性
            $count = Video::whereIn('id', $config['appoint']['video_ids'])->count();
            if (count($config['appoint']['video_ids']) != $count) {
                throw new ShortPlayException(ErrorCode::DATA_INVALID->value);
            }
        }
        $activity->config = $config;
        $activity->save();
        $this->delActivityCache(ActivityType::from($activity->type)->name);
    }

    /**
     * 删除活动缓存
     * @param string $type
     * @return void
     */
    public function delActivityCache(string $type): void
    {
        $this->dispatcher->dispatch(new DeleteListenerEvent(
            RedisKey::ACTIVITY['listener'], ['type' => ['name' => $type]]
        ));
    }

    /**
     * 根据活动类型获取活动
     * @param ActivityType $type
     * @param bool $isThrow
     * @return ?Model
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function getActivityByType(ActivityType $type, bool $isThrow = true): ?Model
    {
        $activity = $this->getActivityCache($type);
        if (!$activity) {
            return $this->handleActivityError($isThrow, ErrorCode::DATA_INVALID);
        }
        if ($activity->status == ActivityStatus::Disable->value) {
            return $this->handleActivityError($isThrow, ErrorCode::ACTIVITY_CLOSE);
        }
        return $activity;
    }

    /**
     * 用户签到
     * @param int $userId
     * @param Activity|Model $activity
     * @return void
     */
    public function userSignIn(int $userId, Activity|Model $activity): void
    {
        $key = RedisKey::ACTIVITY_SIGN_IN_LOCK . $userId;
        $lockStatus = lock($key, 10)->get(function () use ($userId, $activity) {
            $now = now();
            $date = $now->format('Y-m-d');
            // 判断用户是否已签到
            $sign = UserSIgnIn::where('user_id', $userId)->where('date', $date)->first();
            if ($sign) {
                throw new ShortPlayException(ErrorCode::ACTIVITY_SIGNED_IN->value);
            }
            // 根据当天的星期几计算金币
            $coin = $activity->config['days'][$now->dayOfWeekIso - 1];
            // 执行数据库事务
            Db::transaction(function () use ($userId, $date, $coin, $activity) {
                UserSignIn::create(['user_id' => $userId, 'date' => $date]);
                $this->addUserCoinDetail($userId, $coin, $activity);
                $this->incrementUserTotalCoins($userId, $coin);
            });
        });
        if ($lockStatus === false) {
            throw new ShortPlayException(ErrorCode::LOCKING->value);
        }
    }

    /**
     * 获取用户签到日期表格
     * @param int $userId
     * @param Activity|Model $activity
     * @return array
     */
    public function getUserSignTable(int $userId, Activity|Model $activity): array
    {
        $now = now();
        // 本周开始日期（周一）
        $startOfWeek = $now->copy()->startOfWeek();
        // 本周结束日期（周日）
        $endOfWeek = $now->copy()->endOfWeek();

        // 获取用户本周的签到记录，并转换为日期集合
        $signedDates = UserSignIn::where('user_id', $userId)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->pluck('date', 'date')
            ->toArray();

        $weekDates = [];
        // 生成本周的日期并标识今天
        for ($i = 0; $i < 7; $i++) {
            // 存储每一天的日期和是否为今天的标识
            $date = $startOfWeek->copy()->addDays($i);
            $weekDates[] = [
                'day' => $i + 1,
                'date' => $date->format('m-d'),
                'isToday' => $date->isSameDay($now),
                'coin' => $activity->config['days'][$i],
                'isSign' => in_array($date->format('Y-m-d'), $signedDates),
            ];
        }
        return array_merge(compact('weekDates'), [
            'startTime' => $startOfWeek->format('m-d'),
            'endTime' => $endOfWeek->format('m-d'),
        ]);
    }

    /**
     * 领取看下方剧视频福利
     * @param int $userId
     * @param Activity|Model $activity
     * @return void
     */
    public function receiveVideoBenefit(int $userId, Activity|Model $activity): void
    {
        $watchTime = $this->getUserWatchTime($userId, 'appoint');
        if ($watchTime < $activity->config['appoint']['duration']) {
            // 观看时长不足
            throw new ShortPlayException(ErrorCode::ACTIVITY_WATCH_INSUFFICIENT->value);
        }
        $key = RedisKey::BENEFIT_VIDEO_LOCK . $userId;
        $lockStatus = lock($key, 10)->get(function () use ($userId, $activity) {
            // 判断用户是否已领取福利
            if ($this->isDayReceiveAppointVideo($userId)) {
                // 已领取福利
                throw new ShortPlayException(ErrorCode::ACTIVITY_RECEIVED_BENEFIT->value);
            }
            // 执行数据库事务
            Db::transaction(function () use ($userId, $activity) {
                $coin = $activity->config['appoint']['coin'];
                UserAppointVideo::create([
                    'user_id' => $userId,
                    'date' => now()->format('Y-m-d')
                ]);
                $this->addUserCoinDetail($userId, $coin, $activity);
                $this->incrementUserTotalCoins($userId, $coin);
            });
        });
        if ($lockStatus === false) {
            throw new ShortPlayException(ErrorCode::LOCKING->value);
        }
    }

    /**
     * 获取活动指定的视频
     * @param array $videoIds
     * @return Collection
     */
    public function getAppointVideo(array $videoIds): Collection
    {
        return Video::whereIn('id', $videoIds)->get(Video::$select);
    }

    /**
     * 获取用户当天观看指定指定剧时长
     * @noinspection PhpComposerExtensionStubsInspection
     */
    public function getUserWatchTime(int $userId, string $hashKey): int
    {
        try {
            $key = RedisKey::USER_WATCH_TIME . $userId;
            $watchHash = redis()->hGet($key, $hashKey);
            if (empty($watchHash)) {
                return 0;
            }
            return intval($watchHash);
        } catch (\RedisException $e) {
            logger()->error('获取观看时长失败', [$e->getMessage(), $e]);
            throw new ShortPlayException(ErrorCode::REDIS_ERROR->value);
        }
    }

    /**
     * 获取当天用户是否已领取看指定剧福利
     * @param int $userId
     * @return bool
     */
    public function isDayReceiveAppointVideo(int $userId): bool
    {
        $appoint = UserAppointVideo::where('user_id', $userId)
            ->where('date', now()->format('Y-m-d'))
            ->first(['id']);
        return $appoint != null;
    }

    /**
     * 看剧领金币任务表
     * @param int $userId
     * @param Activity|Model $activity
     * @return array
     */
    public function getWatchTable(int $userId, Activity|Model $activity): array
    {
        $watch = $activity->config['watch'];
        $userWatchTime = $this->getUserWatchTimeLog($userId);
        usort($watch, fn($a, $b) => $a['duration'] - $b['duration']);
        return array_map(function ($item) use ($userWatchTime) {
            return [
                'duration' => $item['duration'],
                'coin' => $item['coin'],
                'is_received' => in_array($item['duration'], $userWatchTime),
            ];
        }, $watch);
    }

    /**
     * 领取看剧领金币福利
     * @param int $userId
     * @param Activity|Model $activity
     * @return void
     */
    public function receiveWatchBenefit(int $userId, Activity|Model $activity): void
    {
        $key = RedisKey::BENEFIT_WATCH_LOCK . $userId;
        lock($key, 10)->get(function () use ($userId, $activity) {
            $watchTime = $this->getUserWatchTime($userId, 'total');
            $items = array_filter($activity->config['watch'], fn($i) => $watchTime >= $i['duration']);
            if (empty($items)) {
                throw new ShortPlayException(ErrorCode::ACTIVITY_NO_RECEIVED_COIN->value);
            }
            $watchTimeLogs = $this->getUserWatchTimeLog($userId);
            $items = array_filter($items, fn($i) => !in_array($i['duration'], $watchTimeLogs));
            if (empty($items)) {
                throw new ShortPlayException(ErrorCode::ACTIVITY_NO_RECEIVED_COIN->value);
            }
            Db::transaction(function () use ($userId, $items, $activity) {
                UserWatchTime::batchInsertUserWatchLog($userId, $items);
                $coin = array_sum(array_column($items, 'coin'));
                $this->addUserCoinDetail($userId, $coin, $activity);
                $this->incrementUserTotalCoins($userId, $coin);
            });
        });
    }

    /**
     * 获取用户看剧领金币任务记录
     * @param int $userId
     * @return array
     */
    public function getUserWatchTimeLog(int $userId): array
    {
        return UserWatchTime::where('user_id', $userId)
            ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
            ->get(['duration'])
            ->pluck('duration')
            ->toArray();
    }

    /**
     * 添加用户金币明细记录
     * @param int $userId
     * @param int $coin
     * @param Activity|Model $activity
     * @return void
     */
    private function addUserCoinDetail(int $userId, int $coin, Activity|Model $activity): void
    {
        UserCoinDetail::create([
            'coin' => $coin,
            'user_id' => $userId,
            'activity_id' => $activity->id,
            'activity_type' => $activity->type,
        ]);
    }

    /**
     * 修改用户总金币
     * @param int $userId
     * @param int $coin
     * @return void
     */
    private function incrementUserTotalCoins(int $userId, int $coin): void
    {
        UserCoin::where('user_id', $userId)->increment('coin_num', $coin);
    }

    /**
     * 获取活动缓存
     * @param ActivityType $type
     * @return Model|null
     */
    #[Cacheable(
        RedisKey::ACTIVITY['key'],
        RedisKey::ACTIVITY['val'],
        RedisKey::ACTIVITY['ttl'],
        RedisKey::ACTIVITY['listener']
    )]
    private function getActivityCache(ActivityType $type): ?Model
    {
        return Activity::where('type', $type->value)->first();
    }

    /**
     * 处理活动获取时的错误逻辑
     * @param bool $isThrow
     * @param ErrorCode $errorCode
     * @return null
     */
    private function handleActivityError(bool $isThrow, ErrorCode $errorCode): null
    {
        if ($isThrow) {
            throw new ShortPlayException($errorCode->value);
        }
        return null;
    }
}