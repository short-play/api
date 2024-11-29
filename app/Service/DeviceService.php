<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\Enum\Preference;
use App\Constants\RedisKey;
use App\Kernel\UniqueData;
use App\Model\Device;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Listener\DeleteListenerEvent;
use Hyperf\Di\Annotation\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;

class DeviceService
{

    #[Inject]
    protected EventDispatcherInterface $dispatcher;

    /**
     * 注册设备
     * @param string $device
     * @return array
     */
    #[Cacheable(
        prefix: RedisKey::DEVICE['key'],
        value: RedisKey::DEVICE['val'],
        ttl: RedisKey::DEVICE['ttl'],
        listener: RedisKey::DEVICE['listener']
    )]
    public function getOrSetDevice(string $device): array
    {
        $model = Device::firstOrCreate(['device' => $device]);
        return ['id' => $model->id, 'device' => $model->device, 'preference' => $model->preference];
    }

    /**
     * 修改看剧偏好
     * @param UniqueData $data
     * @param Preference|null $preference
     * @return void
     */
    public function updatePreference(UniqueData $data, ?Preference $preference): void
    {
        $device = $data->getDevice();
        Device::where('id', $device['id'])->update(['preference' => $preference?->value]);
        $this->dispatcher->dispatch(new DeleteListenerEvent(RedisKey::DEVICE['listener'], [
            'device' => $device['device']
        ]));
    }
}