<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Exception\TokenInvalidException;
use Fig\Http\Message\StatusCodeInterface;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Qbhy\HyperfAuth\Exception\AuthException;
use Throwable;

class TokenExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $this->stopPropagation();
        $data = [
            'code' => StatusCodeInterface::STATUS_UNAUTHORIZED,
            'message' => 'UNAUTHORIZED',
            'data' => [],
        ];
        return $this->setResponse($response)->withStatus(200)->withBody(new SwooleStream(json_encode($data)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof TokenInvalidException || $throwable instanceof AuthException;
    }
}