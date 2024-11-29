<?php

declare(strict_types=1);

namespace App\Resource\Backend\Feedback;

use App\Constants\Enum\FeedbackStatus;
use App\Kernel\AbstractAdminResource;
use Hyperf\Contract\LengthAwarePaginatorInterface;

class FeedbackListResource extends AbstractAdminResource
{

    public function __construct(protected LengthAwarePaginatorInterface $paginator)
    {
    }

    /**
     * @return array
     * @noinspection PhpDynamicAsStaticMethodCallInspection
     */
    public function getResources(): array
    {
        return array_map(function ($item) {
            return [
                'id' => (string)$item->id,
                'title' => $item->title,
                'desc' => $item->desc,
                'pic_json' => $item->pic_json,
                'link' => $item->link,
                'status' => $item->status,
                'status_name' => FeedbackStatus::getMessage($item->status),
                'created_at' => $item->created_at->toDateTimeString(),
                'user' => $item->user ?? ['id' => $item->unique_id, 'name' => 'device submit',],
            ];
        }, $this->paginator->items());
    }
}