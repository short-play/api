<?php

declare(strict_types=1);

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use App\Components\impl\AliOssSign;
use App\Components\impl\Mail;
use App\Components\MailInterface;
use App\Components\ResourceSignInterface;

return [
    MailInterface::class => Mail::class,
    ResourceSignInterface::class => AliOssSign::class,
];
