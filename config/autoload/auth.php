<?php

declare(strict_types=1);

/**
 * This file is part of qbhy/hyperf-auth.
 *
 * @link     https://github.com/qbhy/hyperf-auth
 * @document https://github.com/qbhy/hyperf-auth/blob/master/README.md
 * @contact  qbhy0715@qq.com
 * @license  https://github.com/qbhy/hyperf-auth/blob/master/LICENSE
 */

use Qbhy\HyperfAuth\HyperfRedisCache;
use Qbhy\HyperfAuth\Provider\EloquentProvider;
use Qbhy\SimpleJwt\Encoders;
use Qbhy\SimpleJwt\EncryptAdapters as Encrypter;
use function Hyperf\Support\env;
use function Hyperf\Support\make;

return [
    'default' => [
        'guard' => 'api',
        'provider' => 'users',
    ],
    'guards' => [
        'api' => [
            'driver' => Qbhy\HyperfAuth\Guard\JwtGuard::class,
            'provider' => 'users',

            // 以下是 simple-jwt 配置必填jwt 服务端身份标识
            'secret' => env('API_JWT_SECRET'),

            // 可选配置jwt 默认头部token使用的字段
            'header_name' => env('JWT_HEADER_NAME', 'Authorization'),

            // 可选配置 jwt 生命周期，单位秒，默认一年
            'ttl' => (int)env('API_JWT_TTL', 31536000),

            /*
             * 可选配置
             * 允许过期多久以内的 token 进行刷新，单位秒，默认一周
             */
            'refresh_ttl' => (int)env('API_JWT_REFRESH_TTL', 60 * 60 * 24 * 7),

            // 可选配置默认使用的加密类
            'default' => Encrypter\SHA1Encrypter::class,

            // 可选配置 加密类必须实现 Qbhy\SimpleJwt\Interfaces\Encrypter 接口
            'drivers' => [
                Encrypter\PasswordHashEncrypter::alg() => Encrypter\PasswordHashEncrypter::class,
                Encrypter\CryptEncrypter::alg() => Encrypter\CryptEncrypter::class,
                Encrypter\SHA1Encrypter::alg() => Encrypter\SHA1Encrypter::class,
                Encrypter\Md5Encrypter::alg() => Encrypter\Md5Encrypter::class,
            ],

            // 可选配置编码类
            'encoder' => new Encoders\Base64UrlSafeEncoder(),
            // 'encoder' => new Encoders\Base64Encoder(),

            // 可选配置缓存类
            // 'cache' => new \Doctrine\Common\Cache\FilesystemCache(sys_get_temp_dir()),
            // 如果需要分布式部署，请选择 redis 或者其他支持分布式的缓存驱动
            'cache' => function () {
                return make(HyperfRedisCache::class);
            },

            // 可选配置 缓存前缀
            'prefix' => env('API_JWT_PREFIX', 'api'),
        ],
        'admin' => [
            'driver' => Qbhy\HyperfAuth\Guard\JwtGuard::class,
            'provider' => 'admins',

            // 以下是 simple-jwt 配置必填jwt 服务端身份标识
            'secret' => env('ADMIN_JWT_SECRET'),

            // 可选配置jwt 默认头部token使用的字段
            'header_name' => env('JWT_HEADER_NAME', 'Authorization'),

            // 可选配置 jwt 生命周期，单位秒，默认一小时
            'ttl' => (int)env('ADMIN_JWT_TTL', 3600),

            /*
             * 可选配置
             * 允许过期多久以内的 token 进行刷新，单位秒，默认一周
             */
            'refresh_ttl' => (int)env('ADMIN_JWT_REFRESH_TTL', 60 * 60 * 24 * 7),

            // 可选配置默认使用的加密类
            'default' => Encrypter\SHA1Encrypter::class,

            // 可选配置 加密类必须实现 Qbhy\SimpleJwt\Interfaces\Encrypter 接口
            'drivers' => [
                Encrypter\PasswordHashEncrypter::alg() => Encrypter\PasswordHashEncrypter::class,
                Encrypter\CryptEncrypter::alg() => Encrypter\CryptEncrypter::class,
                Encrypter\SHA1Encrypter::alg() => Encrypter\SHA1Encrypter::class,
                Encrypter\Md5Encrypter::alg() => Encrypter\Md5Encrypter::class,
            ],

            // 可选配置编码类
            'encoder' => new Encoders\Base64UrlSafeEncoder(),
            // 'encoder' => new Encoders\Base64Encoder(),

            // 可选配置缓存类
            // 'cache' => new \Doctrine\Common\Cache\FilesystemCache(sys_get_temp_dir()),
            // 如果需要分布式部署，请选择 redis 或者其他支持分布式的缓存驱动
            'cache' => function () {
                return make(HyperfRedisCache::class);
            },

            // 可选配置 缓存前缀
            'prefix' => env('API_JWT_PREFIX', 'admin'),
        ],
    ],
    'providers' => [
        'users' => [
            'driver' => EloquentProvider::class,
            'model' => App\Model\User::class,
        ],
        'admins' => [
            'driver' => EloquentProvider::class,
            'model' => App\Model\Admin::class,
        ],
    ],
];
