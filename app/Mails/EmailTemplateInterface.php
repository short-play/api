<?php

namespace App\Mails;

interface EmailTemplateInterface
{
    public function getTo(): string;

    public function getHtml(): string;

    public function getSubject(): string;
}
