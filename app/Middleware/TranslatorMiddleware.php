<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\Enum\Language;
use Hyperf\Config\Annotation\Value;
use Hyperf\Contract\TranslatorInterface;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TranslatorMiddleware implements MiddlewareInterface
{

    #[Value('translation.locale')]
    protected string $defaultLocale;

    #[Inject]
    protected TranslatorInterface $translator;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $lang = $request->getHeaderLine('Accept-Language') ?? '';
        $locale = $this->determineLocale($lang);
        $this->translator->setLocale($locale);
        return $handler->handle($request);
    }

    private function determineLocale(string $acceptLanguage): string
    {
        $localesWithQ = [];
        $supportLocales = Language::values();
        // 分解 Accept-Language 头
        $languages = explode(',', $acceptLanguage);
        foreach ($languages as $language) {
            // 分离语言代码和 q 值
            if (str_contains($language, ';q=')) {
                [$lang, $qValue] = explode(';q=', $language);
                $localesWithQ[trim($lang)] = (float)$qValue;
            } else {
                // 没有 q 值的语言，默认 q=1
                $localesWithQ[trim($language)] = 1.0;
            }
        }

        // 根据 q 值排序
        arsort($localesWithQ);

        // 尝试匹配支持的语言
        foreach ($localesWithQ as $locale => $q) {
            // 短横线 => 下划线
            $locale = str_replace('-', '_', $locale);
            if (in_array($locale, $supportLocales)) {
                return $locale;
            }
        }

        // 尝试匹配支持的语言
        foreach ($localesWithQ as $locale => $q) {
            // 短横线 => 下划线
            // 只考虑前两位语言代码
            $localePrefix = substr($locale, 0, 2);
            if (in_array($localePrefix, $supportLocales)) {
                return $localePrefix;
            }
        }

        // 如果没有匹配项，返回默认语言
        return $this->defaultLocale;
    }
}
