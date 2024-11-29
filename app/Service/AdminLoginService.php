<?php

declare(strict_types=1);

namespace App\Service;

use App\Components\MailInterface;
use App\Constants\ErrorCode;
use App\Constants\ProjectConfig;
use App\Constants\RedisKey;
use App\Exception\ShortPlayException;
use App\Mails\SendForgetCodeEmail;
use App\Model\Admin;
use Hyperf\Database\Model\Model;
use Hyperf\Di\Annotation\Inject;

class AdminLoginService
{

    #[Inject]
    protected MailInterface $mail;

    /**
     * 管理员登录
     * @param string $email
     * @param int|string $password
     * @return string
     */
    public function login(string $email, int|string $password): string
    {
        $admin = Admin::withTrashed()->where('mail', $email)->first();
        if ($admin == null) {
            throw new ShortPlayException(ErrorCode::ADMIN_NOT_FOUND->value);
        }
        if ($admin->deleted_at !== null) {
            throw new ShortPlayException(ErrorCode::ADMIN_DISABLED->value);
        }
        // 校验密码
        if (!password_verify($password, $admin->password)) {
            throw new ShortPlayException(ErrorCode::PASSWORD_ERROR->value);
        }
        return auth('admin')->login($admin);
    }

    /**
     * 发送重置密码验证码
     * @param string $email
     * @return void
     * @noinspection PhpComposerExtensionStubsInspection
     */
    public function sendForgetPassCode(string $email): void
    {
        $admin = Admin::where('mail', $email)->first(['id']);
        if (empty($admin)) {
            throw new ShortPlayException(ErrorCode::ADMIN_NOT_FOUND->value);
        }
        $code = genRandomInt(100000, 999999);
        $lockKey = RedisKey::ADMIN_FORGET_CODE . $email;
        try {
            $res = redis()->set($lockKey, $code, ['nx', 'ex' => ProjectConfig::CODE_EXPIRE_SECOND]);
            if ($res === false) {
                throw new ShortPlayException(ErrorCode::CODE_BEEN_SEND->value);
            }
            // 发送邮件
            $this->mail->send(new SendForgetCodeEmail($email, $code));
        } catch (\RedisException $e) {
            logger()->error('发送重置验证码失败', [$e->getMessage(), $e]);
            throw new ShortPlayException(ErrorCode::REDIS_ERROR->value);
        }
    }

    /**
     * 重置密码
     * @param string $email
     * @param string $password
     * @param int $code
     * @return Model
     * @noinspection PhpComposerExtensionStubsInspection
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function forgetPassword(string $email, string $password, int $code): Model
    {
        try {
            $key = RedisKey::ADMIN_FORGET_CODE . $email;
            $resCode = redis()->get($key);
            if ($resCode == null) {
                throw new ShortPlayException(ErrorCode::CODE_EXPIRED->value);
            }
            if ($code != $resCode) {
                throw new ShortPlayException(ErrorCode::CODE_WRONG->value);
            }
            $admin = Admin::where('mail', $email)->first(['id']);
            if ($admin == null) {
                throw new ShortPlayException(ErrorCode::ADMIN_NOT_FOUND->value);
            }
            redis()->del($key);
            $admin->password = password_hash($password, PASSWORD_BCRYPT);
            $admin->save();
            return $admin;
        } catch (\RedisException $e) {
            logger()->error('重置密码失败', [$e->getMessage(), $e]);
            throw new ShortPlayException(ErrorCode::REDIS_ERROR->value);
        }
    }
}