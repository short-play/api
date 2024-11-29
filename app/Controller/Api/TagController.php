<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Resource\Api\Tag\TagListResource;
use App\Service\TagService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class TagController extends AbstractController
{

    #[Inject]
    protected TagService $tagService;

    /**
     * 标签列表
     * @return ResponseInterface
     */
    public function tags(): ResponseInterface
    {
        $tags = $this->tagService->getTags();
        return $this->response->resource(new TagListResource($tags));
    }
}