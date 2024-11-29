<?php

declare(strict_types=1);

namespace App\Constants\Enum;

use App\Constants\EnumConstantsTrait;
use App\Constants\EnumValuesTrait;
use Hyperf\Constants\Annotation\Constants;
use Hyperf\Constants\Annotation\Message;

#[Constants]
enum ActivityType: int
{
    use EnumConstantsTrait, EnumValuesTrait;

    #[Message('sign')]
    case Sign = 1;

    #[Message('watch_duration')]
    case WatchDuration = 2;

    #[Message('appoint_video')]
    case AppointVideo = 3;
}
