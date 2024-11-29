<?php

declare(strict_types=1);

namespace App\Constants\Enum;

use App\Constants\EnumConstantsTrait;
use App\Constants\EnumValuesTrait;
use Hyperf\Constants\Annotation\Constants;
use Hyperf\Constants\Annotation\Message;

#[Constants]
enum Agreement: string
{
    use EnumConstantsTrait, EnumValuesTrait;

    #[Message('user_agreement')]
    case UserAgreement = 'UA';

    #[Message('privacy_agreement')]
    case PrivacyAgreement = 'PP';
}
