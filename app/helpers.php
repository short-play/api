<?php

declare(strict_types=1);

/**
 * 自定义函数
 */

use App\Constants\ErrorCode;
use App\Exception\ShortPlayException;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Snowflake\IdGeneratorInterface;
use Psr\Container\ContainerInterface;
use Qbhy\HyperfAuth\Authenticatable;
use Random\RandomException;
use function Hyperf\Config\config;

/**
 * 获取容器实例
 */
if (!function_exists('container')) {
    function container(): ContainerInterface
    {
        return ApplicationContext::getContainer();
    }
}

/**
 * 控制台日志
 */
if (!function_exists('stdLog')) {
    function stdLog()
    {
        return container()->get(StdoutLoggerInterface::class);
    }
}

/**
 * 文件日志
 */
if (!function_exists('logger')) {
    function logger()
    {
        return container()->get(LoggerFactory::class)->get();
    }
}

/**
 * 判断是否生产环境
 */
if (!function_exists('isProduction')) {
    function isProduction(): bool
    {
        return config('app_env') == 'online'
            || config('app_env') == 'production'
            || config('app_env') == 'pre'
            || config('app_env') == 'prod';
    }
}

/**
 * 分布式雪花id生成
 */
if (!function_exists('snowflakeId')) {
    function snowflakeId(): int
    {
        return container()->get(IdGeneratorInterface::class)->generate();
    }
}

/**
 * 获取用户信息
 */
if (!function_exists('user')) {
    function user(): Authenticatable
    {
        return auth()->user();
    }
}

/**
 * 获取管理员信息
 */
if (!function_exists('admin')) {
    function admin(): Authenticatable
    {
        return auth('admin')->user();
    }
}

/**
 * 用户id
 */
if (!function_exists('userId')) {
    function userId(): int
    {
        return (int)user()->id;
    }
}

/**
 * redis实例
 */
if (!function_exists('redis')) {
    function redis(): Hyperf\Redis\Redis
    {
        return container()->get(Hyperf\Redis\Redis::class);
    }
}

/**
 * 生成随机数
 */
if (!function_exists('genRandomInt')) {
    function genRandomInt(int $min, int $max): int
    {
        try {
            return random_int($min, $max);
        } catch (RandomException $e) {
            logger()->error('生成随机数错误', [$e->getMessage(), $e->getTrace()]);
            throw new ShortPlayException(ErrorCode::GEN_RANDOM->value);
        }
    }
}