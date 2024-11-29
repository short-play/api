<?php

namespace App\Exception\Handler;

use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use function Hyperf\Support\now;

class ModelNotFoundHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response): MessageInterface|ResponseInterface
    {
        // 阻止异常冒泡
        $this->stopPropagation();
        // 格式化输出
        $data = json_encode([
            'code' => 404,
            'time' => now()->toDateTimeString(),
            'message' => 'model not found',
            'data' => []
        ]);
        return $this->setResponse($response)->withStatus(200)->withBody(new SwooleStream($data));
    }

    /**
     * 判断该异常处理器是否要对该异常进行处理
     */
    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ModelNotFoundException;
    }
}
