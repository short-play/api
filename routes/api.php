<?php

declare(strict_types=1);

/**
 * 客户端api
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use App\Middleware\AuthMiddleware;
use Hyperf\HttpServer\Router\Router;
use App\Middleware\UniqueIdMiddleware;

Router::addGroup('/api/', function () {

    // 登录
    Router::post('login', 'App\Controller\Api\LoginController@login');
    // 获取播放地址
    Router::post('video/play', 'App\Controller\Api\VideoController@videoPlay');
    // 获取电视剧列表
    Router::post('tvList', 'App\Controller\Api\VideoController@tvList');
    // 搜索视频
    Router::post('searchVideo', 'App\Controller\Api\VideoController@searchVideo');
    // 获取搜索记录
    Router::post('getSearchHistory', 'App\Controller\Api\SearchController@history');
    // 删除搜索记录
    Router::post('clearSearchHistory', 'App\Controller\Api\SearchController@clear');
    // 视频搜索量收集
    Router::post('video/search/{id:\d+}', 'App\Controller\Api\SearchController@search');
    // 追剧接口
    Router::post('video/follow', 'App\Controller\Api\FollowController@follow');
    // 是否追剧
    Router::post('video/{id:\d+}/followed', 'App\Controller\Api\FollowController@followed');
    // 追剧列表
    Router::post('video/follows', 'App\Controller\Api\FollowController@getFollows');
    // 追剧总数
    Router::post('video/followsCount', 'App\Controller\Api\FollowController@getFollowsCount');
    // 取消追剧
    Router::post('video/unfollow', 'App\Controller\Api\FollowController@unfollow');
    // 浏览记录列表
    Router::post('video/viewList', 'App\Controller\Api\ViewController@viewList');
    // 浏览记录保存
    Router::post('video/view', 'App\Controller\Api\ViewController@view');
    // 浏览记录删除
    Router::post('video/deleteView', 'App\Controller\Api\ViewController@deleteView');
    // 浏览记录详情
    Router::post('video/viewDetail/{id:\d+}', 'App\Controller\Api\ViewController@detail');
    // 点赞
    Router::post('video/like', 'App\Controller\Api\LikeController@like');
    // 是否点赞
    Router::post('video/liked', 'App\Controller\Api\LikeController@liked');
    // 点赞列表
    Router::post('video/likeList', 'App\Controller\Api\LikeController@getLikes');
    // 用户评论一级列表
    Router::post('video/comments', 'App\Controller\Api\CommentController@comments');
    // 评论回复列表
    Router::post('comment/replays', 'App\Controller\Api\ReplyController@replays');
    // 看剧偏好保存
    Router::post('preference', 'App\Controller\Api\PreferenceController@store');
    // 提交反馈
    Router::post('feedback', 'App\Controller\Api\FeedbackController@save');
    // 反馈列表
    Router::post('feedbacks', 'App\Controller\Api\FeedbackController@feedbacks');
    // 获取提交反馈上传图片地址
    Router::post('feedback/uploadUrl', 'App\Controller\Api\FeedbackController@uploadUrl');

}, ['middleware' => [UniqueIdMiddleware::class]]);

// 不用获取唯一id的api
Router::addGroup('/api/', function () {
    // 用户协议
    Router::post('userAgreement', 'App\Controller\Api\AgreementController@userAgreement');
    // 隐私政策
    Router::post('privacyPolicy', 'App\Controller\Api\AgreementController@privacyPolicy');
    // 发送注册验证码
    Router::post('sendRegisterCode', 'App\Controller\Api\LoginController@sendRegisterCodeMail');
    // 注册
    Router::post('register', 'App\Controller\Api\LoginController@register');
    // 发送忘记密码邮件
    Router::post('sendForgetCodeMail', 'App\Controller\Api\LoginController@sendForgetCodeMail');
    // 忘记密码
    Router::post('forgetPassword', 'App\Controller\Api\LoginController@forgetPassword');
    // 获取看剧偏好
    Router::post('preferences', 'App\Controller\Api\PreferenceController@list');
    //  标签tag
    Router::post('tags', 'App\Controller\Api\TagController@tags');
    // 获取首页短视频播放列表
    Router::post('videoPlayList', 'App\Controller\Api\VideoController@videoPlayList');
    // 获取猜你喜欢列表
    Router::post('videoLikeList', 'App\Controller\Api\VideoController@videoLikeList');
    // 获取视频详情
    Router::post('videoDetail/{id:\d+}', 'App\Controller\Api\VideoController@videoDetail');
    // 更多好剧推荐
    Router::post('moreVideo/{type:\d+}', 'App\Controller\Api\VideoController@moreVideo');
    // 获取找剧列表
    Router::post('findVideoList', 'App\Controller\Api\VideoController@findVideoList');
    // 获取电影列表
    Router::post('movieList', 'App\Controller\Api\VideoController@movieList');
    // 获取推荐榜
    Router::post('suggestList', 'App\Controller\Api\RankingController@suggestList');
    // 获取推荐榜
    Router::post('newVideoList', 'App\Controller\Api\RankingController@newVideoList');
    // 短剧热搜榜单
    Router::post('hotSearchList', 'App\Controller\Api\RankingController@hotSearchList');
    // 热搜新剧榜单
    Router::post('hotSearchNewVideoList', 'App\Controller\Api\RankingController@searchNewVideo');
    // 获取热搜标签榜单
    Router::post('hotSearchTagList', 'App\Controller\Api\RankingController@searchTagList');
});

// 用户相关api
Router::addGroup('/api/user', function () {

    // 获取当前用户信息
    Router::post('/me', 'App\Controller\Api\UserController@me');
    // 获取金币总数
    Router::post('/coin', 'App\Controller\Api\UserController@coin');
    // 获取金币总数
    Router::post('/coin/detail', 'App\Controller\Api\UserController@coinDetail');
    // 退出登录
    Router::post('/logout', 'App\Controller\Api\UserController@logout');
    // 开通会员
    Router::post('/member', 'App\Controller\Api\UserController@member');
    // 修改密码
    Router::post('/changePassword', 'App\Controller\Api\UserController@changePassword');
    // 修改用户信息
    Router::post('/updateUser', 'App\Controller\Api\UserController@updateUser');
    // 获取上传头像rul
    Router::post('/profileUploadUrl', 'App\Controller\Api\UserController@profileUploadUrl');
    // 用户评论
    Router::post('/comment', 'App\Controller\Api\CommentController@comment');
    // 删除一级评论
    Router::post('/commentDelete/{id:\d+}', 'App\Controller\Api\CommentController@delete');
    // 评论回复
    Router::post('/comment/reply', 'App\Controller\Api\ReplyController@reply');
    // 删除评论回复
    Router::post('/comment/replyDelete/{id:\d+}', 'App\Controller\Api\ReplyController@delete');
    // 评论点赞、踩
    Router::post('/comment/like', 'App\Controller\Api\CommentLikeController@commentLike');
    // 取消评论点赞、踩
    Router::post('/comment/unlike', 'App\Controller\Api\CommentLikeController@commentUnlike');
    // 回复点赞、踩
    Router::post('/reply/like', 'App\Controller\Api\CommentLikeController@replyLike');
    // 取消回复点赞、踩
    Router::post('/reply/unlike', 'App\Controller\Api\CommentLikeController@replyUnlike');
    // 获取消息未读总数
    Router::post('/message/count', 'App\Controller\Api\MessageController@unReadCount');
    // 获取回复消息列表
    Router::post('/message/reply/list', 'App\Controller\Api\MessageController@replyList');
    // 获取点赞消息列表
    Router::post('/message/like/list', 'App\Controller\Api\MessageController@likeList');
    // 获取通知消息列表
    Router::post('/message/notice/list', 'App\Controller\Api\MessageController@noticeList');
    // 设置消息已读
    Router::post('/message/read/{type:\d+}', 'App\Controller\Api\MessageController@readMessage');
    // 删除消息
    Router::post('/message/delete', 'App\Controller\Api\MessageController@deleteMessage');
    // 回复消息详情
    Router::post('/message/comment/detail/{id:\d+}', 'App\Controller\Api\MessageController@commentDetail');
    // 点赞消息详情
    Router::post('/message/like/detail/{id:\d+}', 'App\Controller\Api\MessageController@likeDetail');
    // 获取活动签到表
    Router::post('/activity/sign/table', 'App\Controller\Api\ActivityController@signTable');
    // 用户签到
    Router::post('/activity/sign-in', 'App\Controller\Api\ActivityController@signIn');
    // 获取看下方剧视频列表
    Router::post('/activity/video', 'App\Controller\Api\ActivityController@appointVideo');
    // 领取看下方剧视频福利
    Router::post('/activity/video/benefit', 'App\Controller\Api\ActivityController@videoBenefit');
    // 获取看剧领金币任务表
    Router::post('/activity/watch/table', 'App\Controller\Api\ActivityController@watchTable');
    // 领取看下方剧视频福利
    Router::post('/activity/watch/benefit', 'App\Controller\Api\ActivityController@watchBenefit');

}, ['middleware' => [AuthMiddleware::class]]);
