<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\Enum\OrderStatus;
use App\Constants\Enum\UserMember;
use App\Constants\ErrorCode;
use App\Constants\ProjectConfig;
use App\Constants\RedisKey;
use App\Exception\ShortPlayException;
use App\Model\Order;
use App\Model\User;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Model;
use Hyperf\DbConnection\Db;
use function FriendsOfHyperf\Lock\lock;
use function Hyperf\Support\now;

class OrderService
{

    /**
     * 获取订单号
     * @return string
     * @noinspection SpellCheckingInspection
     */
    public function getOrderNo(): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 3; $i++) {
            // 使用 random_int 生成字符索引
            $randomIndex = genRandomInt(0, $charactersLength - 1);
            $randomString .= $characters[$randomIndex];
        }
        $randomString .= now()->getTimestampMs();
        return $randomString;
    }

    /**
     * 获取订单列表
     * @param int $pageSize
     * @return LengthAwarePaginatorInterface
     */
    public function orderList(int $pageSize): LengthAwarePaginatorInterface
    {
        return Order::latest('created_at')->with('user')->paginate($pageSize);
    }

    /**
     * 开通会员
     * @param int $userId
     * @return void
     */
    public function order(int $userId): void
    {
        $no = $this->getOrderNo();
        $key = RedisKey::ORDER_LOCK . $no;
        $lockStatus = lock($key, 10)->get(function () use ($no, $userId) {
            Order::create([
                'no' => $no,
                'user_id' => $userId,
                'amount' => ProjectConfig::MEMBER_AMOUNT,
                'status' => OrderStatus::Pending->value,
            ]);
        });
        if ($lockStatus === false) {
            throw new ShortPlayException(ErrorCode::LOCKING->value);
        }
    }

    /**
     * 支付订单
     * @param string $orderNo
     * @return Model
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function payOrder(string $orderNo): Model
    {
        $order = Order::where('no', $orderNo)->firstOrFail();
        if ($order->status == OrderStatus::Paid->value) {
            throw new ShortPlayException(ErrorCode::ORDER_PAID->value);
        }
        $user = User::findOrFail($order->user_id);
        Db::transaction(function () use ($order, $user) {
            $payTime = now();
            $order->status = OrderStatus::Paid->value;
            $order->pay_time = $payTime;
            $order->save();
            // 修改用户信息,更改会员状态添加1个月时间
            $user->is_member = UserMember::Open->value;
            $user->member_time = $user->member_time ? $user->member_time->addMonth() : $payTime;
            $user->save();
        });
        return $order;
    }
}