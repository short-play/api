<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;
use function Hyperf\Support\now;

/**
 * @property int $id
 * @property int $ranking_type
 * @property int $unique_id
 * @property int $sort
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Ranking extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'rankings';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'sort', 'unique_id', 'ranking_type'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'sort' => 'integer',
        'unique_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'ranking_type' => 'integer',
    ];

    /**
     * 批量添加榜单信息
     * @param int $type
     * @param array $items
     * @return void
     */
    public static function insertRankings(int $type, array $items): void
    {
        $insertItems = array_map(function ($item) use ($type) {
            return [
                'id' => snowflakeId(),
                'ranking_type' => $type,
                'unique_id' => $item['id'],
                'sort' => $item['sort'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $items);
        self::insert($insertItems);
    }

    /**
     * 视频
     * @return BelongsTo
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, 'unique_id');
    }

    /**
     * 标签
     * @return BelongsTo
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'unique_id');
    }
}
