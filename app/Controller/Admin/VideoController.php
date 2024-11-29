<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Components\ResourceSignInterface;
use App\Constants\ProjectConfig;
use App\Controller\AbstractController;
use App\Request\Backend\Video\VideoItemRequest;
use App\Request\Backend\Video\VideoListRequest;
use App\Request\Backend\Video\VideoRequest;
use App\Resource\Backend\Video\CollectUserListResource;
use App\Resource\Backend\Video\ShortUserListResource;
use App\Resource\Backend\Video\VideoDetailResource;
use App\Resource\Backend\Video\VideoItemListResource;
use App\Resource\Backend\Video\VideoListResource;
use App\Service\ManageVideoService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Annotation\Scene;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class VideoController extends AbstractController
{

    #[Inject]
    protected ManageVideoService $manageVideoService;

    /**
     * 视频列表
     * @param VideoListRequest $request
     * @return ResponseInterface
     */
    public function index(VideoListRequest $request): ResponseInterface
    {
        $search = $request->validated();
        $pageSize = $this->getPageSize();
        $paginate = $this->manageVideoService->videoList($search, $pageSize);
        return $this->response->resource(new VideoListResource($paginate));
    }

    /**
     * 创建视频
     * @param VideoRequest $request
     * @return ResponseInterface
     */
    #[Scene('create')]
    public function create(VideoRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $tags = $req['tags'];
        $actors = $req['actors'];
        $req = array_diff_key($req, compact('tags', 'actors'));
        $this->manageVideoService->createVideo($req, $tags, $actors);
        return $this->response->success();
    }

    /**
     * 获取视频详情
     * @param int $id
     * @return ResponseInterface
     */
    public function detail(int $id): ResponseInterface
    {
        $video = $this->manageVideoService->videoDetail($id);
        return $this->response->resource(new VideoDetailResource($video));
    }

    /**
     * 修改视频
     * @param int $id
     * @param VideoRequest $request
     * @return ResponseInterface
     */
    #[Scene('update')]
    public function update(int $id, VideoRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $tags = $req['tags'];
        $actors = $req['actors'];
        $req = array_diff_key($req, compact('tags', 'actors'));
        $this->manageVideoService->updateVideo($id, $req, $tags, $actors);
        return $this->response->success();
    }

    /**
     * 删除视频
     * @param int $id
     * @return ResponseInterface
     */
    public function delete(int $id): ResponseInterface
    {
        $this->manageVideoService->deleteVideo($id);
        return $this->response->success();
    }

    /**
     * 设置视频是否完结
     * @param int $id
     * @param VideoRequest $request
     * @return ResponseInterface
     */
    #[Scene('finish')]
    public function finish(int $id, VideoRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->manageVideoService->videoFinish($id, intval($req['finish']));
        return $this->response->success();
    }

    /**
     * 视频集合列表
     * @param int $id
     * @return ResponseInterface
     */
    public function items(int $id): ResponseInterface
    {
        $items = $this->manageVideoService->getVideoItems($id);
        return $this->response->resource(new VideoItemListResource($items));
    }

    /**
     * 批量添加视频集合
     * @param int $id
     * @param VideoItemRequest $request
     * @return ResponseInterface
     */
    #[Scene('create')]
    public function itemCreate(int $id, VideoItemRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->manageVideoService->videoItemCreate($id, $req['items']);
        return $this->response->success();
    }

    /**
     * 删除视频集合数据
     * @param int $id
     * @return ResponseInterface
     */
    public function itemDelete(int $id): ResponseInterface
    {
        $this->manageVideoService->deleteVideoItem($id);
        return $this->response->success();
    }

    /**
     * 编辑视频状态,是否可看
     * @param int $id
     * @param VideoItemRequest $request
     * @return ResponseInterface
     */
    #[Scene('view')]
    public function itemView(int $id, VideoItemRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->manageVideoService->videoItemView($id, $req['is_view']);
        return $this->response->success();
    }

    /**
     * 视频收藏用户列表
     * @param int $id
     * @return ResponseInterface
     */
    public function collectUser(int $id): ResponseInterface
    {
        $pageSize = $this->getPageSize();
        $paginate = $this->manageVideoService->videoCollectUsers($id, $pageSize);
        return $this->response->resource(new CollectUserListResource($paginate));
    }

    /**
     * 视频点赞用户列表
     * @param int $id
     * @return ResponseInterface
     */
    public function shortUser(int $id): ResponseInterface
    {
        $pageSize = $this->getPageSize();
        $paginate = $this->manageVideoService->videoItemShortUsers($id, $pageSize);
        return $this->response->resource(new ShortUserListResource($paginate));
    }

    /**
     * 获取视频封面oss
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function coverOss(): ResponseInterface
    {
        $resource = $this->container->get(ResourceSignInterface::class);
        $data = $resource->signature(ProjectConfig::VIDEO_COVER_DIR, ProjectConfig::UPLOAD_EXPIRE);
        return $this->response->success($data);
    }

    /**
     * 获取视频存储oss
     * @param int $id
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function itemOss(int $id): ResponseInterface
    {
        $dir = ProjectConfig::VIDEO_DIR . '/' . $id;
        $resource = $this->container->get(ResourceSignInterface::class);
        $data = $resource->signature($dir, ProjectConfig::UPLOAD_EXPIRE);
        return $this->response->success($data);
    }
}
