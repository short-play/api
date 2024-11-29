<?php

declare(strict_types=1);

namespace App\Constants\Enum;

use App\Constants\EnumValuesTrait;
use Hyperf\Constants\Annotation\Constants;
use App\Constants\EnumConstantsTrait;
use Hyperf\Constants\Annotation\Message;

#[Constants]
enum AdminRole: int
{
    use EnumConstantsTrait, EnumValuesTrait;

    #[Message('super_admin')]
    case SuperAdmin = 1;

    #[Message('admin')]
    case Admin = 2;
}
