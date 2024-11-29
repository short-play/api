<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Constants\Enum\MessageType;
use App\Constants\ErrorCode;
use App\Controller\AbstractController;
use App\Exception\ShortPlayException;
use App\Request\Api\Message\MessageDeleteRequest;
use App\Resource\Api\Message\LikeListResource;
use App\Resource\Api\Message\MessageReplyResource;
use App\Resource\Api\Message\NoticeListResource;
use App\Resource\Api\Message\ReplyListResource;
use App\Resource\Api\Message\UnReadCountResource;
use App\Service\CommentService;
use App\Service\MessageService;
use App\Service\ReplyService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class MessageController extends AbstractController
{

    #[Inject]
    protected ReplyService $replyService;

    #[Inject]
    protected CommentService $commentService;

    #[Inject]
    protected MessageService $messageService;

    /**
     * 获取未读总数
     * @return ResponseInterface
     */
    public function unReadCount(): ResponseInterface
    {
        $messageCount = $this->messageService->getUnreadMessageCount(userId());
        return $this->response->resource(new UnReadCountResource($messageCount));
    }

    /**
     * 消息回复列表
     * @return ResponseInterface
     */
    public function replyList(): ResponseInterface
    {
        list($skip, $take) = $this->getSkipAndTake();
        $replyList = $this->messageService->getReplyMessageList(userId(), $skip, $take);
        return $this->response->resource(new ReplyListResource($replyList));
    }

    /**
     * 点赞消息列表
     * @return ResponseInterface
     */
    public function likeList(): ResponseInterface
    {
        list($skip, $take) = $this->getSkipAndTake();
        $likeList = $this->messageService->getLikeMessageList(userId(), $skip, $take);
        return $this->response->resource(new LikeListResource($likeList));
    }

    /**
     * 通知消息列表
     * @return ResponseInterface
     */
    public function noticeList(): ResponseInterface
    {
        list($skip, $take) = $this->getSkipAndTake();
        $noticeList = $this->messageService->getNoticeMessageList(userId(), $skip, $take);
        return $this->response->resource(new NoticeListResource($noticeList));
    }

    /**
     * 删除消息列表
     * @param MessageDeleteRequest $request
     * @return ResponseInterface
     */
    public function deleteMessage(MessageDeleteRequest $request): ResponseInterface
    {
        $validated = $request->validated();
        $msgId = intval($validated['id']);
        match (MessageType::tryFrom($validated['type'])) {
            MessageType::Reply => $this->messageService->delReplyMessage(userId(), $msgId),
            MessageType::Likes => $this->messageService->delLikeMessage(userId(), $msgId),
            MessageType::Notice => $this->messageService->delNoticeMessage(userId(), $msgId),
        };
        return $this->response->success();
    }

    /**
     * 消息已读
     * @param int $type
     * @return ResponseInterface
     */
    public function readMessage(int $type): ResponseInterface
    {
        match (MessageType::tryFrom($type)) {
            MessageType::Reply => $this->messageService->readReplyMessage(userId()),
            MessageType::Likes => $this->messageService->readLikeMessage(userId()),
            MessageType::Notice => $this->messageService->readNoticeMessage(userId()),
            default => throw new ShortPlayException(ErrorCode::DATA_INVALID->value),
        };
        return $this->response->success();
    }

    /**
     * 获取回复消息下评论和回复
     * @param int $id
     * @return ResponseInterface
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function commentDetail(int $id): ResponseInterface
    {
        // 获取消息信息
        $message = $this->messageService->replyMessageDetail(userId(), $id);
        // 获取评论
        $comment = $this->commentService->commentRelationById($message->comment_id);
        $replyIds = [$message->reply_id];
        if ($message->replied_id != $message->comment_id) {
            $replyIds[] = $message->replied_id;
        }
        $reply = $this->replyService->getReplyByIds($replyIds);
        return $this->response->resource(new MessageReplyResource($message, $comment, $reply));
    }

    /**
     * 获取点赞消息下评论和回复
     * @param int $id
     * @return ResponseInterface
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function likeDetail(int $id): ResponseInterface
    {
        // 获取消息信息
        $message = $this->messageService->likeMessageDetail(userId(), $id);
        // 获取评论
        $comment = $this->commentService->commentRelationById($message->comment_id);
        $reply = null;
        if ($message->comment_id != $message->reply_id) {
            $reply = $this->replyService->getReplyParent($message->reply_id);
        }
        return $this->response->resource(new MessageReplyResource($message, $comment, $reply));
    }
}