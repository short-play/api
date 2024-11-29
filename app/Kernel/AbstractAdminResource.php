<?php

declare(strict_types=1);

namespace App\Kernel;

use Hyperf\Contract\LengthAwarePaginatorInterface;

abstract class AbstractAdminResource implements ResourceInterface
{

    protected LengthAwarePaginatorInterface $paginator;

    /**
     * 获取数据列表
     * @return array
     */
    abstract public function getResources(): array;

    /**
     * 返回数据
     * @return array
     */
    public function toArray(): array
    {
        return [
            'total' => $this->paginator->total(),
            'list' => $this->getResources()
        ];
    }
}