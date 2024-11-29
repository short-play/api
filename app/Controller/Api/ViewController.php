<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Request\Api\View\ViewDeleteRequest;
use App\Request\Api\View\ViewRequest;
use App\Resource\Api\View\ViewDetailResource;
use App\Resource\Api\View\ViewListResource;
use App\Service\VideoService;
use App\Service\ViewService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class ViewController extends AbstractController
{

    #[Inject]
    protected ViewService $viewService;

    #[Inject]
    protected VideoService $videoService;

    /**
     * 观看记录
     * @param ViewRequest $request
     * @return ResponseInterface
     */
    public function view(ViewRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $itemId = intval($req['item_id']);
        $videoId = intval($req['video_id']);
        $duration = intval($req['duration']);
        $video = $this->videoService->checkItem($videoId, $itemId, true);
        $this->viewService->viewStore($this->requestUnique(), $video, $duration);
        return $this->response->success();
    }

    /**
     * 删除浏览记录
     * @param ViewDeleteRequest $request
     * @return ResponseInterface
     */
    public function deleteView(ViewDeleteRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->viewService->viewDelete($this->requestUnique(), $req['view_ids']);
        return $this->response->success();
    }

    /**
     * 浏览记录列表
     * @return ResponseInterface
     */
    public function viewList(): ResponseInterface
    {
        list($skip, $take) = $this->getSkipAndTake();
        $unique = $this->requestUnique()->getUnique();
        $isMovies = $this->request->input('is_movies');
        $viewList = $this->viewService->viewList($unique, $isMovies, $skip, $take);
        return $this->response->resource(new ViewListResource($viewList));
    }

    /**
     * 获取播放历史详情
     * @param int $id
     * @return ResponseInterface
     */
    public function detail(int $id): ResponseInterface
    {
        $detail = $this->viewService->detail($this->requestUnique()->getUnique(), $id);
        return $this->response->resource(new ViewDetailResource($detail));
    }
}