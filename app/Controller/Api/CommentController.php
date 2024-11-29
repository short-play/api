<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Request\Api\Comment\CommentListRequest;
use App\Request\Api\Comment\CommentRequest;
use App\Resource\Api\Comment\CommentListResource;
use App\Resource\Api\Comment\CommentResource;
use App\Service\CommentService;
use App\Service\VideoService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class CommentController extends AbstractController
{

    #[Inject]
    protected CommentService $commentService;

    #[Inject]
    protected VideoService $videoService;

    /**
     * 获取评论列表
     * @param CommentListRequest $request
     * @return ResponseInterface
     */
    public function comments(CommentListRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $unique = $this->requestUnique();
        $notIds = $req['not_ids'] ?? [];
        list($skip, $take) = $this->getSkipAndTake();
        $item = $this->videoService->checkItem(intval($req['video_id']), intval($req['item_id']));
        $comments = $this->commentService->commentList($unique, $item, $notIds, $skip, $take);
        return $this->response->resource(new CommentListResource($comments));
    }

    /**
     * 一级评论
     * @param CommentRequest $request
     * @return ResponseInterface
     */
    public function comment(CommentRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $item = $this->videoService->checkItem(intval($req['video_id']), intval($req['item_id']));
        $comment = $this->commentService->store(userId(), $item, $req['content']);
        return $this->response->resource(new CommentResource($comment));
    }

    /**
     * 删除评论
     * @param int $id
     * @return ResponseInterface
     */
    public function delete(int $id): ResponseInterface
    {
        $this->commentService->delete(userId(), $id);
        return $this->response->success();
    }
}