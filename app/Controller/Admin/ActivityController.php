<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Constants\Enum\ActivityStatus;
use App\Constants\Enum\ActivityType;
use App\Controller\AbstractController;
use App\Request\Backend\Activity\ActivityListRequest;
use App\Request\Backend\Activity\ActivityRequest;
use App\Request\Backend\Activity\TemplateRequest;
use App\Resource\Backend\Activity\ActivityListResource;
use App\Resource\Backend\Activity\ActivityResource;
use App\Service\ActivityService;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class ActivityController extends AbstractController
{

    #[Inject]
    protected ActivityService $activityService;

    /**
     * 活动列表
     * @param ActivityListRequest $request
     * @return ResponseInterface
     */
    public function index(ActivityListRequest $request): ResponseInterface
    {
        $pageSize = $this->getPageSize();
        $search = $request->validated();
        $paginate = $this->activityService->activityList($search, $pageSize);
        return $this->response->resource(new ActivityListResource($paginate));
    }

    /**
     * 获取活动详情
     * @param int $id
     * @return ResponseInterface
     */
    public function show(int $id): ResponseInterface
    {
        $activity = $this->activityService->adminActivityDetail($id);
        return $this->response->resource(new ActivityResource($activity, true));
    }

    /**
     * 修改活动信息
     * @param int $id
     * @param ActivityRequest $request
     * @return ResponseInterface
     */
    public function update(int $id, ActivityRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->activityService->updateActivity($id, $req['name'], $req['desc']);
        return $this->response->success();
    }

    /**
     * 禁用启用
     * @param int $id
     * @return ResponseInterface
     */
    public function status(int $id): ResponseInterface
    {
        $status = $this->request->input('status');
        $activityStatus = ActivityStatus::tryFrom(intval($status));
        if ($activityStatus) {
            $this->activityService->statusActivity($id, $activityStatus);
        }
        return $this->response->success();
    }

    /**
     * 配置模版
     * @param int $id
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function template(int $id): ResponseInterface
    {
        $activity = $this->activityService->adminActivityDetail($id);
        $activityType = ActivityType::from($activity->type);
        $request = $this->container->get(TemplateRequest::class);
        $request->scene($activityType->name)->validateResolved();
        $req = $request->validated();
        $this->activityService->templateActivity($activity, $req['config']);
        return $this->response->success();
    }
}