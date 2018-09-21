<?php

namespace App\Http\Controllers;

use App\Services\OrderRefundService;
use App\Services\OrderService;
use App\Services\PayLogsService;
use App\Services\PromotionService;
use App\Services\GoodsSpecService;
use App\Services\Users\UserService;
use App\Services\WechatNoticeService;
use App\Services\WechatNotifyService;
use Illuminate\Support\Facades\DB;
use EasyWeChat;
use Session;

class WeChatController extends Controller
{
    //网页授权
    public function oauthCallback()
    {
        $app   = EasyWeChat::officialAccount();
        $oauth = $app->oauth;
        // 获取 OAuth 授权结果用户信息
        $user   = $oauth->user()->toArray();
        $openid = $user['id'];
        //获取用户头像等信息
        $user = $app->user->get($openid);
        if (!empty($user)) {
            //保存微信通知数据
            WechatNotifyService::store($user);
            $userInfo = UserService::findByOpenid($openid);
            $wechatUserData = array(
                'openid'         => $openid,
                'subscribe'      => $user['subscribe'],
                'subscribe_time' => $user['subscribe_time'],
                'nickname'       => $user['nickname'],
                'headimgurl'     => $user['headimgurl'],
                'city'           => $user['city'],
                'province'       => $user['province'],
                'country'        => $user['country'],
                'sex'            => $user['sex']
            );
            UserService::saveOrUpdate($wechatUserData, $userInfo->id ?? 0);
            session(array('user' => UserService::findByOpenid($openid)));
        }
        $targetUrl = empty(session('target_url')) ? config('app.url') : session('target_url');
        session::forget('target_url');
        return redirect($targetUrl);
    }

    //微信支付通知
    public function payNotice()
    {
        $app      = EasyWeChat::payment();
        $response = $app->handlePaidNotify(function ($message, $fail) {
            //保存微信通知数据
            WechatNotifyService::store($message);
            //微信查询订单状态
            $searchApp = EasyWeChat::payment();
            $result    = $searchApp->order->queryByOutTradeNumber($message['out_trade_no']);
            if ($result['return_code'] === 'SUCCESS') {
                if ($result['trade_state'] === 'SUCCESS') {
                    //查询订单
                    $orderSn   = substr($message['out_trade_no'], 0, 22);
                    $orderInfo = OrderService::findAddGoodByOrderSn($orderSn);
                    //未找到订单或订单不是未付款状态，退款
                    if (empty($orderInfo) || $orderInfo->state != 1) {
                        OrderRefundService::wechatRefund($message['out_trade_no'], $message['out_trade_no'].time(), $message['total_fee'], $result['cash_fee']);
                        return true;
                    }
                    $pay_time = date('Y-m-d H:i:s', strtotime($result['time_end']));
                    $updateData = array(
                        'real_pay'       => $result['cash_fee'],                                 //实付款
                        'pay_time'       => $pay_time, //付款时间
                        'transaction_id' => $result['transaction_id'],                           //微信支付订单号
                        'state'          => 2,                                                   //已付款
                    );
                    //开始事务
                    DB::beginTransaction();
                    try {
                        //更新订单状态
                        if (OrderService::noticeUpdateOrder($orderInfo->id, $updateData)) {
                            //更新库存
                            GoodsSpecService::updateGoodsSpecNum($orderInfo->id);
                            //更新参加活动商品已售数量
                            PromotionService::updateSelledNum($orderInfo->id);
                            //微信通知
                            if ($orderInfo->openid) {
                                $template     = config('templatemessage.orderPaySuccess');
                                $templateData = array(
                                    'first' =>  '您好，您的订单已支付成功',
                                    'keyword1'=> '植得艾',
                                    'keyword2' => '￥' . $result['cash_fee']/100,
                                    'keyword3' => $pay_time,
                                    'keyword4' => $orderInfo->order_sn,
                                    'remark' => '如有问题请联系客服,欢迎再次光临！'
                                );
                                WechatNoticeService::sendTemplateMessage($orderInfo->user_id, $orderInfo->openid, $orderSn, $template['template_id'], $templateData);
                            }
                            //写入支付记录
                            $payLogData = array(
                                'user_id'  => $orderInfo->user_id,
                                'openid'   => $orderInfo->openid,
                                'pay_type' => 1,
                                'gain'     => $result['cash_fee'],
                                'expense'  => $result['cash_fee'],
                                'balance'  => $orderInfo->balance,
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
        $app      = EasyWeChat::payment();
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

}
