<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Resource\Api\Search\SearchHistoryResource;
use App\Service\SearchLogService;
use App\Service\VideoService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class SearchController extends AbstractController
{

    #[Inject]
    protected SearchLogService $searchLogService;

    #[Inject]
    protected VideoService $videoService;

    /**
     * 获取搜索历史
     * @return ResponseInterface
     */
    public function history(): ResponseInterface
    {
        $searchHistory = $this->searchLogService->getHistory($this->requestUnique());
        return $this->response->resource(new SearchHistoryResource($searchHistory));
    }

    /**
     * 清除搜索历史
     * @return ResponseInterface
     */
    public function clear(): ResponseInterface
    {
        $this->searchLogService->clearHistory($this->requestUnique());
        return $this->response->success();
    }

    /**
     * 搜索量统计
     * @param int $id
     * @return ResponseInterface
     */
    public function search(int $id): ResponseInterface
    {
        $video = $this->videoService->videoDetail($id);
        $this->searchLogService->statisticsSearchVideo($video);
        return $this->response->success();
    }
}