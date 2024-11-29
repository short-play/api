<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ValidationExceptionHandler extends ExceptionHandler
{
    /**
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function handle(Throwable $throwable, ResponseInterface $response): MessageInterface|ResponseInterface
    {
        $this->stopPropagation();
        $data = [
            'code' => StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
            'message' => $throwable->validator->errors()->first(),
            'data' => [],
        ];
        return $this->setResponse($response)->withStatus(200)->withBody(new SwooleStream(json_encode($data)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}
