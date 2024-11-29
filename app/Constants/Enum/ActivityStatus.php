<?php

declare(strict_types=1);

namespace App\Constants\Enum;

use Hyperf\Constants\Annotation\Constants;
use App\Constants\EnumConstantsTrait;
use Hyperf\Constants\Annotation\Message;

#[Constants]
enum ActivityStatus: int
{
    use EnumConstantsTrait;

    #[Message('disable')]
    case Disable = 0;

    #[Message('enable')]
    case Enable = 1;
}
