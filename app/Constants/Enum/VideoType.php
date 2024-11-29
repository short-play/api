<?php

declare(strict_types=1);

namespace App\Constants\Enum;

use App\Constants\EnumValuesTrait;
use Hyperf\Constants\Annotation\Constants;
use Hyperf\Constants\Annotation\Message;
use App\Constants\EnumConstantsTrait;

#[Constants]
enum VideoType: int
{
    use EnumConstantsTrait, EnumValuesTrait;

    #[Message('short')]
    case Short = 1;

    #[Message('movie')]
    case Movie = 2;

    #[Message('tv')]
    case Tv = 3;
}
