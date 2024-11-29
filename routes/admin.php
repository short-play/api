<?php

declare(strict_types=1);

/**
 * 管理端api
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use App\Middleware\AdminAuthMiddleware;
use Hyperf\HttpServer\Router\Router;

$namespace = 'App\Controller\Admin';
// 登录能访问的接口
$authGroupRoutes = function () use ($namespace) {
    // 获取管理员信息
    Router::post('me', "$namespace\AdminController@me");
    // 退出登录
    Router::post('logout', "$namespace\AdminController@logout");
    // 修改管理员信息
    Router::post('update/me', "$namespace\AdminController@updateAdmin");
    // 修改密码
    Router::post('change/password', "$namespace\AdminController@password");
    // 管理员列表
    Router::post('list', "$namespace\ManageAdminController@index");
    // 添加管理员
    Router::post('create', "$namespace\ManageAdminController@create");
    // 获取详情
    Router::post('detail/{id:\d+}', "$namespace\ManageAdminController@detail");
    // 修改管理员
    Router::post('update/{id:\d+}', "$namespace\ManageAdminController@update");
    // 禁用启用管理员
    Router::post('delete', "$namespace\ManageAdminController@delete");
    // 用户列表
    Router::post('users', "$namespace\UserController@index");
    // 用户详情
    Router::post('user/detail/{id:\d+}', "$namespace\UserController@detail");
    // 禁用启用
    Router::post('user/delete', "$namespace\UserController@delete");
    // tag列表
    Router::post('tags', "$namespace\TagController@index");
    // 创建tag
    Router::post('tag/create', "$namespace\TagController@create");
    // 修改tag
    Router::post('tag/update/{id:\d+}', "$namespace\TagController@update");
    // 删除tag
    Router::post('tag/delete/{id:\d+}', "$namespace\TagController@delete");
    // 演员列表
    Router::post('actors', "$namespace\ActorController@index");
    // 添加演员
    Router::post('actor/create', "$namespace\ActorController@create");
    // 修改演员
    Router::post('actor/update/{id:\d+}', "$namespace\ActorController@update");
    // 删除演员
    Router::post('actor/delete/{id:\d+}', "$namespace\ActorController@delete");
    // 获取协议信息
    Router::post('agreement/detail', "$namespace\AgreementController@detail");
    // 添加或修改协议
    Router::post('agreement/save', "$namespace\AgreementController@create");
    // 榜单列表
    Router::post('ranking/list/{type:\d+}', "$namespace\RankingController@index");
    // 添加榜单
    Router::post('ranking/create/{type:\d+}', "$namespace\RankingController@create");
    // 修改榜单排序
    Router::post('ranking/sort/{id:\d+}', "$namespace\RankingController@update");
    // 删除榜单
    Router::post('ranking/delete', "$namespace\RankingController@delete");
    // 获取订单列表
    Router::post('orders', "$namespace\OrderController@index");
    // 支付订单
    Router::post('order/pay/{no}', "$namespace\OrderController@pay");
    // 消息列表
    Router::post('messages', "$namespace\MessageController@index");
    // 添加消息
    Router::post('message/create', "$namespace\MessageController@create");
    // 修改消息
    Router::post('message/update/{id:\d+}', "$namespace\MessageController@update");
    // 删除消息
    Router::post('message/delete/{id:\d+}', "$namespace\MessageController@delete");
    // 发送消息给用户
    Router::post('message/send/user', "$namespace\MessageController@sendUser");
    // 获取视频封面oss
    Router::post('video/cover-oss', "$namespace\VideoController@coverOss");
    // 获取视频列表
    Router::post('videos', "$namespace\VideoController@index");
    // 视频详情
    Router::post('video/detail/{id:\d+}', "$namespace\VideoController@detail");
    // 视频收藏用户列表
    Router::post('video/collect/{id:\d+}', "$namespace\VideoController@collectUser");
    // 视频创建
    Router::post('video/create', "$namespace\VideoController@create");
    // 修改视频
    Router::post('video/update/{id:\d+}', "$namespace\VideoController@update");
    // 视频完结
    Router::post('video/finish/{id:\d+}', "$namespace\VideoController@finish");
    // 删除视频
    Router::post('video/delete/{id:\d+}', "$namespace\VideoController@delete");
    // 获取视频存储oss
    Router::post('video/item/oss/{id:\d+}', "$namespace\VideoController@itemOss");
    // 视频集合列表
    Router::post('video/{id:\d+}/items', "$namespace\VideoController@items");
    // 视频点赞用户列表
    Router::post('video/item/short/{id:\d+}', "$namespace\VideoController@shortUser");
    // 视频集合保存
    Router::post('video/{id:\d+}/item/create', "$namespace\VideoController@itemCreate");
    // 修改视频是否可看
    Router::post('video/item/view/{id:\d+}', "$namespace\VideoController@itemView");
    // 视频集合删除
    Router::post('video/item/delete/{id:\d+}', "$namespace\VideoController@itemDelete");
    // 获取反馈列表
    Router::post('feedbacks', "$namespace\FeedbackController@index");
    // 修改反馈状态
    Router::post('feedback/status/{id:\d+}', "$namespace\FeedbackController@status");
    // 获取视频一级评论
    Router::post('video/comments', "$namespace\CommentController@index");
    // 删除一级评论
    Router::post('video/comment/delete/{id:\d+}', "$namespace\CommentController@delete");
    // 获取评论回复列表
    Router::post('comment/{id:\d+}/reply', "$namespace\ReplyController@index");
    // 删除评论回复
    Router::post('comment/reply/delete/{id:\d+}', "$namespace\ReplyController@delete");
    // 活动列表
    Router::post('activities', "$namespace\ActivityController@index");
    // 活动详情
    Router::post('activity/detail/{id:\d+}', "$namespace\ActivityController@show");
    // 修改活动
    Router::post('activity/update/{id:\d+}', "$namespace\ActivityController@update");
    // 禁用启用
    Router::post('activity/status/{id:\d+}', "$namespace\ActivityController@status");
    // 创建活动模版
    Router::post('activity/{id:\d+}/template', "$namespace\ActivityController@template");
};

Router::addGroup('/admin/', function () use ($authGroupRoutes, $namespace) {
    // 获取协议type类型
    Router::post('agreements', "App\Controller\EnumController@agreement");
    // 获取榜单类型
    Router::post('ranking/type', "App\Controller\EnumController@ranking");
    // 获取视频枚举信息
    Router::post('video/enum', "App\Controller\EnumController@video");
    // 获取语言
    Router::post('language', "App\Controller\EnumController@language");
    // 获取管理员type
    Router::post('type', "App\Controller\EnumController@adminType");
    // 登录
    Router::post('login', "$namespace\LoginController@login");
    // 发送重置密码验证码
    Router::post('sendForgetPassCode', "$namespace\LoginController@sendCode");
    // 重置密码
    Router::post('forgetPassword', "$namespace\LoginController@forgetPassword");
    // 鉴权接口
    Router::addGroup('', $authGroupRoutes, ['middleware' => [AdminAuthMiddleware::class => 8]]);
});