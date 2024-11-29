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

namespace App\Exception\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use function Hyperf\Support\now;

class AppExceptionHandler extends ExceptionHandler
{
    public function __construct(protected LoggerInterface $logger)
    {
    }

    public function handle(Throwable $throwable, ResponseInterface $response): MessageInterface|ResponseInterface
    {
        $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        $this->logger->error($throwable->getTraceAsString());
        if (!isProduction()) {
            return $this->setResponse($response)->withStatus(200)->withBody(new SwooleStream(json_encode([
                'code' => StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                'time' => now()->toDateTimeString(),
                'message' => $throwable->getMessage(),
                'data' => [],
                'traces' => $throwable->getTrace(),
            ])));
        }
        return $this->setResponse($response)->withStatus(200)->withBody(new SwooleStream(json_encode([
            'code' => StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
            'time' => now()->toDateTimeString(),
            'message' => 'Server Error',
            'data' => [],
        ])));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
