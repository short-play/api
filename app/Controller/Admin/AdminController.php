<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\Backend\Admin\ChangePasswordRequest;
use App\Request\Backend\Admin\UpdateMeRequest;
use App\Resource\Backend\Admin\AdminDetailResource;
use App\Service\AdminService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class AdminController extends AbstractController
{

    #[Inject]
    protected AdminService $adminService;

    /**
     * 获取管理员信息
     * @return ResponseInterface
     */
    public function me(): ResponseInterface
    {
        return $this->response->resource(new AdminDetailResource(admin()));
    }

    /**
     * 修改密码
     * @param ChangePasswordRequest $request
     * @return ResponseInterface
     */
    public function password(ChangePasswordRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->adminService->changePassword(admin(), $req['old_password'], $req['password']);
        return $this->response->success();
    }

    /**
     * 修改管理员信息
     * @param UpdateMeRequest $request
     * @return ResponseInterface
     */
    public function updateAdmin(UpdateMeRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->adminService->updateAdmin(admin(), $req['mail'], $req['name']);
        return $this->response->success();
    }

    /**
     * 退出登录
     * @return ResponseInterface
     */
    public function logout(): ResponseInterface
    {
        $this->adminService->logout(admin()->id);
        return $this->response->success();
    }
}
