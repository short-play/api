<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\Api\Login\ForgetCodeRequest;
use App\Request\Api\Login\ForgetPasswordRequest;
use App\Request\Api\Login\LoginRequest;
use App\Service\AdminLoginService;
use App\Service\AdminService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class LoginController extends AbstractController
{

    #[Inject]
    protected AdminLoginService $adminLoginService;

    #[Inject]
    protected AdminService $adminService;

    /**
     * 登录
     * @param LoginRequest $request
     * @return ResponseInterface
     */
    public function login(LoginRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $token = $this->adminLoginService->login($req['email'], $req['password']);
        return $this->response->success(compact('token'));
    }

    /**
     * 发送忘记密码验证码
     * @param ForgetCodeRequest $request
     * @return ResponseInterface
     */
    public function sendCode(ForgetCodeRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->adminLoginService->sendForgetPassCode($req['email']);
        return $this->response->success();
    }

    /**
     * 重置密码
     * @param ForgetPasswordRequest $request
     * @return ResponseInterface
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function forgetPassword(ForgetPasswordRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $code = intval($req['code']);
        $password = (string)$req['password'];
        $admin = $this->adminLoginService->forgetPassword($req['email'], $password, $code);
        $this->adminService->delAdminCache($admin->id);
        return $this->response->success();
    }
}
