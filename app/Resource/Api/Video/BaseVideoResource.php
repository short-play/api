<?php

declare(strict_types=1);

namespace App\Resource\Api\Video;

use App\Constants\Enum\TagType;
use App\Constants\Enum\VideoType;
use App\Kernel\ResourceInterface;

abstract class BaseVideoResource implements ResourceInterface
{
    /**
     * 默认返回内容
     * @param array $video
     * @return array
     * @noinspection PhpDynamicAsStaticMethodCallInspection
     */
    public function defaultResponse(array $video): array
    {
        return [
            'id' => (string)$video['id'],
            'num' => $video['num'],
            'desc' => $video['desc'],
            'title' => $video['title'],
            'rating' => $video['rating'],
            'is_finish' => $video['is_finish'],
            'image_url' => $video['image_url'],
            'play_count' => $video['play_count'],
            'collect_count' => $video['collect_count'],
            'interact_count' => $video['interact_count'],
            'type_name' => VideoType::getMessage($video['type']),
            'tag_type' => $video['tag_type'] ? TagType::getMessage($video['tag_type']) : null,
        ];
    }
}