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

namespace App\Controller;

use App\Kernel\Response;
use App\Kernel\UniqueData;
use Hyperf\Contract\TranslatorInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Container\ContainerInterface;

abstract class AbstractController
{
    #[Inject]
    protected ContainerInterface $container;

    #[Inject]
    protected RequestInterface $request;

    #[Inject]
    protected Response $response;

    #[Inject]
    protected TranslatorInterface $translator;

    /**
     * 获取分页参数
     * [skip, take]
     * @return array
     */
    public function getSkipAndTake(): array
    {
        $page = $this->getPage();
        $pageSize = $this->getPageSize();
        $skip = ($page - 1) * $pageSize;
        return [$skip, $pageSize];
    }

    /**
     * 获取默认分页页码
     * @return int
     */
    protected function getPage(): int
    {
        $page = $this->request->input('page', 1);
        if (!is_numeric($page) || $page < 1) {
            $page = 1;
        }
        return intval($page);
    }

    /**
     * 获取默认分页大小
     * @return int
     */
    protected function getPageSize(): int
    {
        $whiteList = [5, 10, 20];
        $pageSize = $this->request->input('limit', 20);
        if (!is_numeric($pageSize) || !in_array($pageSize, $whiteList)) {
            $pageSize = $whiteList[0];
        }
        return intval($pageSize);
    }

    /**
     * 获取当前请求的语言环境
     * @return string
     */
    protected function getLanguage(): string
    {
        return $this->translator->getLocale();
    }

    /**
     * 登录身份信息
     * @return UniqueData
     */
    final protected function requestUnique(): UniqueData
    {
        return $this->request->getAttribute(UniqueData::class);
    }
}
