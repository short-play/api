<?php

declare(strict_types=1);

namespace App\Components;

use App\Mails\EmailTemplateInterface;

interface MailInterface
{
    public function send(EmailTemplateInterface $emailTemplate): void;
}