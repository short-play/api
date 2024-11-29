<?php

declare(strict_types=1);

namespace App\Resource\Backend\Video;

use App\Kernel\AbstractAdminResource;
use Hyperf\Contract\LengthAwarePaginatorInterface;

class VideoListResource extends AbstractAdminResource
{

    public function __construct(protected LengthAwarePaginatorInterface $paginator)
    {
    }

    /**
     * @return array
     */
    public function getResources(): array
    {
        return array_map(function ($item) {
            return (new VideoDetailResource($item))->toArray();
        }, $this->paginator->items());
    }
}