<?php

declare(strict_types=1);

namespace App\Model;

use App\Constants\RedisKey;
use Carbon\Carbon;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Database\Model\SoftDeletes;
use Qbhy\HyperfAuth\Authenticatable;

/**
 * @property int $id
 * @property string $mail
 * @property string $name
 * @property string $password
 * @property int $role
 * @property string $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Admin extends Model implements Authenticatable
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'admins';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'mail', 'name', 'password', 'role'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'role' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function getId(): int
    {
        return $this->id;
    }

    #[Cacheable(
        prefix: RedisKey::ADMIN['key'],
        value: RedisKey::ADMIN['val'],
        ttl: RedisKey::ADMIN['ttl'],
        listener: RedisKey::ADMIN['listener']
    )]
    public static function retrieveById($key): ?Authenticatable
    {
        return self::find($key);
    }
}
