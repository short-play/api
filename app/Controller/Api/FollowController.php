<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Request\Api\Follow\FollowRequest;
use App\Resource\Api\Follow\FollowListResource;
use App\Service\FollowService;
use App\Service\VideoService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class FollowController extends AbstractController
{

    #[Inject]
    protected FollowService $followService;

    #[Inject]
    protected VideoService $videoService;

    /**
     * 追剧保存
     * @param FollowRequest $request
     * @return ResponseInterface
     */
    public function follow(FollowRequest $request): ResponseInterface
    {
        $validated = $request->validated();
        $this->followService->follow($this->requestUnique(), $validated['video_ids']);
        return $this->response->success();
    }

    /**
     * 是否追剧
     * @param int $id
     * @return ResponseInterface
     */
    public function followed(int $id): ResponseInterface
    {
        $video = $this->videoService->videoCount($id);
        $followed = $this->followService->isFollowed($this->requestUnique(), [$id]);
        return $this->response->success(['isFollowed' => empty($followed), 'count' => $video->collect_count]);
    }

    /**
     * 追剧列表
     * @return ResponseInterface
     */
    public function getFollows(): ResponseInterface
    {
        list($skip, $take) = $this->getSkipAndTake();
        $follows = $this->followService->followList($this->requestUnique(), $skip, $take);
        return $this->response->resource(new FollowListResource($follows));
    }

    /**
     * 追剧总数
     * @return ResponseInterface
     */
    public function getFollowsCount(): ResponseInterface
    {
        $count = $this->followService->followsCount($this->requestUnique());
        return $this->response->success(compact('count'));
    }

    /**
     * 取消追剧
     * @param FollowRequest $request
     * @return ResponseInterface
     */
    public function unfollow(FollowRequest $request): ResponseInterface
    {
        $validated = $request->validated();
        $this->followService->unfollow($this->requestUnique(), $validated['video_ids']);
        return $this->response->success();
    }
}