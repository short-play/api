<?php

declare(strict_types=1);

namespace App\Kernel;

use App\Model\User;

class UniqueData
{
    public int $unique;

    public function __construct(protected array $device, protected ?User $user)
    {
        $this->unique = $this->isUser() ? $this->user->id : $this->device['id'];
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getDevice(): array
    {
        return $this->device;
    }

    public function getUnique(): int
    {
        return $this->unique;
    }

    public function isUser(): bool
    {
        return !empty($this->user);
    }
}