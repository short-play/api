<?php

declare(strict_types=1);

namespace App\Constants\Enum;

use Hyperf\Constants\Annotation\Constants;
use App\Constants\EnumConstantsTrait;

#[Constants]
enum UserMember: int
{
    use EnumConstantsTrait;

    case UnOpen = 0;

    case Open = 1;
}
