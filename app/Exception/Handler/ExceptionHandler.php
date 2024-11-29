<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use Psr\Http\Message\ResponseInterface;

abstract class ExceptionHandler extends \Hyperf\ExceptionHandler\ExceptionHandler
{
    protected function setResponse(ResponseInterface $response): ResponseInterface
    {
        return $response->withHeader('Content-Type', 'application/json;charset=utf-8');
    }
}