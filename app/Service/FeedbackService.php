<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\Enum\FeedbackStatus;
use App\Kernel\UniqueData;
use App\Model\Feedback;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Collection;

class FeedbackService
{

    /**
     * 获取反馈列表
     * @param array $search
     * @param int $pageSize
     * @return LengthAwarePaginatorInterface
     */
    public function feedbackList(array $search, int $pageSize): LengthAwarePaginatorInterface
    {
        $query = Feedback::with('user')->latest('created_at');
        if (!empty($search['title'])) {
            $query->where('title', 'like', "%{$search['title']}%");
        }
        if (is_numeric($search['status'])) {
            $query->where('status', $search['status']);
        }
        return $query->paginate($pageSize);
    }

    /**
     * 反馈列表
     * @param UniqueData $uniqueData
     * @return Collection
     */
    public function feedbacks(UniqueData $uniqueData): Collection
    {
        return Feedback::where('unique_id', $uniqueData->getUnique())
            ->orderByDesc('created_at')
            ->get(Feedback::$select);
    }

    /**
     * 保存
     * @param UniqueData $uniqueData
     * @param array $data
     * @return void
     */
    public function saveFeedback(UniqueData $uniqueData, array $data): void
    {
        $saveData = array_merge($data, [
            'status' => FeedbackStatus::Unresolved->value,
            'unique_id' => $uniqueData->getUnique(),
        ]);
        Feedback::create($saveData);
    }

    /**
     * 修改反馈状态
     * @param int $id
     * @param FeedbackStatus $feedbackStatus
     * @return void
     */
    public function statusFeedback(int $id, FeedbackStatus $feedbackStatus): void
    {
        Feedback::where('id', $id)->update(['status' => $feedbackStatus->value]);
    }
}