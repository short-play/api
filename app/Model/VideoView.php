<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $video_id
 * @property int $item_id
 * @property int $unique_id
 * @property int $num
 * @property int $play_duration
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class VideoView extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'video_views';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'id', 'video_id', 'item_id', 'unique_id', 'play_duration', 'num', 'duration'
    ];

    /**
     * 查询字段
     * @var array|string[]
     */
    public static array $select = [
        'id', 'video_id', 'item_id', 'play_duration', 'num', 'duration', 'updated_at'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'num' => 'integer',
        'item_id' => 'integer',
        'video_id' => 'integer',
        'duration' => 'integer',
        'unique_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'play_duration' => 'integer',
    ];

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class)->select(Video::$select);
    }
}
