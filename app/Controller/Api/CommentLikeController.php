<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Constants\Enum\LikeType;
use App\Controller\AbstractController;
use App\Request\Api\CommentLike\CommentLikeRequest;
use App\Service\CommentLikeService;
use App\Service\CommentService;
use App\Service\ReplyService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Annotation\Scene;
use Psr\Http\Message\ResponseInterface;

class CommentLikeController extends AbstractController
{

    #[Inject]
    protected CommentLikeService $commentLikeService;

    #[Inject]
    protected CommentService $commentService;

    #[Inject]
    protected ReplyService $replyService;

    /**
     * 评论点赞
     * @param CommentLikeRequest $request
     * @return ResponseInterface
     */
    #[Scene('like')]
    public function commentLike(CommentLikeRequest $request): ResponseInterface
    {
        $validated = $request->validated();
        $type = LikeType::from(intval($validated['is_dislike']));
        $comment = $this->commentService->getCommentById(intval($validated['cr_id']));
        $this->commentLikeService->likeStore(userId(), $comment, $type);
        return $this->response->success();
    }

    /**
     * 回复点赞
     * @param CommentLikeRequest $request
     * @return ResponseInterface
     */
    #[Scene('like')]
    public function replyLike(CommentLikeRequest $request): ResponseInterface
    {
        $validated = $request->validated();
        $type = LikeType::from(intval($validated['is_dislike']));
        $reply = $this->replyService->getReplyById(intval($validated['cr_id']));
        $this->commentLikeService->likeStore(userId(), $reply, $type);
        return $this->response->success();
    }

    /**
     * 评论取消
     * @param CommentLikeRequest $request
     * @return ResponseInterface
     */
    #[Scene('unlike')]
    public function commentUnlike(CommentLikeRequest $request): ResponseInterface
    {
        $validated = $request->validated();
        $this->commentLikeService->cancelCommentLike(userId(), intval($validated['cr_id']));
        return $this->response->success();
    }

    /**
     * 回复取消
     * @param CommentLikeRequest $request
     * @return ResponseInterface
     */
    #[Scene('unlike')]
    public function replyUnlike(CommentLikeRequest $request): ResponseInterface
    {
        $validated = $request->validated();
        $this->commentLikeService->cancelReplyLike(userId(), intval($validated['cr_id']));
        return $this->response->success();
    }
}