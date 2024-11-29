<?php

declare(strict_types=1);

namespace App\Constants;

class RedisKey
{

    // 用户注册验证码
    public const USER_REGISTER_CODE = 'USER:REGISTER:CODE:';

    // 用户忘记密码
    public const USER_FORGET_CODE = 'USER:FORGET:CODE:';

    // 管理员忘记密码
    public const ADMIN_FORGET_CODE = 'ADMIN:FORGET:CODE:';

    // 订单下单锁
    public const ORDER_LOCK = 'ORDER:CREATE:';

    // 修改订单状态锁
    public const ORDER_STATUS_LOCK = 'ORDER:STATUS:';

    // 创建管理员锁
    public const ADMIN_CREATE_LOCK = 'ADMIN:CREATE:';

    // 添加榜单锁
    public const RANKING_LOCK = 'RANKING:CREATE:';

    // 给用户发送消息锁
    public const MESSAGE_SEND_LOCK = 'MESSAGE:SEND:';

    // 用户签到锁
    public const ACTIVITY_SIGN_IN_LOCK = 'ACTIVITY:SIGN_IN:';

    // 看下方剧福利锁
    public const BENEFIT_VIDEO_LOCK = 'BENEFIT:VIDEO:';

    // 看剧领金币福利锁
    public const BENEFIT_WATCH_LOCK = 'BENEFIT:WATCH:';

    // 用户观看时长
    public const USER_WATCH_TIME = 'USER:WATCH_TIME:';

    // 用户观看历史锁
    public const USER_VIEW_HISTORY = 'USER:VIEW_HISTORY:';

    // 标签缓存
    public const TAG = [
        'key' => 'tags', 'ttl' => 86400, 'listener' => 'tags-update'
    ];

    // 用户
    public const USER = [
        'key' => 'user', 'val' => '#{key}', 'ttl' => 86400, 'listener' => 'user-update'
    ];

    // 视频详情缓存键
    public const VIDEO_DETAIL = [
        'key' => 'video:detail', 'val' => '#{id}', 'ttl' => 3600, 'aheadSeconds' => 300
    ];

    // 管理员缓存
    public const ADMIN = [
        'key' => 'admin', 'val' => '#{key}', 'ttl' => 86400, 'listener' => 'admin-update'
    ];

    // 设备
    public const DEVICE = [
        'key' => 'device', 'val' => '#{device}', 'ttl' => -1, 'listener' => 'device-update'
    ];

    // 活动
    public const ACTIVITY = [
        'key' => 'activity', 'val' => '#{type.name}', 'ttl' => 86400, 'listener' => 'activity-update'
    ];

    // 搜索历史
    public const SEARCH_HISTORY = [
        'key' => 'history', 'val' => '#{data.unique}', 'ttl' => 86400, 'listener' => 'history-update'
    ];

    // 协议
    public const AGREEMENT = [
        'key' => 'agreement', 'val' => '#{type}:#{language}', 'ttl' => -1, 'listener' => 'agreement-update'
    ];
}