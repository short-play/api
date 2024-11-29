<?php

declare(strict_types=1);

namespace App\Service;

use App\Kernel\UniqueData;
use App\Model\CommentLike;
use App\Model\CommentReplay;
use App\Model\MessageLike;
use App\Model\MessageReplay;
use App\Model\Video;
use App\Model\VideoItem;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Model;
use Hyperf\DbConnection\Db;
use Hyperf\Contract\LengthAwarePaginatorInterface;

class ReplyService
{

    /**
     * 获取回复详情
     * @param int $replyId
     * @param array $columns
     * @return CommentReplay
     */
    public function getReplyById(int $replyId, array $columns = ['*']): CommentReplay
    {
        return CommentReplay::findOrFail($replyId, $columns);
    }

    /**
     * 根据id获取多个回复
     * @param array $ids
     * @param bool $isRelation
     * @return Collection
     * @noinspection PhpUndefinedMethodInspection
     */
    public function getReplyByIds(array $ids, bool $isRelation = true): Collection
    {
        if (!$isRelation) {
            return CommentReplay::findMany($ids);
        }
        return CommentReplay::whereIn('id', $ids)->with(['user', 'like', 'replyUser'])->get();
    }

    /**
     * 获取单个回复的父级
     * @param int $id
     * @param bool $isRelation
     * @return Collection
     */
    public function getReplyParent(int $id, bool $isRelation = true): Collection
    {
        $ids = [$id];
        $detail = $this->getReplyById($id, ['parent_id']);
        if ($detail->parent_id != $detail->comment_id) {
            $ids[] = $detail->parent_id;
        }
        return $this->getReplyByIds($ids, $isRelation);
    }

    /**
     * 评论回复列表
     * @param UniqueData $u
     * @param int $cId
     * @param array $nIds
     * @param int $s
     * @param int $t
     * @return Collection
     */
    public function getReplyList(UniqueData $u, int $cId, array $nIds, int $s, int $t): Collection
    {
        // 获取一级评论回复列表
        $query = CommentReplay::where('comment_id', $cId)
            ->select(CommentReplay::$select)
            ->orderBy('created_at')
            ->with('replyUser')
            ->with('user');
        // 如果存在nIds则不查询指定数据
        if (!empty($nIds)) {
            $query->whereNotIn('id', $nIds);
        }
        // 如果是用户访问，则增加用户是否点赞的关联
        if ($u->isUser()) {
            $query->with(['like' => fn($q) => $q->where('user_id', $u->getUnique())]);
        }
        return $query->skip($s)->take($t)->get();
    }

    /**
     * 后台管理系统评论回复列表
     * @param int $commentId
     * @param int $pageSize
     * @return LengthAwarePaginatorInterface
     */
    public function adminReplyList(int $commentId, int $pageSize): LengthAwarePaginatorInterface
    {
        // 获取一级评论回复列表
        return CommentReplay::where('comment_id', $commentId)
            ->select(CommentReplay::$select)
            ->orderBy('created_at')
            ->with('replyUser')
            ->with('user')
            ->paginate($pageSize);
    }

    /**
     * 评论回复
     * @param int $uid
     * @param Model $comment
     * @param int $replyId
     * @param string $content
     * @return CommentReplay
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function reply(int $uid, Model $comment, int $replyId, string $content): CommentReplay
    {
        // 获取被回复用户信息
        if ($comment->id == $replyId) {
            $rContent = $comment->content;
            $replyUser = $comment->user()->firstOrFail(['id']);
        } else {
            $reply = CommentReplay::findOrFail($replyId, ['id', 'user_id', 'content']);
            $replyUser = $reply->user()->firstOrFail(['id']);
            $rContent = $reply->content;
        }
        return Db::transaction(function () use ($comment, $uid, $replyId, $replyUser, $content, $rContent) {
            // 增加评论的回复数, 互动数
            $comment->where('id', $comment->id)->incrementEach(
                ['reply_count' => 1, 'interaction_count' => 1]
            );
            // 增加视频的互动数
            Video::where('id', $comment->video_id)->increment('interact_count');
            // 增加视频的评论数
            VideoItem::where('id', $comment->item_id)->increment('comment_count');
            // 增加评论回复
            $commentReplay = CommentReplay::create([
                'user_id' => $uid,
                'content' => $content,
                'parent_id' => $replyId,
                'comment_id' => $comment->id,
                'reply_user_id' => $replyUser->id,
            ]);
            // 自己回复自己，不发消息通知
            if ($replyUser->id != $uid) {
                // 增加消息通知和消息计数
                MessageReplay::insertMessageReplay(intval($comment->user_id), [
                    'message_user_id' => $replyUser->id,
                    'replied_user_id' => $replyUser->id,
                    'reply_user_id' => $uid,
                    'comment_id' => $comment->id,
                    'reply_id' => $commentReplay->id,
                    'replied_id' => $replyId,
                    'replied_content' => $rContent,
                    'reply_content' => $content
                ]);
            }
            return $commentReplay;
        });
    }

    /**
     * 删除回复评论
     * @param int $uid
     * @param int $replayId
     * @return void
     */
    public function delete(int $uid, int $replayId): void
    {
        $replay = CommentReplay::where(['id' => $replayId, 'user_id' => $uid])->firstOrFail();
        $this->deleteCommentReply($replay);
    }

    /**
     * 后台管理系统删除回复评论
     * @param int $replayId
     * @return void
     */
    public function adminDeleteReplay(int $replayId): void
    {
        $replay = CommentReplay::findOrFail($replayId);
        $this->deleteCommentReply($replay);
    }

    /**
     * 删除回复评论
     * @param CommentReplay|Model $replay
     * @return void
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    protected function deleteCommentReply(CommentReplay|Model $replay): void
    {
        // 获取回复的一级评论信息
        $comment = $replay->comment()->select(['id', 'video_id', 'item_id'])->firstOrFail();
        // 事务、执行操作
        DB::transaction(function () use ($replay, $comment) {
            // 减少评论的回复数, 互动数
            $comment->where('id', $comment->id)->decrementEach(
                ['reply_count' => 1, 'interaction_count' => 1]
            );
            // 减少视频的评论数
            VideoItem::where('id', $comment->item_id)->decrement('comment_count');
            // 减少视频的互动数
            Video::where('id', $comment->video_id)->decrement('interact_count');
            // 删除点赞记录
            CommentLike::where('cr_id', $replay->id)->delete();
            // 软删除被人点赞给自己的消息通知
            MessageLike::where(['comment_id' => $comment->id, 'reply_id' => $replay->id])->delete();
            // 软删除被人回复给自己的消息通知
            MessageReplay::where(['comment_id' => $comment->id, 'replied_id' => $replay->id])->delete();
            // 强制删除回复给别人的消息通知
            MessageReplay::where(['comment_id' => $comment->id, 'reply_id' => $replay->id])->forceDelete();
            // 删除回复
            $replay->delete();
        });
    }
}