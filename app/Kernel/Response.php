<?php

declare(strict_types=1);

namespace App\Kernel;

use Fig\Http\Message\StatusCodeInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Http\Message\ResponseInterface as ResponseInterfaceAlias;

class Response
{
    #[Inject]
    protected ResponseInterface $response;

    /**
     * @param array $data
     * @param string $message
     * @return ResponseInterfaceAlias
     */
    public function success(array $data = [], string $message = 'success'): ResponseInterfaceAlias
    {
        return $this->response->json([
            'code' => StatusCodeInterface::STATUS_OK,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * @param ResourceInterface $resource
     * @param string $message
     * @return ResponseInterfaceAlias
     */
    public function resource(ResourceInterface $resource, string $message = 'success'): ResponseInterfaceAlias
    {
        return $this->success($resource->toArray(), $message);
    }
}
