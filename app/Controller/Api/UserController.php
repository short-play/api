<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Components\ResourceSignInterface;
use App\Constants\ProjectConfig;
use App\Controller\AbstractController;
use App\Request\Api\User\ChangePasswordRequest;
use App\Request\Api\User\UserUpdateRequest;
use App\Resource\Api\User\UserCoinsResource;
use App\Resource\Api\User\UserResource;
use App\Service\OrderService;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class UserController extends AbstractController
{

    #[Inject]
    protected UserService $userService;

    #[Inject]
    protected OrderService $orderService;

    /**
     * 获取用户信息
     * @return ResponseInterface
     */
    public function me(): ResponseInterface
    {
        return $this->response->resource(new UserResource(user()));
    }

    /**
     * 退出登录
     * @return ResponseInterface
     */
    public function logout(): ResponseInterface
    {
        $userId = userId();
        auth()->logout();
        $this->userService->delUserCache($userId);
        return $this->response->success();
    }

    /**
     * 修改密码
     * @param ChangePasswordRequest $request
     * @return ResponseInterface
     */
    public function changePassword(ChangePasswordRequest $request): ResponseInterface
    {
        $validated = $request->validated();
        $this->userService->changePassword(userId(), $validated['oldPassword'], $validated['password']);
        return $this->response->success();
    }

    /**
     * 修改用户信息
     * @param UserUpdateRequest $request
     * @return ResponseInterface
     */
    public function updateUser(UserUpdateRequest $request): ResponseInterface
    {
        $requestData = array_filter($request->validated());
        $this->userService->updateUser(userId(), $requestData);
        return $this->response->success();
    }

    /**
     * 获取用户头像上传url
     *
     * @throws ContainerExceptionInterface
     */
    public function profileUploadUrl(): ResponseInterface
    {
        $resource = $this->container->get(ResourceSignInterface::class);
        $data = $resource->signature(ProjectConfig::PIC_DIR, ProjectConfig::UPLOAD_EXPIRE);
        return $this->response->success($data);
    }

    /**
     * 开通会员
     * @return ResponseInterface
     */
    public function member(): ResponseInterface
    {
        $this->orderService->order(userId());
        return $this->response->success();
    }

    /**
     * 获取金币总数
     * @return ResponseInterface
     */
    public function coin(): ResponseInterface
    {
        $coin = $this->userService->getUserCoinCount(userId());
        return $this->response->success(compact('coin'));
    }

    /**
     * 获取用户任务金币明细
     * @return ResponseInterface
     */
    public function coinDetail(): ResponseInterface
    {
        list($skip, $take) = $this->getSkipAndTake();
        $coins = $this->userService->getUserCoinDetail(userId(), $skip, $take);
        return $this->response->resource(new UserCoinsResource($coins));
    }
}