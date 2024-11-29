<?php

use function Hyperf\Support\env;

return [
    'host' => env('MAIL_HOST', 'smtp.qq.com'),
    'port' => env('MAIL_PORT', 465),
    'username' => env('MAIL_USERNAME', ''),
    'password' => env('MAIL_PASSWORD', ''),
    'encryption' => env('MAIL_ENCRYPTION', 'ssl'),
    'from_address' => env('MAIL_FROM_ADDRESS', ''),
    'from_name' => env('MAIL_FROM_NAME', 'short'),
];