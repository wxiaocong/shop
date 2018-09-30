<?php

namespace App\Services;

use App\Daoes\OrderDao;
use App\Daoes\OrderRefundDao;
use App\Models\OrderRefund;
use App\Services\OrderService;
use App\Services\PayLogsService;
use App\Services\WechatNoticeService;
use EasyWeChat;

class OrderRefundService {
    public static function refundFailure($request) {
        $refundSn = $request['out_refund_no'] ?? '';
        if ($refundSn) {
            //更新退款表
            $orderRefund = OrderRefundDao::findByRefundNo($request['out_refund_no']);
            $orderRefund->state = 2;
            $orderRefund = OrderRefundDao::save($orderRefund, null);

            if ($orderRefund) {
                //更新订单及订单商品
                return OrderService::refundmentFailure($orderRefund);
            }
        }
        return false;
    }

    /**
     * 保存更新order_refund
     * @param unknown $request
     * @param unknown $id
     */
    public static function noticeUpdate($request) {
        $refundSn = $request['out_refund_no'] ?? '';
        if ($refundSn) {
            //更新退款表
            $orderRefund = OrderRefundDao::findByRefundNo($request['out_refund_no']);
            if ($orderRefund) {
                $orderRefund->refund_id = $request['refund_id'];
                $orderRefund->real_refund_fee = $request['refund_fee'];
                $orderRefund->success_time = $request['success_time'];
                $orderRefund->state = 1;
                $orderRefund = OrderRefundDao::save($orderRefund, null);

                if ($orderRefund) {
                    $orderSn = substr($request['out_trade_no'], 0, 22);

                    $orderInfo = OrderDao::findByOrderSn($orderSn, true);
                    //微信通知
                    $template = config('templatemessage.orderRefundSuccess');
                    $templateData = array(
                        'first' => '您好，您的订单已退款成功',
                        'keyword1' => $orderSn,
                        'keyword2' => '￥' . $request['refund_fee'] / 100,
                        'remark' => '如有问题请联系客服,欢迎再次光临！',
                    );
                    $url = config('app.url').'/order/detail/'.$orderSn;
                    WechatNoticeService::sendTemplateMessage($orderInfo->user_id, $orderInfo->openid, $url, $template['template_id'], $templateData);
                    //写入支付记录
                    $payLogData = array(
                        'user_id' => $orderInfo->user_id,
                        'openid' => $orderInfo->openid,
                        'pay_type' => 4,
                        'gain' => $request['refund_fee'],
                        'expense' => 0,
                        'balance' => $orderInfo->user->balance,
                        'order_id' => $orderInfo->id,
                    );
                    PayLogsService::store($payLogData);
                    return true;
                }
            } else {
                //没有退款单号的记录异常退款
                $orderRefund = new OrderRefund();
                $orderRefund->order_sn = $request['out_trade_no'];
                $orderRefund->out_refund_no = $request['out_refund_no'];
                $orderRefund->total_fee = $request['total_fee'];
                $orderRefund->refund_fee = $request['refund_fee'];
                $orderRefund->real_refund_fee = $request['refund_fee'];
                $orderRefund->refund_desc = '订单异常退款';
                $orderRefund->state = 4;
                OrderRefundDao::save($orderRefund);
            }
        }
        return false;
    }

    /**
     * 根据商户订单号申请退款
     * 1、交易时间超过一年的订单无法提交退款
     * 2、微信支付退款支持单笔交易分多次退款，多次退款需要提交原支付订单的商户订单号和设置不同的退款单号。申请退款总金额不能超过订单金额。 一笔退款失败后重新提交，请不要更换退款单号，请使用原商户退款单号
     * 3、请求频率限制：150qps，即每秒钟正常的申请退款请求次数不超过150次，错误或无效请求频率限制：6qps，即每秒钟异常或错误的退款申请请求不超过6次
     * 4、每个支付订单的部分退款次数不能超过50次
     *
     * 参数分别为：商户订单号、商户退款单号、订单金额、退款金额、其他参数
     */
    public static function wechatRefund($orderSn, $refundSn, $totalFee, $refundFee, $config = array()) {
        $app = EasyWeChat::payment();
        $config['notify_url'] = env('WECHAT_PAYMENT_REFUND_NOTIFY_URL');
        return $app->refund->byOutTradeNumber($orderSn, $refundSn, $totalFee, $refundFee, $config);
    }

    /**
     * 根据微信订单号申请退款
     * 参数分别为：微信订单号、商户退款单号、订单金额、退款金额、其他参数
     */
    public static function wechatRefundByTransactionId($transactionId, $refundSn, $totalFee, $refundFee, $config = array()) {
        $app = EasyWeChat::payment();
        $config['notify_url'] = env('WECHAT_PAYMENT_REFUND_NOTIFY_URL');
        return $app->refund->byTransactionId($transactionId, $refundSn, $totalFee, $refundFee, $config);
    }

    /**
     * 退款查询
     * @param string $refundSn
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public static function searchRefundResult($refundSn, $type = 0) {
        $app = EasyWeChat::payment();
        switch ($type) {
        case 1: //微信订单号
            return $app->refund->queryByTransactionId($refundSn);
            break;
        case 2: //商户退款单号
            return $app->refund->queryByOutRefundNumber($refundSn);
            break;
        case 3: //微信退款单号
            return $app->refund->queryByRefundId($refundSn);
            break;
        default: //商户订单号
            return $app->refund->queryByOutTradeNumber($refundSn);
        }
    }

    /**
     * 查询
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params = array()) {
        return OrderRefundDao::findByParams($params);
    }
}
