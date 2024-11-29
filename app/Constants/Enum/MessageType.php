<?php

declare(strict_types=1);

namespace App\Constants\Enum;

use Hyperf\Constants\Annotation\Constants;
use App\Constants\EnumConstantsTrait;

#[Constants]
enum MessageType: int
{
    use EnumConstantsTrait;

    case Reply = 1;

    case Likes = 2;

    case Notice = 3;
}
