<?php

declare(strict_types=1);

use function Hyperf\Support\env;

return [
    'endpoint' => env('ENDPOINT', ''),
    'accessId' => env('ACCESS_KEY_ID', ''),
    'accessSecret' => env('ACCESS_KEY_SECRET', ''),
    'bucket' => env('BUCKET', ''),
];