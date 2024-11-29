<?php

return [
    'user' => [
        'not_found' => '用户不存在',
        'disable' => '用户已禁用',
        'exists' => '用户已存在',
        'activated' => '已开通会员',
        'not_activated' => '未开通会员'
    ],
    'admin' => [
        'not_found' => '管理员不存在',
        'disabled' => '管理员已禁用',
        'exists' => '管理员已存在',
        'no_operate_self' => '不能操作自己信息'
    ],
    'tag' => [
        'apply_video' => '不可操作,标签已被使用',
        'invalid' => '标签信息无效',
        'apply_ranking' => '不可操作,标签已添加到榜单'
    ],
    'actor' => [
        'apply_video' => '不可操作,演员已被使用',
        'invalid' => '演员信息无效'
    ],
    'video' => [
        'tag_type_limit' => '只能添加 :type 类型的视频',
        'title_exists' => '视频标题重复',
        'rating_empty' => '电影评分不能为空',
        'movie_num_limit' => '电影类型的视频只能为一集',
        'num_lt_update' => '集数不能小于已上传视频数量',
        'update_gt_num' => '上传的视频数量不能大于总集数',
        'removal' => '视频已下架',
        'not_found' => '视频不存在',
        'item_not_found' => '该视频不存在或已删除',
        'add_ranking' => '视频已添加到榜单,不可删除',
        'add_activity' => '视频已添加到活动模版,不可删除'
    ],
    'order' => [
        'paid' => '订单已支付'
    ],
    'message' => [
        'send_user' => '消息已发送用户,不可操作'
    ],
    'activity' => [
        'close' => '活动已关闭',
        'signed_in' => '已签到,不可重复签到',
        'template_empty_no_enabled' => '未配置模版,无法开启活动',
        'watch_insufficient' => '观看时长不足',
        'received_benefit' => '已领取福利',
        'no_received_coin' => '暂无可领取的金币'
    ],
    'global' => [
        'password_error' => '密码错误',
        'gen_random' => '随机值生成失败',
        'code_wrong' => '验证码错误',
        'code_expired' => '验证码已过期',
        'data_invalid' => '数据参数异常',
        'not_logged' => '未登录',
        'device_empty' => '设备信息为空',
        'code_been_send' => '验证码已发送',
        'mail_exists' => '邮箱已存在',
        'unauthorized' => '无权限',
        'locking' => '锁定中,稍后重试',
        'exists' => '数据已存在',
        'data_limit_size' => '数据最多添加 :size 条'
    ],
];
