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
return [
    'handler' => [
        'http' => [
            App\Exception\Handler\ValidationExceptionHandler::class,
            App\Exception\Handler\ShortPlayExceptionHandler::class,
            App\Exception\Handler\HttpExceptionHandler::class,
            App\Exception\Handler\TokenExceptionHandler::class,
            App\Exception\Handler\ModelNotFoundHandler::class,
            App\Exception\Handler\AppExceptionHandler::class,
        ],
    ],
];
