<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use function Hyperf\Support\now;

/**
 * @property int $id
 * @property int $video_id
 * @property int $tag_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class VideoTag extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'video_tags';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'video_id', 'tag_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'tag_id' => 'integer',
        'video_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 批量插入视频和标签关联表
     * @param int $videoId
     * @param array $tagIds
     * @return void
     */
    public static function batchInsertVideoTags(int $videoId, array $tagIds): void
    {
        $batchVideoTag = array_map(function ($tagId) use ($videoId) {
            return [
                'id' => snowflakeId(),
                'tag_id' => $tagId,
                'video_id' => $videoId,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }, $tagIds);
        self::insert($batchVideoTag);
    }
}
