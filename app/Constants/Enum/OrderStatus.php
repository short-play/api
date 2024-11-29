<?php

declare(strict_types=1);

namespace App\Constants\Enum;

use Hyperf\Constants\Annotation\Constants;
use App\Constants\EnumConstantsTrait;
use Hyperf\Constants\Annotation\Message;

#[Constants]
enum OrderStatus: int
{
    use EnumConstantsTrait;

    #[Message('pending')]
    case Pending = 1;

    #[Message('paid')]
    case Paid = 2;
}
