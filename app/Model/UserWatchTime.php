<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use function Hyperf\Support\now;

/**
 * @property int $id
 * @property int $user_id
 * @property int $duration
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class UserWatchTime extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_watch_time';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'duration'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'duration' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 批量插入看剧领金币任务记录表
     * @param int $userId
     * @param array $data
     * @return void
     */
    public static function batchInsertUserWatchLog(int $userId, array $data): void
    {
        self::insert(array_map(function ($item) use ($userId) {
            return [
                'id' => snowflakeId(),
                'user_id' => $userId,
                'duration' => $item['duration'],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }, $data));
    }
}
