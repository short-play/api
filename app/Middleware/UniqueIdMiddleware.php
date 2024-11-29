<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\ErrorCode;
use App\Exception\ShortPlayException;
use App\Kernel\UniqueData;
use App\Service\DeviceService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UniqueIdMiddleware implements MiddlewareInterface
{

    #[Inject]
    protected DeviceService $deviceService;

    /**
     * 获取身份全局唯一id 设备和用户
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $device = $this->device($request->getHeaderLine('device-id'));
        $user = auth()->guest() ? null : user();
        $request->setAttribute(UniqueData::class, new UniqueData($device, $user));
        return $handler->handle($request);
    }

    /**
     * 获取设备信息
     * @param string $deviceId
     * @return array
     */
    private function device(string $deviceId): array
    {
        if (empty($deviceId)) {
            throw new ShortPlayException(ErrorCode::DEVICE_EMPTY->value);
        }
        // todo 这块做解密验证参数是否异常
        return $this->deviceService->getOrSetDevice($deviceId);
    }
}