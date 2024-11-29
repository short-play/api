<?php

declare(strict_types=1);

namespace App\Exception;

use Hyperf\Server\Exception\ServerException;
use Throwable;

/**
 * 项目异常
 */
class ShortPlayException extends ServerException
{

    protected array $params = [];

    /**
     * @param int $code
     * @param array $params
     * @param Throwable|null $previous
     */
    public function __construct(int $code = 0, $params = [], ?Throwable $previous = null)
    {
        $this->params = $params;
        parent::__construct('', $code, $previous);
    }

    public function getParams(): array
    {
        return $this->params;
    }
}