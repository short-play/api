<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Constants\Enum\VideoType;
use App\Controller\AbstractController;
use App\Request\Api\Video\VideoFindRequest;
use App\Request\Api\Video\VideoPlayRequest;
use App\Resource\Api\Video\MovieListResource;
use App\Resource\Api\Video\TvListResource;
use App\Resource\Api\Video\VideoDetailResource;
use App\Resource\Api\Video\VideoListResource;
use App\Resource\Api\Video\VideoPlayListResource;
use App\Service\FollowService;
use App\Service\SearchLogService;
use App\Service\VideoService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class VideoController extends AbstractController
{

    #[Inject]
    protected VideoService $videoService;

    #[Inject]
    protected SearchLogService $searchLogService;

    #[Inject]
    protected FollowService $followService;

    /**
     * 获取首页播放列表
     * @return ResponseInterface
     */
    public function videoPlayList(): ResponseInterface
    {
        list($skip, $take) = $this->getSkipAndTake();
        $videos = $this->videoService->videoPlayList($skip, $take);
        return $this->response->resource(new VideoPlayListResource($videos));
    }

    /**
     * 获取猜你喜欢列表
     * @return ResponseInterface
     */
    public function videoLikeList(): ResponseInterface
    {
        $videoLikes = $this->videoService->videoLikeList();
        return $this->response->resource(new VideoListResource($videoLikes));
    }

    /**
     * 获取视频详情
     * @param int $id
     * @return ResponseInterface
     */
    public function videoDetail(int $id): ResponseInterface
    {
        $video = $this->videoService->videoDetail($id);
        return $this->response->resource(new VideoDetailResource($video));
    }

    /**
     * 更多好剧推荐
     * @param int $type
     * @return ResponseInterface
     */
    public function moreVideo(int $type): ResponseInterface
    {
        $type = VideoType::tryFrom($type) ?? VideoType::Short;
        $goodVideoList = $this->videoService->moreVideo($type);
        return $this->response->resource(new VideoListResource($goodVideoList));
    }

    /**
     * 找剧列表
     * @param VideoFindRequest $request
     * @return ResponseInterface
     */
    public function findVideoList(VideoFindRequest $request): ResponseInterface
    {
        $req = $request->validated();
        list($skip, $take) = $this->getSkipAndTake();
        if (!empty($req['tagId'])) {
            $videos = $this->videoService->tagVideoList(intval($req['tagId']), $skip, $take);
        } else {
            $videos = $this->videoService->findVideoList($skip, $take);
        }
        return $this->response->resource(new VideoListResource($videos));
    }

    /**
     * 获取电影列表
     * @return ResponseInterface
     */
    public function movieList(): ResponseInterface
    {
        list($skip, $take) = $this->getSkipAndTake();
        $movieList = $this->videoService->movieOrTvList(VideoType::Movie, $skip, $take);
        return $this->response->resource(new MovieListResource($movieList));
    }

    /**
     * 获取电视剧列表
     * @return ResponseInterface
     */
    public function tvList(): ResponseInterface
    {
        list($skip, $take) = $this->getSkipAndTake();
        $tvList = $this->videoService->movieOrTvList(VideoType::Tv, $skip, $take);
        $videoIds = array_column($tvList, 'id');
        $ids = $this->followService->isFollowed($this->requestUnique(), $videoIds, false);
        return $this->response->resource(new TvListResource($tvList, $ids));
    }

    /**
     * 搜索视频
     * @return ResponseInterface
     */
    public function searchVideo(): ResponseInterface
    {
        list($skip, $take) = $this->getSkipAndTake();
        $keyWord = $this->request->input('keyword', '');
        if (!is_string($keyWord) || empty($keyWord)) {
            return $this->response->success();
        }
        $searchVideo = $this->videoService->searchVideo($keyWord, $skip, $take);
        $this->searchLogService->storeHistory($this->requestUnique(), $keyWord);
        return $this->response->resource(new VideoListResource($searchVideo));
    }

    /**
     * 获取播放视频url
     * @param VideoPlayRequest $request
     * @return ResponseInterface
     */
    public function videoPlay(VideoPlayRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $itemId = intval($req['itemId']);
        $videoId = intval($req['videoId']);
        $url = $this->videoService->getPlayUrl($this->requestUnique(), $videoId, $itemId);
        return $this->response->success(compact('url'));
    }
}
