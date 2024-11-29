<?php

declare(strict_types=1);

namespace App\Mails;

use function Hyperf\Config\config;
use function Hyperf\Translation\__;

class SendRegisterCodeEmail implements EmailTemplateInterface
{

    private string $to;

    private int|string $code;

    public function __construct(string $to, int|string $code)
    {
        $this->to = $to;
        $this->code = $code;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getHtml(): string
    {
        return __('mail.register_body', ['from_name' => config('mail.from_name'), 'code' => $this->code]);
    }

    public function getSubject(): string
    {
        return __('mail.register_subject');
    }
}