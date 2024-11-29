<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\Backend\Actor\ActorListRequest;
use App\Request\Backend\Actor\ActorRequest;
use App\Resource\Backend\Actor\ActorListResource;
use App\Service\ActorService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class ActorController extends AbstractController
{

    #[Inject]
    protected ActorService $actorsService;

    /**
     * 演员列表
     * @param ActorListRequest $request
     * @return ResponseInterface
     */
    public function index(ActorListRequest $request): ResponseInterface
    {
        $pageSize = $this->getPageSize();
        $search = $request->validated();
        $paginate = $this->actorsService->actorsList($search, $pageSize);
        return $this->response->resource(new ActorListResource($paginate));
    }

    /**
     * 添加演员
     * @param ActorRequest $request
     * @return ResponseInterface
     */
    public function create(ActorRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->actorsService->createActors($req['name']);
        return $this->response->success();
    }

    /**
     * 修改演员
     * @param int $id
     * @param ActorRequest $request
     * @return ResponseInterface
     */
    public function update(int $id, ActorRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->actorsService->updateActors($id, $req['name']);
        return $this->response->success();
    }

    /**
     * 删除演员
     * @param int $id
     * @return ResponseInterface
     */
    public function delete(int $id): ResponseInterface
    {
        $this->actorsService->deleteActors($id);
        return $this->response->success();
    }
}