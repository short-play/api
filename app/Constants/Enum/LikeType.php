<?php

declare(strict_types=1);

namespace App\Constants\Enum;

use Hyperf\Constants\Annotation\Constants;
use App\Constants\EnumConstantsTrait;

#[Constants]
enum LikeType: int
{
    use EnumConstantsTrait;

    case Like = 0;

    case Dislike = 1;
}
