<?php

namespace App\Kernel;

use App\Constants\Enum\AdminRole;
use App\Exception\UnauthorizedException;
use Hyperf\Validation\Request\FormRequest;
use Psr\Container\ContainerInterface;

class BaseRequest extends FormRequest
{

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * 后台管理权限校验
     * @return bool
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    protected function adminAuthorization(): bool
    {
        return admin()->role == AdminRole::SuperAdmin->value;
    }

    protected function failedAuthorization()
    {
        throw new UnauthorizedException();
    }
}
