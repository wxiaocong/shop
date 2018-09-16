<?php

namespace App\Services;

use App\Models\WechatNotice;
use App\Daoes\WechatNoticeDao;
use EasyWeChat\Factory;

class WechatNoticeService 
{
    /**
     * 发送模板消息
     * @param int $user_id
     * @param int $openid
     * @param string $template_id
     * @param array $data
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public static function sendTemplateMessage($user_id, $openid, $orderSn, $template_id, $data = array())
    {
        $wechatNotice = new WechatNotice;
        
        $wechatNotice->user_id = $user_id;
        $wechatNotice->openid = $openid;
        $wechatNotice->template_id = $template_id;
        $wechatNotice->template_data = json_encode($data);
        $wechatNotice->is_send = 1;
        $wechatNotice->is_received = 0;
        
        $wechatNotice->save();
        
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        return $app->template_message->send([
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => config('app.url').'/order/detail/'.$orderSn,
            'data' => $data,
        ]);
    }
    
    public static function findById($noticeId) {
        return WechatNoticeDao::findById($noticeId);
    }
}
