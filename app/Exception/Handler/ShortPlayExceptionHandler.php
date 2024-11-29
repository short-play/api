<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Constants\ErrorCode;
use App\Exception\ShortPlayException;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ShortPlayExceptionHandler extends ExceptionHandler
{
    /**
     * @noinspection PhpParamsInspection
     * @noinspection PhpDynamicAsStaticMethodCallInspection
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     * @noinspection PhpMethodParametersCountMismatchInspection
     */
    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        // 限制冒泡
        $this->stopPropagation();
        // 格式化输出
        $code = $throwable->getCode();
        $data = [
            'code' => $code,
            'message' => ErrorCode::getMessage($code, $throwable->getParams()),
            'data' => [],
        ];
        return $this->setResponse($response)->withStatus(200)->withBody(new SwooleStream(json_encode($data)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ShortPlayException;
    }
}