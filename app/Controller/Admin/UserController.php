<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\Backend\User\UserDeleteRequest;
use App\Request\Backend\User\UserListRequest;
use App\Resource\Backend\User\UserDetailResource;
use App\Resource\Backend\User\UserListResource;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class UserController extends AbstractController
{

    #[Inject]
    protected UserService $userService;

    /**
     * 获取用户列表
     * @param UserListRequest $request
     * @return ResponseInterface
     */
    public function index(UserListRequest $request): ResponseInterface
    {
        $pageSize = $this->getPageSize();
        $validated = array_filter($request->validated());
        $paginate = $this->userService->userList($validated, $pageSize);
        return $this->response->resource(new UserListResource($paginate));
    }

    /**
     * 获取用户详情
     * @param int $id
     * @return ResponseInterface
     */
    public function detail(int $id): ResponseInterface
    {
        $user = $this->userService->getUserById($id);
        return $this->response->resource(new UserDetailResource($user));
    }

    /**
     * 禁用启用用户
     * @param UserDeleteRequest $request
     * @return ResponseInterface
     */
    public function delete(UserDeleteRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->userService->deleteUser($req['disable'], $req['ids']);
        return $this->response->success();
    }
}