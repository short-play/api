<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\Backend\Comment\CommentListRequest;
use App\Resource\Backend\Comment\CommentListResource;
use App\Service\CommentService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class CommentController extends AbstractController
{

    #[Inject]
    protected CommentService $commentService;

    /**
     * 获取评论列表
     * @param CommentListRequest $request
     * @return ResponseInterface
     */
    public function index(CommentListRequest $request): ResponseInterface
    {
        $search = $request->validated();
        $pageSize = $this->getPageSize();
        $paginate = $this->commentService->commentAdminList($search, $pageSize);
        return $this->response->resource(new CommentListResource($paginate));
    }

    /**
     * 删除评论
     * @param int $id
     * @return ResponseInterface
     */
    public function delete(int $id): ResponseInterface
    {
        $this->commentService->adminDeleteComment($id);
        return $this->response->success();
    }
}