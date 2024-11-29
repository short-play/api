<?php

declare(strict_types=1);

namespace App\Resource\Api\View;

use App\Kernel\ResourceInterface;
use Hyperf\Database\Model\Model;

class ViewDetailResource implements ResourceInterface
{

    public function __construct(protected Model $model)
    {
    }

    public function toArray(): array
    {
        return [
            'video_id' => (string)$this->model->video_id,
            'item_id' => (string)$this->model->item_id,
            'time' => $this->model->updated_at->toDateTimeString(),
            'play_duration' => $this->model->play_duration,
            'duration' => $this->model->duration,
            'watch_num' => $this->model->num,
        ];
    }
}