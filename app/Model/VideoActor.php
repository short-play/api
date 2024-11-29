<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use function Hyperf\Support\now;

/**
 * @property int $id
 * @property int $video_id
 * @property int $actor_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class VideoActor extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'video_actors';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'video_id', 'actor_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'video_id' => 'integer',
        'actor_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 批量插入视频演员关联表
     * @param int $videoId
     * @param array $actorIds
     * @return void
     */
    public static function batchInsertVideoActors(int $videoId, array $actorIds): void
    {
        $batchVideoActor = array_map(function ($actorId) use ($videoId) {
            return [
                'id' => snowflakeId(),
                'actor_id' => $actorId,
                'video_id' => $videoId,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }, $actorIds);
        self::insert($batchVideoActor);
    }
}
