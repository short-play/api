<?php

declare(strict_types=1);

namespace App\Components;

interface ResourceSignInterface
{
    public function signature(string $prefix = '', int $expire = 30, int $contentLengthRange = 1048576000): array;
}