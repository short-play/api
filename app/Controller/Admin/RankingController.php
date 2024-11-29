<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\Backend\Ranking\RankingDeleteRequest;
use App\Request\Backend\Ranking\RankingRequest;
use App\Resource\Backend\Ranking\RankingListResource;
use App\Service\RankingService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Annotation\Scene;
use Psr\Http\Message\ResponseInterface;

class RankingController extends AbstractController
{

    #[Inject]
    protected RankingService $rankingService;

    /**
     * 榜单列表
     * @param int $type
     * @return ResponseInterface
     */
    public function index(int $type): ResponseInterface
    {
        $pageSize = $this->getPageSize();
        $paginate = $this->rankingService->rankingList($type, $pageSize);
        return $this->response->resource(new RankingListResource($paginate));
    }

    /**
     * 添加榜单
     * @param int $type
     * @param RankingRequest $request
     * @return ResponseInterface
     */
    #[Scene('create')]
    public function create(int $type, RankingRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->rankingService->create($type, $req['items']);
        return $this->response->success();
    }

    /**
     * 修改榜单排序
     * @param int $id
     * @param RankingRequest $request
     * @return ResponseInterface
     */
    #[Scene('update')]
    public function update(int $id, RankingRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->rankingService->updateRanking($id, intval($req['sort']));
        return $this->response->success();
    }

    /**
     * 删除榜单
     * @param RankingDeleteRequest $request
     * @return ResponseInterface
     */
    public function delete(RankingDeleteRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->rankingService->deleteRanking($req['ids']);
        return $this->response->success();
    }
}