<?php

declare(strict_types=1);

namespace App\Service;

use App\Components\MailInterface;
use App\Constants\ErrorCode;
use App\Constants\ProjectConfig;
use App\Constants\RedisKey;
use App\Exception\ShortPlayException;
use App\Mails\SendForgetCodeEmail;
use App\Mails\SendRegisterCodeEmail;
use App\Model\MessageCount;
use App\Model\User;
use App\Model\UserCoin;
use Hyperf\Database\Model\Model;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use function Hyperf\Support\now;

class LoginService
{

    #[Inject]
    protected MailInterface $mail;

    /**
     * 登录
     * @param string $email
     * @param string $password
     * @return array
     */
    public function login(string $email, string $password): array
    {
        $userInfo = User::withTrashed()->where('mail', $email)
            ->first(['id', 'password', 'deleted_at']);
        if ($userInfo == null) {
            throw new ShortPlayException(ErrorCode::USER_NOT_FOUND->value);
        }
        if ($userInfo->deleted_at !== null) {
            throw new ShortPlayException(ErrorCode::USER_DISABLE->value);
        }
        // 校验密码
        if (!password_verify($password, $userInfo->password)) {
            throw new ShortPlayException(ErrorCode::PASSWORD_ERROR->value);
        }
        return array($userInfo, auth()->login($userInfo));
    }

    /**
     * 发送注册验证码
     * @param string $email
     * @return void
     * @noinspection PhpComposerExtensionStubsInspection
     */
    public function sendRegisterCodeMail(string $email): void
    {
        $userInfo = User::withTrashed()->where('mail', $email)->first(['id']);
        if ($userInfo) {
            throw new ShortPlayException(ErrorCode::USER_EXISTS->value);
        }
        $code = genRandomInt(100000, 999999);
        $lockKey = RedisKey::USER_REGISTER_CODE . $email;
        try {
            $res = redis()->set($lockKey, $code, ['nx', 'ex' => ProjectConfig::CODE_EXPIRE_SECOND]);
            if ($res === false) {
                throw new ShortPlayException(ErrorCode::CODE_BEEN_SEND->value);
            }
            // 发送邮件
            $this->mail->send(new SendRegisterCodeEmail($email, $code));
        } catch (\RedisException $e) {
            logger()->error('发送注册验证码错误', [$e->getMessage(), $e]);
            throw new ShortPlayException(ErrorCode::REDIS_ERROR->value);
        }
    }

    /**
     * 用户注册
     * @param string $email
     * @param string $password
     * @param int $requestCode
     * @return void
     * @noinspection PhpComposerExtensionStubsInspection
     */
    public function register(string $email, string $password, int $requestCode): void
    {
        // 校验验证码
        $this->verifyCode(RedisKey::USER_REGISTER_CODE . $email, $requestCode);
        $user = User::withTrashed()->where('mail', $email)->first();
        if ($user) {
            throw new ShortPlayException(ErrorCode::USER_EXISTS->value);
        }
        try {
            redis()->del(RedisKey::USER_REGISTER_CODE . $email);
            Db::transaction(function () use ($email, $password) {
                // 创建用户
                $user = User::create([
                    'mail' => $email,
                    'name' => '用户' . now()->format('Ymd'),
                    'password' => password_hash($password, PASSWORD_BCRYPT)
                ]);
                // 初始化用户总金币表
                UserCoin::create(['user_id' => $user->id]);
                // 初始化消息表
                MessageCount::create(['user_id' => $user->id]);
            });
        } catch (\RedisException $e) {
            logger()->error('用户注册错误', [$e->getMessage(), $e]);
            throw new ShortPlayException(ErrorCode::REDIS_ERROR->value);
        }
    }

    /**
     * 发送重置密码验证码
     * @param string $email
     * @return void
     * @noinspection PhpComposerExtensionStubsInspection
     */
    public function sendForgetCodeMail(string $email): void
    {
        $userInfo = User::where('mail', $email)->first(['id']);
        if ($userInfo == null) {
            throw new ShortPlayException(ErrorCode::USER_NOT_FOUND->value);
        }
        $code = genRandomInt(100000, 999999);
        $lockKey = RedisKey::USER_FORGET_CODE . $email;
        try {
            $res = redis()->set($lockKey, $code, ['nx', 'ex' => ProjectConfig::CODE_EXPIRE_SECOND]);
            if ($res === false) {
                throw new ShortPlayException(ErrorCode::CODE_BEEN_SEND->value);
            }
            // 发送邮件
            $this->mail->send(new SendForgetCodeEmail($email, $code));
        } catch (\RedisException $e) {
            logger()->error('发送重置密码验证码错误', [$e->getMessage(), $e]);
            throw new ShortPlayException(ErrorCode::REDIS_ERROR->value);
        }
    }

    /**
     * 重置密码
     * @param string $email
     * @param string $password
     * @param int $requestCode
     * @return Model
     * @noinspection PhpComposerExtensionStubsInspection
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function forgetPassword(string $email, string $password, int $requestCode): Model
    {
        // 校验验证码
        $this->verifyCode(RedisKey::USER_FORGET_CODE . $email, $requestCode);
        $user = User::where('mail', $email)->first(['id']);
        if ($user == null) {
            throw new ShortPlayException(ErrorCode::USER_NOT_FOUND->value);
        }
        try {
            redis()->del(RedisKey::USER_FORGET_CODE . $email);
            $user->password = password_hash($password, PASSWORD_BCRYPT);
            $user->save();
            return $user;
        } catch (\RedisException $e) {
            logger()->error('重置密码错误', [$e->getMessage(), $e]);
            throw new ShortPlayException(ErrorCode::REDIS_ERROR->value);
        }
    }

    /**
     * 校验验证码
     * @param string $redisKey
     * @param int $code
     * @return void
     * @noinspection PhpComposerExtensionStubsInspection
     */
    private function verifyCode(string $redisKey, int $code): void
    {
        try {
            $resCode = redis()->get($redisKey);
            if ($resCode == null) {
                throw new ShortPlayException(ErrorCode::CODE_EXPIRED->value);
            }
            if ($code != $resCode) {
                throw new ShortPlayException(ErrorCode::CODE_WRONG->value);
            }
        } catch (\RedisException $e) {
            logger()->error('verifyCode错误', [$e->getMessage(), $e]);
            throw new ShortPlayException(ErrorCode::REDIS_ERROR->value);
        }
    }
}