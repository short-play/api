<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\TokenInvalidException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Qbhy\HyperfAuth\AuthMiddleware as JWTAuthMiddleware;

class AuthMiddleware extends JWTAuthMiddleware
{
    /**
     * 用户身份鉴权
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->auth->guard()->check()) {
            throw new TokenInvalidException();
        }
        return $handler->handle($request);
    }
}
