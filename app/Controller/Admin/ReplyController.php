<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Resource\Backend\Reply\ReplyListResource;
use App\Service\ReplyService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class ReplyController extends AbstractController
{

    #[Inject]
    protected ReplyService $replyService;

    /**
     * 评论回复列表
     * @param int $id
     * @return ResponseInterface
     */
    public function index(int $id): ResponseInterface
    {
        $pageSize = $this->getPageSize();
        $paginate = $this->replyService->adminReplyList($id, $pageSize);
        return $this->response->resource(new ReplyListResource($paginate));
    }

    /**
     * 回复删除
     * @param int $id
     * @return ResponseInterface
     */
    public function delete(int $id): ResponseInterface
    {
        $this->replyService->adminDeleteReplay($id);
        return $this->response->success();
    }
}