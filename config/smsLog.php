<?php
//validity 短信验证码过期时间
return [ 
    'type' => array (
        'verifyCode' => array (
                'code' => 1,
                'text' => '用户验证码' 
        ),
        'password' => array (
                'code' => 2,
                'text' => '登录密码' 
        ),
        'registration' => array (
                'code' => 3,
                'text' => '审核通知' 
        ),
        'expiredReminder' => array (
                'code' => 4,
                'text' => '系统到期提醒' 
        ) 
    ),
    'status' => array (
        'success' => array (
                'code' => 0,
                'text' => '发送失败' 
        ),
        'fail' => array (
                'code' => 1,
                'text' => '发送成功' 
        ) 
    ),
    'validity' => 300
];