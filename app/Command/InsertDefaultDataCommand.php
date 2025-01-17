<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Command;

use App\Model\MessageCount;
use App\Model\User;
use App\Model\UserCoin;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Filesystem\FilesystemFactory;
use function Hyperf\Support\now;

#[Command]
class InsertDefaultDataCommand extends HyperfCommand
{
    #[Inject]
    private FilesystemFactory $filesystemFactory;

    /**
     * 执行的命令行.
     */
    protected ?string $name = 'init:db';


    public function handle()
    {
        // 创建用户
        $user = User::create([
            'mail' => 'test@test.com',
            'name' => '用户' . now()->format('Ymd'),
            'password' => password_hash('123456', PASSWORD_BCRYPT)
        ]);
        // 初始化用户总金币表
        UserCoin::create(['user_id' => $user->id]);
        // 初始化消息表
        MessageCount::create(['user_id' => $user->id]);
    }
}
