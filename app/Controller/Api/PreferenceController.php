<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Constants\Enum\Preference;
use App\Controller\AbstractController;
use App\Service\DeviceService;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class PreferenceController extends AbstractController
{

    #[Inject]
    protected UserService $userService;

    #[Inject]
    protected DeviceService $deviceService;

    /**
     * 用户偏好列表
     * @return ResponseInterface
     */
    public function list(): ResponseInterface
    {
        $list = array_map(fn($preference) => [
            'id' => $preference->value,
            'name' => $preference->getMessage()
        ], Preference::cases());
        return $this->response->success($list);
    }

    /**
     * 保存用户偏好
     * @return ResponseInterface
     */
    public function store(): ResponseInterface
    {
        $preferenceVal = $this->request->input('preference');
        if ($preferenceVal != null) {
            $preferenceVal = Preference::tryFrom($preferenceVal);
        }
        $unique = $this->requestUnique();
        match ($unique->isUser()) {
            true => $this->userService->updatePreference($unique->getUnique(), $preferenceVal),
            default => $this->deviceService->updatePreference($unique, $preferenceVal)
        };
        return $this->response->success();
    }
}