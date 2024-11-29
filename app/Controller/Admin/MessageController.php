<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\Backend\Message\MessageRequest;
use App\Request\Backend\Message\MessageSendUserRequest;
use App\Resource\Backend\Message\MessageListResource;
use App\Service\MessageService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class MessageController extends AbstractController
{

    #[Inject]
    protected MessageService $messageService;

    /**
     * 通知消息列表
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $pageSize = $this->getPageSize();
        $paginate = $this->messageService->adminMessageList($pageSize);
        return $this->response->resource(new MessageListResource($paginate));
    }

    /**
     * 添加消息
     * @param MessageRequest $request
     * @return ResponseInterface
     */
    public function create(MessageRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->messageService->createNoticeMessage((string)$req['text']);
        return $this->response->success();
    }

    /**
     * 修改消息内容
     * @param int $id
     * @param MessageRequest $request
     * @return ResponseInterface
     */
    public function update(int $id, MessageRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->messageService->updateNoticeMessage($id, (string)$req['text']);
        return $this->response->success();
    }

    /**
     * 删除通知消息内容
     * @param int $id
     * @return ResponseInterface
     */
    public function delete(int $id): ResponseInterface
    {
        $this->messageService->deleteNoticeMessage($id);
        return $this->response->success();
    }

    /**
     * 发送通知消息给用户
     * @param MessageSendUserRequest $request
     * @return ResponseInterface
     */
    public function sendUser(MessageSendUserRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->messageService->sendNoticeMessage(intval($req['id']), $req['user_ids']);
        return $this->response->success();
    }
}