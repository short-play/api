<?php

declare(strict_types=1);

namespace App\Service;

use App\Kernel\UniqueData;
use App\Model\Comment;
use App\Model\CommentLike;
use App\Model\CommentReplay;
use App\Model\Video;
use App\Model\VideoItem;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Model;
use Hyperf\DbConnection\Db;

class CommentService
{

    /**
     * 获取评论详情
     * @param int $commentId
     * @param array $columns
     * @return Model
     */
    public function getCommentById(int $commentId, array $columns = ['*']): Model
    {
        return Comment::findOrFail($commentId, $columns);
    }

    /**
     * 获取评论详情
     * @param int $commentId
     * @param array $columns
     * @return Model
     */
    public function commentRelationById(int $commentId, array $columns = ['*']): Model
    {
        return Comment::where('id', $commentId)
            ->with(['user', 'like'])
            ->firstOrFail($columns);
    }

    /**
     * 获取后台管理系统评论列表
     * @param array $search
     * @param int $pageSize
     * @return LengthAwarePaginatorInterface
     */
    public function commentAdminList(array $search, int $pageSize): LengthAwarePaginatorInterface
    {
        return Comment::where('video_id', $search['video_id'])
            ->where('item_id', $search['item_id'])
            ->orderByDesc('created_at')
            ->with('user')
            ->paginate($pageSize);
    }

    /**
     * 获取评论列表
     * @param UniqueData $u
     * @param array $item
     * @param array $nIds
     * @param int $s
     * @param int $t
     * @return Collection
     */
    public function commentList(UniqueData $u, array $item, array $nIds, int $s, int $t): Collection
    {
        $query = Comment::where('video_id', $item['video_id'])
            ->where('item_id', $item['id'])
            ->orderByDesc('interaction_count')
            ->orderByDesc('created_at')
            ->with('user');
        // 如果存在不查询指定id
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
     * 评论保存
     * @param int $uid
     * @param array $item
     * @param string $content
     * @return Comment
     */
    public function store(int $uid, array $item, string $content): Comment
    {
        return Db::transaction(function () use ($uid, $item, $content) {
            // 添加视频评论数
            VideoItem::where('id', $item['id'])->increment('comment_count');
            // 添加总互动数
            Video::where('id', $item['video_id'])->increment('interact_count');
            return Comment::create([
                'user_id' => $uid,
                'item_id' => $item['id'],
                'content' => $content,
                'video_id' => $item['video_id'],
            ]);
        });
    }

    /**
     * 删除评论
     * @param int $uid
     * @param int $commentId
     * @return void
     */
    public function delete(int $uid, int $commentId): void
    {
        $comment = Comment::where(['id' => $commentId, 'user_id' => $uid])->firstOrFail();
        $this->deleteComment($comment);
    }

    /**
     * 后台管理系统删除评论
     * @param int $commentId
     * @return void
     */
    public function adminDeleteComment(int $commentId): void
    {
        $comment = Comment::findOrFail($commentId);
        $this->deleteComment($comment);
    }

    /**
     * 删除评论
     * @param Comment|Model $comment
     * @return void
     */
    protected function deleteComment(Comment|Model $comment): void
    {
        Db::transaction(function () use ($comment) {
            $delReplyNum = 1;
            // 如果有回复评论则删除
            if ($comment->reply_count > 0) {
                $delReplyNum += CommentReplay::where('comment_id', $comment->id)->delete();
            }
            // 删除评论所有点赞
            $likeDelCount = CommentLike::where('comment_id', $comment->id)->delete();
            // 递减视频评论数
            VideoItem::where('id', $comment->item_id)->decrement(
                'comment_count', $delReplyNum
            );
            // 递减视频总互动数
            Video::where('id', $comment->video_id)->decrement(
                'interact_count', $delReplyNum + $likeDelCount
            );
            // 删除一级评论
            $comment->delete();
        });
    }
}