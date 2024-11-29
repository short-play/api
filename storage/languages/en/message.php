<?php

return [
    'user' => [
        'not_found' => 'User Not Found',
        'disable' => 'User Disabled',
        'exists' => 'User Already Exists',
        'activated' => 'Membership Activated',
        'not_activated' => 'Membership Not Activated'
    ],
    'admin' => [
        'not_found' => 'Admin Not Found',
        'disabled' => 'Admin Disabled',
        'exists' => 'admin exists',
        'no_operate_self' => 'Cant manipulate your own information'
    ],
    'tag' => [
        'apply_video' => 'Not operable, tag has already been used',
        'invalid' => 'tag data invalid',
        'apply_ranking' => 'Not operable, tag has been added to list'
    ],
    'actor' => [
        'apply_video' => 'Not operable, actor has already been used',
        'invalid' => 'actor data invalid'
    ],
    'video' => [
        'tag_type_limit' => 'Only videos of :type genres can be added',
        'title_exists' => 'video title already exists',
        'rating_empty' => 'the movie score is empty',
        'movie_num_limit' => 'Movie type videos can only be one episode',
        'num_lt_update' => 'The number of episodes cannot be Less than the number of uploaded videos',
        'update_gt_num' => 'The number of videos uploaded cannot be greater than the total number of episodes',
        'removal' => 'The video has been taken down',
        'not_found' => 'Video does not exist',
        'item_not_found' => 'The video does not exist or has been removed',
        'add_ranking' => 'The video has been added to the list and cannot be deleted',
        'add_activity' => 'The video has been added to the active template and cannot be deleted'
    ],
    'order' => [
        'paid' => 'order paid'
    ],
    'message' => [
        'send_user' => 'The message has been sent to the user and cannot be operated'
    ],
    'activity' => [
        'close' => 'Activity closed',
        'signed_in' => 'Signed in, cannot be repeated',
        'template_empty_no_enabled' => 'The activity cannot be started because the template is not configured',
        'watch_insufficient' => 'under watch',
        'received_benefit' => 'Received benefits',
        'no_received_coin' => 'Gold coins that are not available for collection'
    ],
    'global' => [
        'password_error' => 'Incorrect Password',
        'gen_random' => 'Random Value Generation Failed',
        'code_wrong' => 'Verification Code Incorrect',
        'code_expired' => 'Verification Code Expired',
        'data_invalid' => 'Data Parameter Invalid',
        'not_logged' => 'Not Logged In',
        'device_empty' => 'Device Information Empty',
        'code_been_send' => 'Verification Code Sent',
        'mail_exists' => 'Mail Exists',
        'unauthorized' => 'Unauthorized',
        'locking' => 'Locked, try again later',
        'exists' => 'data exists',
        'data_limit_size' => 'maximum of :size data items can be added'
    ],
];
