<?php

declare(strict_types=1);

namespace App\Constants\Enum;

use Hyperf\Constants\Annotation\Constants;
use Hyperf\Constants\Annotation\Message;
use App\Constants\EnumConstantsTrait;

#[Constants]
enum Preference: int
{
    use EnumConstantsTrait;

    #[Message('boy')]
    case Boy = 1;

    #[Message('girl')]
    case Girl = 2;
}