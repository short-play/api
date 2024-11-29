<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\Enum\LikeType;
use App\Model\Comment;
use App\Model\CommentLike;
use App\Model\CommentReplay;
use App\Model\MessageCount;
use App\Model\MessageLike;
use Hyperf\Database\Model\Model;
use Hyperf\DbConnection\Db;

class CommentLikeService
{

    /**
     * 点赞详情
     * @param int $uid
     * @param int|string $crId
     * @return Model|null
     */
    public function likeDetail(int $uid, int|string $crId): ?Model
    {
        return CommentLike::where(['user_id' => $uid, 'cr_id' => $crId])->first();
    }

    /**
     * 评论取消点赞和踩
     * @param int $uid
     * @param int $crId
     * @return void
     */
    public function cancelCommentLike(int $uid, int $crId): void
    {
        $likeDetail = $this->likeDetail($uid, $crId);
        if ($likeDetail == null) {
            return;
        }
        Db::transaction(function () use ($uid, $likeDetail) {
            // 如果不存在点赞记录，则不做任何操作或者是踩
            if ($likeDetail->type == LikeType::Dislike->value) {
                $likeDetail->delete();
                return;
            }
            // 删除点赞消息
            $this->deleteLikeMessage($uid, $likeDetail->cr_id);
            // 减少
            Comment::where('id', $likeDetail->cr_id)->decrementEach(
                ['interaction_count' => 1, 'like_count' => 1]
            );
            $likeDetail->delete();
        });
    }

    /**
     * 取消回复点赞和踩
     * @param int $uid
     * @param int $crId
     * @return void
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function cancelReplyLike(int $uid, int $crId): void
    {
        $likeDetail = $this->likeDetail($uid, $crId);
        if ($likeDetail == null) {
            return;
        }
        Db::transaction(function () use ($uid, $likeDetail) {
            // 如果不存在点赞记录，则不做任何操作或者是踩
            if ($likeDetail->type == LikeType::Dislike->value) {
                $likeDetail->delete();
                return;
            }
            // 删除消息回复
            $this->deleteLikeMessage($uid, $likeDetail->cr_id);
            // 减少点赞量
            CommentReplay::where('id', $likeDetail->cr_id)->decrement('like_count');
            // 减少总互动量
            Comment::where('id', $likeDetail->comment_id)->decrement('interaction_count');
            // 删除
            $likeDetail->delete();
        });
    }

    /**
     * 评论点赞
     * @param int $uid
     * @param Model $model
     * @param LikeType $type
     * @return void
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function likeStore(int $uid, Model $model, LikeType $type): void
    {
        Db::transaction(function () use ($uid, $model, $type) {
            // 获取是否已点赞或踩
            $commentLike = $this->likeDetail($uid, $model->id);
            // 执行点赞或踩和更新点赞数、评论互动数
            match ($commentLike?->type) {
                null => $this->createLike($uid, $model, $type),
                default => $this->toggleLike($commentLike, $model, $type),
            };
        });
    }

    /**
     * 添加点赞
     * @param int $uid
     * @param Model $model
     * @param LikeType $type
     * @return void
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    protected function createLike(int $uid, Model $model, LikeType $type): void
    {
        CommentLike::create([
            'user_id' => $uid,
            'cr_id' => $model->id,
            'type' => $type->value,
            'comment_id' => $model instanceof CommentReplay ? $model->comment_id : $model->id,
        ]);
        $this->incrementCounts($uid, $model, $type, true);
    }

    /**
     * 根据条件判断是否执行操作
     * @param Model $commentLike
     * @param Model $model
     * @param LikeType $type
     * @return void
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    protected function toggleLike(Model $commentLike, Model $model, LikeType $type): void
    {
        if ($this->shouldToggle($commentLike, $type)) {
            $commentLike->type = $type->value;
            $commentLike->save();
            $this->incrementCounts(intval($commentLike->user_id), $model, $type);
        }
    }

    /**
     * 添加点赞数量统计
     * @param int $uid
     * @param Model $model
     * @param LikeType $type
     * @param bool $iCr
     * @return void
     */
    protected function incrementCounts(int $uid, Model $model, LikeType $type, bool $iCr = false): void
    {
        if ($model instanceof CommentReplay) {
            $this->incrementReplayCounts($uid, $model, $type, $iCr);
        } else {
            $this->incrementCommentCounts($uid, $model, $type, $iCr);
        }
    }

    /**
     * 回复评论点赞计数统计
     * @param int $uid
     * @param Model $replay
     * @param LikeType $type
     * @param bool $iCr
     * @return void
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    protected function incrementReplayCounts(int $uid, Model $replay, LikeType $type, bool $iCr): void
    {
        if ($type === LikeType::Like) {
            $replay->increment('like_count');
            $this->addLikeMessage($uid, $replay);
            $replay->comment()->increment('interaction_count');
        } elseif (!$iCr) {
            $replay->decrement('like_count');
            $this->deleteLikeMessage($uid, $replay->id);
            $replay->comment()->decrement('interaction_count');
        }
    }

    /**
     * 评论点赞计数统计
     * @param int $uid
     * @param Model $c
     * @param LikeType $type
     * @param bool $iCr
     * @return void
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    protected function incrementCommentCounts(int $uid, Model $c, LikeType $type, bool $iCr): void
    {
        $field = ['interaction_count' => 1, 'like_count' => 1];
        if ($type === LikeType::Like) {
            $this->addLikeMessage($uid, $c);
            $c->where('id', $c->id)->incrementEach($field);
        } elseif (!$iCr) {
            $this->deleteLikeMessage($uid, $c->id);
            $c->where('id', $c->id)->decrementEach($field);
        }
    }

    /**
     * 添加点赞消息记录信息
     * @param int $uid
     * @param Model $model
     * @return void
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    protected function addLikeMessage(int $uid, Model $model): void
    {
        MessageLike::create([
            'like_user_id' => $uid,
            'reply_id' => $model->id,
            'content' => $model->content,
            'message_user_id' => $model->user_id,
            'comment_id' => $model instanceof CommentReplay ? $model->comment_id : $model->id,
        ]);
        MessageCount::where('user_id', $model->user_id)->increment('like_unread_count');
    }

    /**
     * 删除点赞消息
     * @param int $uid
     * @param $replyId
     * @return void
     */
    protected function deleteLikeMessage(int $uid, $replyId): void
    {
        MessageLike::where(['like_user_id' => $uid, 'reply_id' => $replyId])->forceDelete();
    }

    /**
     * 条件
     * @param Model $commentLike
     * @param LikeType $type
     * @return bool
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    protected function shouldToggle(Model $commentLike, LikeType $type): bool
    {
        return $commentLike->type != $type->value;
    }
}