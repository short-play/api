<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\RedisKey;
use App\Model\Agreement;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Listener\DeleteListenerEvent;
use Hyperf\Di\Annotation\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;

class AgreementService
{

    #[Inject]
    protected EventDispatcherInterface $dispatcher;

    /**
     * 获取字典信息
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    #[Cacheable(
        prefix: RedisKey::AGREEMENT['key'],
        value: RedisKey::AGREEMENT['val'],
        ttl: RedisKey::AGREEMENT['ttl'],
        listener: RedisKey::AGREEMENT['listener']
    )]
    public function getAgreement(string $type, string $language): string
    {
        $agreement = Agreement::where(compact('type', 'language'))->first();
        if (empty($agreement)) {
            return '';
        }
        return $agreement->value;
    }

    /**
     * 添加或修改协议
     * @param string $type
     * @param string $language
     * @param string $value
     * @return void
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function createOrUpdate(string $type, string $language, string $value): void
    {
        $agreement = Agreement::where(compact('type', 'language'))->first();
        if ($agreement == null) {
            Agreement::create(compact('type', 'language', 'value'));
        } else {
            $agreement->value = $value;
            $agreement->save();
        }
        $this->dispatcher->dispatch(new DeleteListenerEvent(RedisKey::AGREEMENT['listener'], [
            'type' => $type,
            'language' => $language,
        ]));
    }
}