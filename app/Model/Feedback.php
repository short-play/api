<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $unique_id
 * @property string $title
 * @property string $desc
 * @property array $pic_json
 * @property string $link
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Feedback extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'feedbacks';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'id', 'unique_id', 'title', 'desc', 'pic_json', 'link', 'status'
    ];

    /**
     * 查询字段
     * @var array|string[]
     */
    public static array $select = [
        'id', 'title', 'desc', 'pic_json', 'link', 'status', 'created_at'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'unique_id' => 'integer',
        'status' => 'integer',
        'pic_json' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 用户
     * @noinspection PhpUndefinedMethodInspection
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'unique_id')
            ->select(['id', 'name'])
            ->withTrashed();
    }
}
