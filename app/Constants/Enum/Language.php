<?php

declare(strict_types=1);

namespace App\Constants\Enum;

use App\Constants\EnumValuesTrait;
use Hyperf\Constants\Annotation\Constants;
use App\Constants\EnumConstantsTrait;
use Hyperf\Constants\Annotation\Message;

#[Constants]
enum Language: string
{
    use EnumConstantsTrait, EnumValuesTrait;

    #[Message('简体中文')]
    case ZhCN = 'zh_CN';

    #[Message('English')]
    case En = 'en';
}
