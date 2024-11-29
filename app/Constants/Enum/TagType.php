<?php

declare(strict_types=1);

namespace App\Constants\Enum;

use App\Constants\EnumValuesTrait;
use Hyperf\Constants\Annotation\Constants;
use Hyperf\Constants\Annotation\Message;
use App\Constants\EnumConstantsTrait;

#[Constants]
enum TagType: int
{
    use EnumConstantsTrait, EnumValuesTrait;

    #[Message('new')]
    case New = 1;

    #[Message('hot')]
    case Hot = 2;
}
