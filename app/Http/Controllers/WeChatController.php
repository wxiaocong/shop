<?php

namespace App\Http\Controllers;

use App\Services\GoodsSpecService;
use App\Services\OrderRefundService;
use App\Services\OrderService;
use App\Services\PayLogsService;
use App\Services\Users\UserService;
use App\Services\WechatNoticeService;
use App\Services\WechatNotifyService;
use EasyWeChat;
use Illuminate\Support\Facades\DB;
use Session;

class WeChatController extends Controller {
    //网页授权
    public function oauthCallback()
    {
        $app = EasyWeChat::officialAccount();
        $oauth = $app->oauth;
        // 获取 OAuth 授权结果用户信息
        $user = $oauth->user()->toArray();
        $openid = $user['id'];
        //获取用户头像等信息
        $user = $app->user->get($openid);
        if (!empty($user)) {
            //保存微信通知数据
            WechatNotifyService::store($user);
            $userInfo = UserService::findByOpenid($openid);
            $wechatUserData = array(
                'subscribe' => $user['subscribe'],
                'subscribe_time' => $user['subscribe_time'],
                'nickname' => $user['nickname'],
                'headimgurl' => $user['headimgurl'],
                'city' => $user['city'],
                'province' => $user['province'],
                'country' => $user['country'],
                'sex' => $user['sex'],
            );
            UserService::saveOrUpdate($openid, $wechatUserData);
            session(array('user' => UserService::findByOpenid($openid)));
        }
        $targetUrl = empty(session('target_url')) ? config('app.url') : session('target_url');
        session::forget('target_url');
        return redirect($targetUrl);
    }

    //微信支付通知
    public function payNotice()
    {
        $app = EasyWeChat::payment();
        $response = $app->handlePaidNotify(function ($message, $fail) {
            //保存微信通知数据
            WechatNotifyService::store($message);
            //微信查询订单状态
            $searchApp = EasyWeChat::payment();
            $result = $searchApp->order->queryByOutTradeNumber($message['out_trade_no']);
            if ($result['return_code'] === 'SUCCESS') {
                if ($result['trade_state'] === 'SUCCESS') {
                    //查询订单
                    $orderSn = substr($message['out_trade_no'], 0, 22);
                    $orderInfo = OrderService::findAddGoodByOrderSn($orderSn);
                    //未找到订单或订单不是未付款状态，退款
                    if (empty($orderInfo) || $orderInfo->state != 1) {
                        OrderRefundService::wechatRefund($message['out_trade_no'], $message['out_trade_no'] . time(), $message['total_fee'], $result['cash_fee']);
                        return true;
                    }
                    $pay_time = date('Y-m-d H:i:s', strtotime($result['time_end']));
                    $updateData = array(
                        'real_pay' => $result['cash_fee'], //实付款
                        'pay_time' => $pay_time, //付款时间
                        'transaction_id' => $result['transaction_id'], //微信支付订单号
                        'state' => 2, //已付款
                    );
                    //开始事务
                    DB::beginTransaction();
                    try {
                        //更新订单状态
                        if (OrderService::noticeUpdateOrder($orderInfo->id, $updateData)) {
                            //更新库存
                            GoodsSpecService::updateGoodsSpecNum($orderInfo->id);
                            //用户级别变更及销售奖励分配
                            UserService::upgradeUserLevel($orderInfo->user_id);
                            //微信通知
                            if ($orderInfo->openid) {
                                $template = config('templatemessage.orderPaySuccess');
                                $templateData = array(
                                    'first' => '您好，您的订单已支付成功',
                                    'keyword1' => '￥' . $result['cash_fee'] / 100,
                                    'keyword2' => $orderInfo->order_sn,
                                    'remark' => '如有问题请联系客服,欢迎再次光临！',
                                );
                                WechatNoticeService::sendTemplateMessage($orderInfo->user_id, $orderInfo->openid, $orderSn, $template['template_id'], $templateData);
                            }
                            //写入支付记录
                            $payLogData = array(
                                'user_id' => $orderInfo->user_id,
                                'openid' => $orderInfo->openid,
                                'pay_type' => 1,
                                'gain' => $result['cash_fee'],
                                'expense' => $result['cash_fee'],
                                'balance' => $orderInfo->balance,
                                'order_id' => $orderInfo->id,
                            );
                            PayLogsService::store($payLogData);

                            DB::commit();
                            return true;
                        } else {
                            DB::rollback();
                            return $fail('更新失败');
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return $fail('更新失败');
                    }
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }
            return true;
        });
        return $response;
    }

    /**
     * 模板消息通知
     */
    public function templateMessageNotice()
    {
        $noticeId = intval(request('noticeId', 0));
        if ($noticeId) {
            return WechatNoticeService::findById($noticeId)->update(array('is_received' => 1));
        }
    }

    /**
     * 退款通知
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function refundNotice()
    {
        $app = EasyWeChat::payment();
        $response = $app->handleRefundedNotify(function ($message, $reqInfo, $fail) {
            //保存微信通知数据
            WechatNotifyService::store($reqInfo);
            // 其中 $message['req_info'] 获取到的是加密信息
            // $reqInfo 为 message['req_info'] 解密后的信息
            if ($message['return_code'] === 'SUCCESS' && $reqInfo['refund_status'] === 'SUCCESS') {
                //更新退款
                return OrderRefundService::noticeUpdate($reqInfo);
            }

            //退款失败
            OrderRefundService::refundFailure($reqInfo);
            return $fail('退款失败');
        });
        return $response;
    }

    /**
     * 消息回复
     * @return [type] [description]
     */
    public function response()
    {
        $app = EasyWeChat::officialAccount();
        $app->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'event':
                    if ($message['Event'] == 'subscribe') {
                        //扫码事件, 创建用户建立上下级
                        if (isset($message['EventKey'])) {
                            $parentId = $message['EventKey'];
                            $openid = $message['FromUserName'];
                            //查询用户是否存在
                            $userInfo = UserService::findByOpenid($openid);
                            if(empty($userInfo)){
                                UserService::saveOrUpdate($openid, ['referee_id'=>$parentId]);
                            }
                        }
                        return '欢迎来到植得艾'
                    }
                    return '';
                    break;
                case 'text':
                    return '';
                    break;
                case 'image':
                    return '';
                    break;
                case 'voice':
                    return '';
                    break;
                case 'video':
                    return '';
                    break;
                case 'location':
                    return '';
                    break;
                case 'link':
                    return '';
                    break;
                case 'file':
                    return '';
                default:
                    return '';
                    break;
            }
        });
        return $app->server->serve();
    }


    /**
     * 获取永久素材列表
     * @return [type] [description]
     */
    public function getMaterialList()
    {
        $app = EasyWeChat::officialAccount();
        $res = $app->material->list('news', 0, 10);
        dd($res);
    }

    /**
     * 创建菜单
     * @return [type] [description]
     */
    public function createMenu()
    {
        $app = EasyWeChat::officialAccount();
        $buttons = [
            [
                "type" => "view",
                "name" => "植·商城",
                "url"  => "http://zda.youwangtong.com"
            ],
            [
                "type" => "view",
                "name" => "得·官网",
                "url"  => "http://zda.youwangtong.com"
            ],
            [
                "name"       => "艾·中心",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "植得艾·简介",
                        "url"  => "http://mp.weixin.qq.com/s?__biz=MzI4MjY2MTEzMw==&mid=100000007&idx=1&sn=c6208da57c3c6f30a0e3013958355306&chksm=6b97d9655ce0507354acb673d069da9db54956da41342f1f439f58bb3741fd3dd52e0c629023&scene=18#wechat_redirect"
                    ],
                    [
                        "type" => "view",
                        "name" => "公司介绍",
                        "url"  => "http://mp.weixin.qq.com/s?__biz=MzI4MjY2MTEzMw==&mid=100000019&idx=1&sn=95bfbd084f7be588b6be798b521d0092&chksm=6b97d9715ce050672a5eb3eeb9d312e4ea78016bb9d66ce350f61e8c6fdd401529e52474ded5&scene=18#wechat_redirect"
                    ],
                    [
                        "type" => "view",
                        "name" => "模式解析",
                        "url" => "http://mp.weixin.qq.com/s?__biz=MzI4MjY2MTEzMw==&mid=100000021&idx=1&sn=8fdbc2bb2d346fd189c4d436eeb6bb70&chksm=6b97d9775ce050613f967e97b021f8115bb201ba3a490b099c8cbcf111acf691c70fecaaa24d&scene=18#wechat_redirect"
                    ],
                    [
                        "type" => "media_id",
                        "name" => "市场前景",
                        "media_id" => "8OX3D9Djktvimem6-BdSjpiLGjMb1gXfotAbg5QTBSA"
                    ],
                    [
                        "type" => "view",
                        "name" => "产品分析",
                        "url" => "http://mp.weixin.qq.com/s?__biz=MzI4MjY2MTEzMw==&mid=100000007&idx=1&sn=c6208da57c3c6f30a0e3013958355306&chksm=6b97d9655ce0507354acb673d069da9db54956da41342f1f439f58bb3741fd3dd52e0c629023&scene=18#wechat_redirect"
                    ],
                ],
            ],
        ];
        $app->menu->create($buttons);
    }


    public function shareQrCode()
    {
        $userId = intval(request('id', 0));
        $userInfo = UserService::findById($userId);
        if (!empty($userInfo)) {
            $data['imgSrc'] = env('APP_URL').'/shareImg/' . $userId . '.jpg';
            return view('users.shareQrCode', $data);
        }
        abort(500, '不存在在分享链接');
    }
}
