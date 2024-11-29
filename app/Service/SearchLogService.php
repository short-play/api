<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\Enum\VideoType;
use App\Constants\ProjectConfig;
use App\Constants\RedisKey;
use App\Kernel\UniqueData;
use App\Model\SearchHistory;
use App\Model\Tag;
use App\Model\Video;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Listener\DeleteListenerEvent;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;

class SearchLogService
{

    #[Inject]
    protected EventDispatcherInterface $dispatcher;

    /**
     * 记录搜索历史
     * @param string $value
     * @param UniqueData $unique
     * @return void
     */
    public function storeHistory(UniqueData $unique, string $value): void
    {
        $history = $this->getHistory($unique);
        if (in_array($value, array_column($history, 'value'))) {
            return;
        }
        Db::transaction(function () use ($unique, $value, $history) {
            // 超过搜索量, 删除最老一条数据
            if (count($history) == ProjectConfig::SEARCH_HISTORY_MAX_LIMIT) {
                $id = $history[array_key_last($history)]['id'];
                SearchHistory::where(compact('id'))->delete();
            }
            // 添加搜索历史
            SearchHistory::create(['unique_id' => $unique->getUnique(), 'value' => $value]);
            // 删除历史记录缓存
            $this->dispatcher->dispatch(
                new DeleteListenerEvent(RedisKey::SEARCH_HISTORY['listener'], ['data' => [
                    'unique' => $unique->getUnique()
                ]])
            );
        });
    }

    /**
     * 获取搜索历史
     * @param UniqueData $data
     * @return array
     */
    #[Cacheable(
        prefix: RedisKey::SEARCH_HISTORY['key'],
        value: RedisKey::SEARCH_HISTORY['val'],
        ttl: RedisKey::SEARCH_HISTORY['ttl'],
        listener: RedisKey::SEARCH_HISTORY['listener']
    )]
    public function getHistory(UniqueData $data): array
    {
        return SearchHistory::where('unique_id', $data->getUnique())
            ->orderByDesc('created_at')
            ->get(['id', 'value'])
            ->toArray();
    }

    /**
     * 清除搜索记录
     * @param UniqueData $unique
     * @return void
     */
    public function clearHistory(UniqueData $unique): void
    {
        Db::transaction(function () use ($unique) {
            SearchHistory::where('unique_id', $unique->getUnique())->delete();
            // 删除历史记录缓存
            $this->dispatcher->dispatch(
                new DeleteListenerEvent(RedisKey::SEARCH_HISTORY['listener'], ['data' => [
                    'unique' => $unique->getUnique()
                ]])
            );
        });
    }

    /**
     * 视频搜索量统计 标签搜索量统计
     * @param array $video
     * @return void
     */
    public function statisticsSearchVideo(array $video): void
    {
        if ($video['type'] != VideoType::Short->value) {
            return;
        }
        Db::transaction(function () use ($video) {
            // 根据视频搜索量
            Video::where('id', $video['id'])->increment('search_count');
            // 更新tag搜索量
            $tagIds = array_map(fn($v) => $v['pivot']['tag_id'], $video['tags'] ?? []);
            Tag::whereIn('id', $tagIds)->increment('search_count');
        });
    }
}