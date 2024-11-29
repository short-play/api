<?php

declare(strict_types=1);

namespace App\Constants\Enum;

use App\Constants\EnumValuesTrait;
use Hyperf\Constants\Annotation\Constants;
use App\Constants\EnumConstantsTrait;
use Hyperf\Constants\Annotation\Message;

#[Constants]
enum FeedbackStatus: int
{
    use EnumConstantsTrait, EnumValuesTrait;

    #[Message('unresolved')]
    case Unresolved = 0;

    #[Message('resolve')]
    case Resolve = 1;
}
