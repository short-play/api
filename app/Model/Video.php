<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\Database\Model\SoftDeletes;

/**
 * @property int $id
 * @property string $title
 * @property string $image_url
 * @property int $type
 * @property int $tag_type
 * @property int $item_id
 * @property string $desc
 * @property int $preference
 * @property int $num
 * @property int $play_count
 * @property int $collect_count
 * @property int $interact_count
 * @property int $is_cat
 * @property int $is_finish
 * @property string $rating
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 */
class Video extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'videos';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'id', 'type', 'item_id', 'preference', 'collect_count', 'play_count',
        'title', 'image_url', 'tag_type', 'desc', 'rating', 'num',
        'interact_count', 'is_cat', 'is_finish'
    ];

    /**
     * 查询字段
     * @var array|string[]
     */
    public static array $select = [
        'id', 'image_url', 'type', 'title', 'num', 'rating', 'is_finish', 'tag_type'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'num' => 'integer',
        'type' => 'integer',
        'is_cat' => 'integer',
        'item_id' => 'integer',
        'play_count' => 'integer',
        'is_finish' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'collect_count' => 'integer',
        'interact_count' => 'integer',
    ];

    public function item(): HasOne
    {
        return $this->hasOne(VideoItem::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(VideoItem::class)->orderBy('sort');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'video_tags')->select(['value']);
    }

    public function actor(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class, 'video_actors')->select(['value']);
    }
}
