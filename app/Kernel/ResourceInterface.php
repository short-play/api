<?php

declare(strict_types=1);

namespace App\Kernel;

interface ResourceInterface
{
    public function toArray(): array;
}