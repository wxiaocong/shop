<?php

namespace App\Services;

use App\Models\WechatNotify;

class WechatNotifyService 
{
    //保存微信通知信息
    public static function store($message) {
        $notify = new WechatNotify();
        $notify->message = json_encode($message);
        return $notify->save();
    }
}
