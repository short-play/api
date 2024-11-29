<?php

declare(strict_types=1);

namespace App\Constants\Enum;

use Hyperf\Constants\Annotation\Constants;
use App\Constants\EnumConstantsTrait;

#[Constants]
enum MessageStatus: int
{
    use EnumConstantsTrait;

    case Unread = 0;

    case Read = 1;
}
