<?php

declare(strict_types=1);

namespace App\Constants\Enum;

use App\Constants\EnumValuesTrait;
use Hyperf\Constants\Annotation\Constants;
use App\Constants\EnumConstantsTrait;
use Hyperf\Constants\Annotation\Message;

#[Constants]
enum VideoFinish: int
{
    use EnumConstantsTrait, EnumValuesTrait;

    #[Message('un_complete')]
    case UnComplete = 0;

    #[Message('complete')]
    case Complete = 1;
}
