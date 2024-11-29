<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use function Hyperf\Support\now;

/**
 * @property int $id
 * @property int $video_id
 * @property int $sort
 * @property string $url
 * @property int $duration
 * @property int $short_count
 * @property int $comment_count
 * @property int $is_view
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 */
class VideoItem extends Model
{

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'video_items';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'video_id', 'sort', 'url', 'duration', 'is_view'];

    /**
     * 查询字段
     * @var array|string[]
     */
    public static array $select = ['id', 'video_id', 'sort', 'duration', 'is_view', 'short_count'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'sort' => 'integer',
        'is_view' => 'integer',
        'video_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'short_count' => 'integer',
        'comment_count' => 'integer',
    ];

    /**
     * 批量添加视频集合
     * @param int $videoId
     * @param int $sort
     * @param array $inserts
     * @return array
     */
    public static function batchInsertVideoItems(int $videoId, int $sort, array $inserts): array
    {
        $items = array_map(function ($item) use ($videoId, &$sort) {
            return [
                'id' => snowflakeId(),
                'video_id' => $videoId,
                'sort' => $sort++,
                'url' => $item['url'],
                'duration' => $item['duration'],
                'is_view' => $item['is_view'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $inserts);
        self::insert($items);
        array_multisort(array_column($items, 'sort'), SORT_ASC, $items);
        return $items;
    }
}
