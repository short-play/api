<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Constants\Enum\FeedbackStatus;
use App\Controller\AbstractController;
use App\Request\Backend\Feedback\FeedbackListRequest;
use App\Resource\Backend\Feedback\FeedbackListResource;
use App\Service\FeedbackService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class FeedbackController extends AbstractController
{

    #[Inject]
    protected FeedbackService $feedbackService;

    /**
     * 反馈列表
     * @param FeedbackListRequest $request
     * @return ResponseInterface
     */
    public function index(FeedbackListRequest $request): ResponseInterface
    {
        $search = $request->validated();
        $pageIndex = $this->getPageSize();
        $paginate = $this->feedbackService->feedbackList($search, $pageIndex);
        return $this->response->resource(new FeedbackListResource($paginate));
    }

    /**
     * 修改反馈状态
     * @param int $id
     * @return ResponseInterface
     */
    public function status(int $id): ResponseInterface
    {
        $status = intval($this->request->input('status'));
        $feedbackStatus = FeedbackStatus::tryFrom($status);
        if ($feedbackStatus != null) {
            $this->feedbackService->statusFeedback($id, $feedbackStatus);
        }
        return $this->response->success();
    }
}