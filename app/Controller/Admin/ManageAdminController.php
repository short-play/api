<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\Backend\Admin\AdminDeleteRequest;
use App\Request\Backend\Admin\AdminListRequest;
use App\Request\Backend\Admin\AdminRequest;
use App\Resource\Backend\Admin\AdminDetailResource;
use App\Resource\Backend\Admin\AdminListResource;
use App\Service\AdminService;
use App\Service\ManageAdminService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Annotation\Scene;
use Psr\Http\Message\ResponseInterface;

class ManageAdminController extends AbstractController
{

    #[Inject]
    protected AdminService $adminService;

    #[Inject]
    protected ManageAdminService $manageAdminService;

    /**
     * 获取管理员列表
     * @param AdminListRequest $request
     * @return ResponseInterface
     */
    public function index(AdminListRequest $request): ResponseInterface
    {
        $validated = array_filter($request->validated());
        $pageSize = $this->getPageSize();
        $paginate = $this->manageAdminService->adminList($validated, $pageSize);
        return $this->response->resource(new AdminListResource($paginate));
    }

    /**
     * 创建管理员
     * @param AdminRequest $request
     * @return ResponseInterface
     */
    #[Scene(scene: 'create')]
    public function create(AdminRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->manageAdminService->createAdmin($req);
        return $this->response->success();
    }

    /**
     * 获取详情
     * @param int $id
     * @return ResponseInterface
     */
    public function detail(int $id): ResponseInterface
    {
        $admin = $this->adminService->getById($id);
        return $this->response->resource(new AdminDetailResource($admin));
    }

    /**
     * 修改信息
     * @param int $id
     * @param AdminRequest $request
     * @return ResponseInterface
     */
    #[Scene(scene: 'update')]
    public function update(int $id, AdminRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->manageAdminService->updateAdmin($id, array_filter($req));
        $this->adminService->delAdminCache($id);
        return $this->response->success();
    }

    /**
     * 禁用启用管理员
     * @param AdminDeleteRequest $request
     * @return ResponseInterface
     */
    public function delete(AdminDeleteRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->manageAdminService->deleteAdmin(admin()->id, $req['disable'], $req['ids']);
        array_map(fn($id) => $this->adminService->delAdminCache(intval($id)), $req['ids']);
        return $this->response->success();
    }
}