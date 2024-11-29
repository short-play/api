<?php

declare(strict_types=1);

namespace App\Exception;

use App\Constants\ErrorCode;

class UnauthorizedException extends ShortPlayException
{

    public function __construct()
    {
        parent::__construct(ErrorCode::UNAUTHORIZED->value);
    }
}