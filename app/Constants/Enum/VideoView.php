<?php

declare(strict_types=1);

namespace App\Constants\Enum;

use App\Constants\EnumValuesTrait;
use Hyperf\Constants\Annotation\Constants;
use App\Constants\EnumConstantsTrait;
use Hyperf\Constants\Annotation\Message;

#[Constants]
enum VideoView: int
{
    use EnumConstantsTrait, EnumValuesTrait;

    #[Message('no')]
    case No = 0;

    #[Message('yes')]
    case Yes = 1;
}
