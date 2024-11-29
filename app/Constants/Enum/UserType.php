<?php

declare(strict_types=1);

namespace App\Constants\Enum;

use App\Constants\EnumValuesTrait;
use Hyperf\Constants\Annotation\Constants;
use App\Constants\EnumConstantsTrait;
use Hyperf\Constants\Annotation\Message;

#[Constants]
enum UserType: int
{
    use EnumConstantsTrait, EnumValuesTrait;

    #[Message('user')]
    case USER = 1;

    #[Message('device')]
    case Device = 2;
}
