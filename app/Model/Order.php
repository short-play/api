<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;

/**
 * @property int $id
 * @property string $no
 * @property int $user_id
 * @property string $amount
 * @property int $status
 * @property Carbon|null $pay_time
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Order extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'orders';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'id', 'no', 'user_id', 'amount', 'status', 'pay_time'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'status' => 'integer',
        'pay_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 关联用户
     * @noinspection PhpUndefinedMethodInspection
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed()->select(['id', 'name']);
    }
}
