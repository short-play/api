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

// 加载路由文件
$path = BASE_PATH . '/routes';

$files = scandir($path);
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        require_once $path . '/' . basename($file);
    }
}