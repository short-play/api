<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Components\ResourceSignInterface;
use App\Constants\ProjectConfig;
use App\Controller\AbstractController;
use App\Request\Api\Feedback\FollowRequest;
use App\Service\FeedbackService;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use function Hyperf\Support\now;

class FeedbackController extends AbstractController
{

    #[Inject]
    protected FeedbackService $feedbackService;

    /**
     * 反馈列表
     * @return ResponseInterface
     */
    public function feedbacks(): ResponseInterface
    {
        $feedbacks = $this->feedbackService->feedbacks($this->requestUnique());
        return $this->response->success($feedbacks->toArray());
    }

    /**
     * 保存反馈记录
     * @param FollowRequest $request
     * @return ResponseInterface
     */
    public function save(FollowRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->feedbackService->saveFeedback($this->requestUnique(), $req);
        return $this->response->success();
    }

    /**
     * 获取上传图片url
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function uploadUrl(): ResponseInterface
    {
        $resource = $this->container->get(ResourceSignInterface::class);
        $prefix = ProjectConfig::FEEDBACK_DIR . '/' . now()->format('Ymd');
        $data = $resource->signature($prefix, ProjectConfig::UPLOAD_EXPIRE);
        return $this->response->success($data);
    }
}