<?php

declare(strict_types=1);

namespace App\Constants;

class ProjectConfig
{

    // 首次同步当前时间前30天
    public const SYNC_SUB_DAY = 30;

    // 会员价钱
    public const MEMBER_AMOUNT = 15;

    // 头像上传文件目录
    public const PIC_DIR = 'profile';

    // 反馈上传图片目录
    public const FEEDBACK_DIR = 'feedback';

    // 视频目录
    public const VIDEO_DIR = 'video';

    // 视频封面目录
    public const VIDEO_COVER_DIR = 'video_cover';

    // 上传文件url过期时间
    public const UPLOAD_EXPIRE = 60;

    // 猜你喜欢列表数量
    public const VIDEO_LIKE_LIST_SIZE = 9;

    // 更多好剧推荐列表数量
    public const VIDEO_GOOD_LIST_SIZE = 30;

    // 推荐榜限制条数
    public const RECOMMENDED_SIZE = 30;

    // 热搜榜限制条数
    public const RANKING_SEARCH_SIZE = 6;

    // 搜索历史最大限制条数
    public const SEARCH_HISTORY_MAX_LIMIT = 6;

    // 验证码过期时间
    public const CODE_EXPIRE_SECOND = 60 * 5;
}