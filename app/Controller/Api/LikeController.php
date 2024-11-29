<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Request\Api\Like\LikeRequest;
use App\Resource\Api\Like\LikeListResource;
use App\Service\LikeService;
use App\Service\VideoService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Annotation\Scene;
use Psr\Http\Message\ResponseInterface;

class LikeController extends AbstractController
{

    #[Inject]
    protected LikeService $likeService;

    #[Inject]
    protected VideoService $videoService;

    /**
     * 点赞列表
     * @return ResponseInterface
     */
    public function getLikes(): ResponseInterface
    {
        list($skip, $take) = $this->getSkipAndTake();
        $collection = $this->likeService->getLikes($this->requestUnique()->getUnique(), $skip, $take);
        return $this->response->resource(new LikeListResource($collection));
    }

    /**
     * 是否点赞
     * @param LikeRequest $request
     * @return ResponseInterface
     */
    #[Scene(scene: 'liked')]
    public function liked(LikeRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $uId = $this->requestUnique()->getUnique();
        $item = $this->videoService->itemCount(intval($req['item_id']));
        $res = $this->likeService->findVideoLike($uId, intval($req['video_id']), intval($req['item_id']));
        return $this->response->success([
            'is_liked' => $res != null,
            'like_count' => $item->short_count,
            'comment_count' => $item->comment_count,
        ]);
    }

    /**
     * 点赞和取消点赞
     * @param LikeRequest $request
     * @return ResponseInterface
     */
    #[Scene(scene: 'like')]
    public function like(LikeRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $item = $this->videoService->checkItem(intval($req['video_id']), intval($req['item_id']));
        if ($req['is_cancel']) {
            $this->likeService->likeCancel($this->requestUnique(), $item);
        } else {
            $this->likeService->likeStore($this->requestUnique(), $item);
        }
        return $this->response->success();
    }
}