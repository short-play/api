<?php

declare(strict_types=1);

namespace App\Components\impl;

use App\Components\ResourceSignInterface;
use function Hyperf\Config\config;
use function Hyperf\Support\now;

class AliOssSign implements ResourceSignInterface
{

    /**
     * @param string $prefix
     * @param int $expire
     * @param int $contentLengthRange
     * @return array
     */
    public function signature(string $prefix = '', int $expire = 30, int $contentLengthRange = 1048576000): array
    {
        return $this->postPolicySignature($prefix, $expire, $contentLengthRange);
    }

    /**
     * 服务端生成PostObject所需的签名和Post Policy
     *
     * @param string $prefix
     * @param int $expire
     * @param int $contentLengthRange
     * @return array
     */
    protected function postPolicySignature(string $prefix, int $expire, int $contentLengthRange): array
    {
        $now = time();
        $end = $now + $expire;
        $expiration = $this->gmtIso8601($end);

        // 最大文件大小.用户可以自己设置
        $condition = [
            0 => 'content-length-range',
            1 => 0,
            2 => $contentLengthRange,
        ];
        $conditions[] = $condition;

        $start = [
            0 => 'starts-with',
            1 => '$key',
            2 => $prefix,
        ];
        $conditions[] = $start;

        $arr = [
            'expiration' => $expiration,
            'conditions' => $conditions,
        ];
        $policy = json_encode($arr);
        $base64Policy = base64_encode($policy);
        $stringToSign = $base64Policy;
        $host = 'https://' . config('oss.bucket') . '.' . config('oss.endpoint');
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, config('oss.accessSecret'), true));

        return [
            'host' => $host,
            'accessId' => config('oss.accessId'),
            'policy' => $base64Policy,
            'signature' => $signature,
            'dir' => $prefix,
        ];
    }

    protected function gmtIso8601(int $time): string
    {
        return now('UTC')->setTimestamp($time)->format('Y-m-d\TH:i:s\Z');
    }
}