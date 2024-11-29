<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\Backend\Tag\TagListRequest;
use App\Request\Backend\Tag\TagRequest;
use App\Request\Backend\Tag\TagUpdateRequest;
use App\Resource\Backend\Tag\TagListResource;
use App\Service\TagService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class TagController extends AbstractController
{

    #[Inject]
    protected TagService $tagService;

    /**
     * 标签列表
     * @param TagListRequest $request
     * @return ResponseInterface
     */
    public function index(TagListRequest $request): ResponseInterface
    {
        $pageSize = $this->getPageSize();
        $validated = array_filter($request->validated());
        $tags = $this->tagService->tagList($validated, $pageSize);
        return $this->response->resource(new TagListResource($tags));
    }

    /**
     * 创建标签
     * @param TagRequest $request
     * @return ResponseInterface
     */
    public function create(TagRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->tagService->createTag((string)$req['value'], intval($req['sort']));
        return $this->response->success();
    }

    /**
     * 修改信息
     * @param int $id
     * @param TagUpdateRequest $request
     * @return ResponseInterface
     */
    public function update(int $id, TagUpdateRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->tagService->updateTag($id, $req);
        return $this->response->success();
    }

    /**
     * 删除标签
     * @param int $id
     * @return ResponseInterface
     */
    public function delete(int $id): ResponseInterface
    {
        $this->tagService->deleteTag($id);
        return $this->response->success();
    }
}