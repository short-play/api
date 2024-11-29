<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Constants\Enum\ActivityType;
use App\Controller\AbstractController;
use App\Resource\Api\Activity\AppointVideoResource;
use App\Resource\Api\Activity\SignInTableResource;
use App\Resource\Api\Activity\WatchTimeResource;
use App\Service\ActivityService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class ActivityController extends AbstractController
{

    #[Inject]
    protected ActivityService $activityService;

    /**
     * 获取用户签到日期表格
     * @return ResponseInterface
     */
    public function signTable(): ResponseInterface
    {
        $activity = $this->activityService->getActivityByType(ActivityType::Sign);
        $signTables = $this->activityService->getUserSignTable(userId(), $activity);
        return $this->response->resource(new SignInTableResource($activity, $signTables));
    }

    /**
     * 用户签到
     * @return ResponseInterface
     */
    public function signIn(): ResponseInterface
    {
        $activity = $this->activityService->getActivityByType(ActivityType::Sign);
        $this->activityService->userSignIn(userId(), $activity);
        return $this->response->success();
    }

    /**
     * 看下方剧视频列表
     * @return ResponseInterface
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function appointVideo(): ResponseInterface
    {
        $activity = $this->activityService->getActivityByType(ActivityType::AppointVideo);
        $videos = $this->activityService->getAppointVideo($activity->config['appoint']['video_ids']);
        $time = $this->activityService->getUserWatchTime(userId(), 'appoint');
        $isReceive = $this->activityService->isDayReceiveAppointVideo(userId());
        return $this->response->resource(new AppointVideoResource($activity, $videos, $time, $isReceive));
    }

    /**
     * 领取看下方剧视频福利
     * @return ResponseInterface
     */
    public function videoBenefit(): ResponseInterface
    {
        $activity = $this->activityService->getActivityByType(ActivityType::AppointVideo);
        $this->activityService->receiveVideoBenefit(userId(), $activity);
        return $this->response->success();
    }

    /**
     * 看剧领金币任务表
     * @return ResponseInterface
     */
    public function watchTable(): ResponseInterface
    {
        $activity = $this->activityService->getActivityByType(ActivityType::WatchDuration);
        $watchTime = $this->activityService->getUserWatchTime(userId(), 'total');
        $watchTable = $this->activityService->getWatchTable(userId(), $activity);
        return $this->response->resource(new WatchTimeResource($activity, $watchTable, $watchTime));
    }

    /**
     * 领取看剧领金币福利
     * @return ResponseInterface
     */
    public function watchBenefit(): ResponseInterface
    {
        $activity = $this->activityService->getActivityByType(ActivityType::WatchDuration);
        $this->activityService->receiveWatchBenefit(userId(), $activity);
        return $this->response->success();
    }
}