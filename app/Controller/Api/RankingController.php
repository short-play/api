<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Constants\Enum\RankingType;
use App\Controller\AbstractController;
use App\Resource\Api\Ranking\RankingTagListResource;
use App\Resource\Api\Ranking\RankingVideoListResource;
use App\Service\TagService;
use App\Service\VideoService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class RankingController extends AbstractController
{

    #[Inject]
    protected VideoService $videoService;

    #[Inject]
    protected TagService $tagService;

    /**
     * 推荐榜单
     * @return ResponseInterface
     */
    public function suggestList(): ResponseInterface
    {
        $suggestList = $this->videoService->rankingVideoList(RankingType::Recommended);
        return $this->response->resource(new RankingVideoListResource($suggestList));
    }

    /**
     * 新剧
     * @return ResponseInterface
     */
    public function newVideoList(): ResponseInterface
    {
        list($skip, $take) = $this->getSkipAndTake();
        $suggestList = $this->videoService->newVideoList($skip, $take);
        return $this->response->resource(new RankingVideoListResource($suggestList));
    }

    /**
     * 短剧热搜榜单
     * @return ResponseInterface
     */
    public function hotSearchList(): ResponseInterface
    {
        $hotSearchVideos = $this->videoService->rankingVideoList(RankingType::ShortSearch);;
        return $this->response->resource(new RankingVideoListResource($hotSearchVideos));
    }

    /**
     * 热搜新剧榜单
     * @return ResponseInterface
     */
    public function searchNewVideo(): ResponseInterface
    {
        $searchNewVideos = $this->videoService->rankingVideoList(RankingType::NewShortSearch);;
        return $this->response->resource(new RankingVideoListResource($searchNewVideos));
    }

    /**
     * 获取热搜标签榜单
     * @return ResponseInterface
     */
    public function searchTagList(): ResponseInterface
    {
        $tags = $this->tagService->getHotSearchTagList();
        return $this->response->resource(new RankingTagListResource($tags));
    }
}