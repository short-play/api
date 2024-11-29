<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Request\Api\Login\ForgetCodeRequest;
use App\Request\Api\Login\ForgetPasswordRequest;
use App\Request\Api\Login\LoginRequest;
use App\Request\Api\Login\RegisterCodeRequest;
use App\Request\Api\Login\RegisterRequest;
use App\Service\LoginService;
use App\Service\UserDeviceService;
use App\Service\UserService;
use Hyperf\Coroutine\Coroutine;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class LoginController extends AbstractController
{

    #[Inject]
    protected LoginService $loginService;

    #[Inject]
    protected UserService $userService;

    /**
     * 登录
     * @param LoginRequest $request
     * @return ResponseInterface
     */
    public function login(LoginRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $deviceId = $this->requestUnique()->getDevice()['id'];
        list($user, $token) = $this->loginService->login($req['email'], $req['password']);
        Coroutine::create(function () use ($deviceId, $user) {
            // 同步设备信息到用户
            (new UserDeviceService($deviceId, $user))->run();
        });
        return $this->response->success(['authorization' => 'Bearer', 'token' => $token]);
    }

    /**
     * 发送注册验证码
     * @param RegisterCodeRequest $request
     * @return ResponseInterface
     */
    public function sendRegisterCodeMail(RegisterCodeRequest $request): ResponseInterface
    {
        $validated = $request->validated();
        $this->loginService->sendRegisterCodeMail($validated['email']);
        return $this->response->success();
    }

    /**
     * 注册
     * @param RegisterRequest $request
     * @return ResponseInterface
     */
    public function register(RegisterRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->loginService->register($req['email'], $req['password'], intval($req['code']));
        return $this->response->success();
    }

    /**
     * 发送忘记密码验证码
     * @param ForgetCodeRequest $request
     * @return ResponseInterface
     */
    public function sendForgetCodeMail(ForgetCodeRequest $request): ResponseInterface
    {
        $validated = $request->validated();
        $this->loginService->sendForgetCodeMail($validated['email']);
        return $this->response->success();
    }

    /**
     * 忘记密码
     * @param ForgetPasswordRequest $request
     * @return ResponseInterface
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function forgetPassword(ForgetPasswordRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $code = intval($req['code']);
        $password = (string)$req['password'];
        $user = $this->loginService->forgetPassword($req['email'], $password, $code);
        $this->userService->delUserCache($user->id);
        return $this->response->success();
    }
}
