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
 * @property int $sex
 * @property string $password
 * @property string $profile
 * @property string $personal_sign
 * @property int $is_member
 * @property string $birthday
 * @property int $preference
 * @property Carbon $member_time
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 */
class User extends Model implements Authenticatable
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'users';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'id', 'mail', 'name', 'sex', 'password', 'profile', 'personal_sign',
        'is_member', 'birthday', 'preference', 'member_time'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'sex' => 'integer',
        'is_member' => 'integer',
        'member_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 获取id
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * 获取用户信息
     * @param $key
     * @return Authenticatable|null
     */
    #[Cacheable(
        prefix: RedisKey::USER['key'],
        value: RedisKey::USER['val'],
        ttl: RedisKey::USER['ttl'],
        listener: RedisKey::USER['listener']
    )]
    public static function retrieveById($key): ?Authenticatable
    {
        return User::find($key);
    }
}
