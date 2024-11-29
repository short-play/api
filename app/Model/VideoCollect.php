<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $video_id
 * @property int $unique_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class VideoCollect extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'video_collects';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'video_id', 'unique_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'video_id' => 'integer',
        'unique_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 用户
     * @return BelongsTo
     * @noinspection PhpUndefinedMethodInspection
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'unique_id')
            ->select(['id', 'name'])
            ->withTrashed();
    }

    /**
     * 设备
     * @return BelongsTo
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'unique_id')
            ->select(['id', 'device']);
    }

    /**
     * 视频
     * @return BelongsTo
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class)->select(Video::$select);
    }

    /**
     * 浏览记录
     * @return BelongsTo
     */
    public function view(): BelongsTo
    {
        return $this->belongsTo(VideoView::class, 'video_id', 'video_id');
    }

    /**
     * 批量插入追剧数据
     *
     * @param array $videoIds
     * @param int $uniqueId
     * @return void
     */
    public static function insertVideoCollects(array $videoIds, int $uniqueId): void
    {
        $nowDateTime = Carbon::now();
        $insertValues = array_map(function ($item) use ($nowDateTime, $uniqueId) {
            return [
                'id' => snowflakeId(),
                'video_id' => $item,
                'unique_id' => $uniqueId,
                'created_at' => $nowDateTime,
                'updated_at' => $nowDateTime,
            ];
        }, $videoIds);
        self::query()->insert($insertValues);
    }
}
