<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Constants\ErrorCode;
use App\Constants\RedisKey;
use App\Controller\AbstractController;
use App\Exception\ShortPlayException;
use App\Resource\Backend\Order\OrderListResource;
use App\Service\OrderService;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;
use function FriendsOfHyperf\Lock\lock;

class OrderController extends AbstractController
{

    #[Inject]
    protected OrderService $orderService;

    #[Inject]
    protected UserService $userService;

    /**
     * 获取订单列表
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $pageSize = $this->getPageSize();
        $paginate = $this->orderService->orderList($pageSize);
        return $this->response->resource(new OrderListResource($paginate));
    }

    /**
     * 支付订单
     * @param string $no
     * @return ResponseInterface
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function pay(string $no): ResponseInterface
    {
        $key = RedisKey::ORDER_STATUS_LOCK . $no;
        $lockStatus = lock($key, 10)->get(function () use ($no) {
            $order = $this->orderService->payOrder($no);
            $this->userService->delUserCache($order->user_id);
        });
        if ($lockStatus === false) {
            throw new ShortPlayException(ErrorCode::LOCKING->value);
        }
        return $this->response->success();
    }
}
