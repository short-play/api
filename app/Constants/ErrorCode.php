<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\Annotation\Constants;
use Hyperf\Constants\Annotation\Message;
use Hyperf\Constants\EnumConstantsTrait;

#[Constants]
enum ErrorCode: int
{
    use EnumConstantsTrait;

    // 用户
    #[Message('message.user.not_found')]
    case USER_NOT_FOUND = 10001;

    #[message('message.user.disable')]
    case USER_DISABLE = 10002;

    #[message('message.user.exists')]
    case USER_EXISTS = 10003;

    #[message('message.user.activated')]
    case USER_ACTIVATED = 10004;

    #[message('message.user.not_activated')]
    case USER_NOT_ACTIVATED = 10005;

    // 管理员
    #[Message('message.admin.not_found')]
    case ADMIN_NOT_FOUND = 40001;

    #[Message('message.admin.disabled')]
    case ADMIN_DISABLED = 40002;

    #[message('message.admin.exists')]
    case ADMIN_EXISTS = 40003;

    #[Message('message.admin.no_operate_self')]
    case ADMIN_NO_OPERATE_SELF = 40004;

    // config配置错误
    #[Message('message.tag.apply_video')]
    case TAG_APPLY_VIDEO = 20000;

    #[Message('message.actor.apply_video')]
    case ACTOR_APPLY_VIDEO = 20001;

    #[Message('message.tag.invalid')]
    case TAG_INVALID = 20002;

    #[Message('message.actor.invalid')]
    case ACTOR_INVALID = 20003;

    #[Message('message.tag.apply_ranking')]
    case TAG_APPLY_RANKING = 20004;

    // 视频错误信息
    #[Message('message.video.tag_type_limit')]
    case TAG_TYPE_LIMIT = 50001;

    #[Message('message.video.title_exists')]
    case VIDEO_TITLE_EXISTS = 50002;

    #[Message('message.video.rating_empty')]
    case VIDEO_RATING_EMPTY = 50003;

    #[Message('message.video.movie_num_limit')]
    case VIDEO_MOVIE_NUM_LIMIT = 50004;

    #[Message('message.video.num_lt_update')]
    case VIDEO_NUM_LT_UPDATE = 50005;

    #[Message('message.video.update_gt_num')]
    case VIDEO_UPDATE_GT_NUM = 50006;

    #[Message('message.video.removal')]
    case VIDEO_REMOVAL = 50007;

    #[Message('message.video.not_found')]
    case VIDEO_NOT_FOUND = 50008;

    #[Message('message.video.item_not_found')]
    case VIDEO_ITEM_NOT_FOUND = 50009;

    #[Message('message.video.add_ranking')]
    case VIDEO_ADD_RANKING = 50010;

    #[Message('message.video.add_activity')]
    case VIDEO_ADD_ACTIVITY = 50011;

    // 订单
    #[Message('message.order.paid')]
    case ORDER_PAID = 60001;

    // 消息错误
    #[Message('message.message.send_user')]
    case MESSAGE_SEND_USER = 70001;

    // 活动错误
    #[Message('message.activity.close')]
    case ACTIVITY_CLOSE = 80001;

    #[Message('message.activity.signed_in')]
    case ACTIVITY_SIGNED_IN = 80002;

    #[Message('message.activity.template_empty_no_enabled')]
    case ACTIVITY_TEMPLATE_EMPTY_NO_ENABLED = 80003;

    #[Message('message.activity.watch_insufficient')]
    case ACTIVITY_WATCH_INSUFFICIENT = 80004;

    #[Message('message.activity.received_benefit')]
    case ACTIVITY_RECEIVED_BENEFIT = 80005;

    #[Message('message.activity.no_received_coin')]
    case ACTIVITY_NO_RECEIVED_COIN = 80006;

    // 公共异常
    #[Message('message.global.password_error')]
    case PASSWORD_ERROR = 30000;

    #[message('message.global.gen_random')]
    case GEN_RANDOM = 30001;

    #[message('message.global.code_wrong')]
    case CODE_WRONG = 30002;

    #[message('message.global.code_expired')]
    case CODE_EXPIRED = 30003;

    #[message('message.global.data_invalid')]
    case DATA_INVALID = 30006;

    #[message('message.global.not_logged')]
    case NOT_LOGGED = 30007;

    #[message('message.global.device_empty')]
    case DEVICE_EMPTY = 30008;

    #[message('message.global.code_been_send')]
    case CODE_BEEN_SEND = 30009;

    #[message('message.global.mail_exists')]
    case MAIL_EXISTS = 30010;

    #[message('message.global.redis_error')]
    case REDIS_ERROR = 30011;

    #[message('message.global.unauthorized')]
    case UNAUTHORIZED = 30012;

    #[message('message.global.locking')]
    case LOCKING = 30013;

    #[message('message.global.exists')]
    case EXISTS = 30014;

    #[message('message.global.data_limit_size')]
    case DATA_LIMIT_SIZE = 30015;
}
