<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Request\Api\Reply\ReplyListRequest;
use App\Request\Api\Reply\ReplyRequest;
use App\Resource\Api\Reply\ReplyListResource;
use App\Resource\Api\Reply\ReplyResource;
use App\Service\CommentService;
use App\Service\ReplyService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class ReplyController extends AbstractController
{

    #[Inject]
    protected ReplyService $replyService;

    #[Inject]
    protected CommentService $commentService;

    /**
     * 评论回复列表
     * @param ReplyListRequest $request
     * @return ResponseInterface
     */
    public function replays(ReplyListRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $notIds = $req['not_ids'] ?? [];
        $unique = $this->requestUnique();
        $commentId = intval($req['comment_id']);
        list($skip, $take) = $this->getSkipAndTake();
        $replays = $this->replyService->getReplyList($unique, $commentId, $notIds, $skip, $take);
        return $this->response->resource(new ReplyListResource($replays));
    }

    /**
     * 评论回复
     * @param ReplyRequest $request
     * @return ResponseInterface
     */
    public function reply(ReplyRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $comment = $this->commentService->getCommentById(intval($req['comment_id']));
        $reply = $this->replyService->reply(userId(), $comment, intval($req['reply_id']), $req['content']);
        return $this->response->resource(new ReplyResource($reply));
    }

    /**
     * 回复删除
     * @param int $id
     * @return ResponseInterface
     */
    public function delete(int $id): ResponseInterface
    {
        $this->replyService->delete(userId(), $id);
        return $this->response->success();
    }
}