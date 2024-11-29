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
        'short' => 'short',
        'movie' => 'movie',
        'tv' => 'tv',
    ],
    RankingType::class => [
        'recommended' => 'recommended',
        'new' => 'new',
        'short_search' => 'short search',
        'tag_search' => 'tag search',
        'new_short_search' => 'new short search'
    ],
    Preference::class => [
        'boy' => 'boy',
        'girl' => 'girl',
    ],
    TagType::class => [
        'new' => 'new',
        'hot' => 'hot'
    ],
    AdminRole::class => [
        'super_admin' => 'super',
        'admin' => 'admin'
    ],
    Agreement::class => [
        'user_agreement' => '用户协议',
        'privacy_agreement' => '隐私政策'
    ],
    OrderStatus::class => [
        'pending' => 'pending',
        'paid' => 'paid'
    ],
    VideoFinish::class => [
        'complete' => 'Complete',
        'un_complete' => 'UnComplete',
    ],
    VideoView::class => [
        'no' => 'no',
        'yes' => 'yes'
    ],
    FeedbackStatus::class => [
        'unresolved' => 'unresolved',
        'resolve' => 'resolve'
    ],
    UserType::class => [
        'user' => 'user',
        'device' => 'device'
    ],
    ActivityType::class => [
        'sign' => 'sign',
        'watch_duration' => 'watch duration',
        'appoint_video' => 'appoint video'
    ],
    ActivityStatus::class => [
        'disable' => 'disable',
        'enable' => 'enable'
    ]
];
