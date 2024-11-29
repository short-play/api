<?php

use App\Constants\Enum\ActivityStatus;
use App\Constants\Enum\ActivityType;
use App\Constants\Enum\AdminRole;
use App\Constants\Enum\Agreement;
use App\Constants\Enum\FeedbackStatus;
use App\Constants\Enum\OrderStatus;
use App\Constants\Enum\Preference;
use App\Constants\Enum\RankingType;
use App\Constants\Enum\TagType;
use App\Constants\Enum\UserType;
use App\Constants\Enum\VideoFinish;
use App\Constants\Enum\VideoType;
use App\Constants\Enum\VideoView;

return [
    VideoType::class => [
        'short' => '短剧',
        'movie' => '电影',
        'tv' => '电视剧',
    ],
    RankingType::class => [
        'recommended' => '推荐榜',
        'new' => '新剧',
        'short_search' => '短剧热搜榜',
        'tag_search' => '分类热搜榜',
        'new_short_search' => '短剧新剧榜'
    ],
    Preference::class => [
        'boy' => '男生',
        'girl' => '女生',
    ],
    TagType::class => [
        'new' => '新剧',
        'hot' => '爆剧'
    ],
    AdminRole::class => [
        'super_admin' => '超管',
        'admin' => '管理员'
    ],
    Agreement::class => [
        'user_agreement' => '用户协议',
        'privacy_agreement' => '隐私政策'
    ],
    OrderStatus::class => [
        'pending' => '待支付',
        'paid' => '已支付'
    ],
    VideoFinish::class => [
        'complete' => '完结',
        'un_complete' => '未完结',
    ],
    VideoView::class => [
        'no' => '否',
        'yes' => '是'
    ],
    FeedbackStatus::class => [
        'unresolved' => '未解决',
        'resolve' => '已解决'
    ],
    UserType::class => [
        'user' => '用户',
        'device' => '设备'
    ],
    ActivityType::class => [
        'sign' => '签到',
        'watch_duration' => '看剧',
        'appoint_video' => '观看指定剧'
    ],
    ActivityStatus::class => [
        'enable' => '启用',
        'disable' => '禁用',
    ]
];
