<?php

namespace App\Http\Controllers\Admins\Tool;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Services\OrderRefundService;
use App\Daoes\OrderRefund;
use App\Model\OrderRefund;

class WechatController extends Controller
{
    public function index()
    {
        return view('admins.tool.wechatOrder');
    }

    public function getOrderInfo()
    {
        $transaction_id = request('transaction_id');
        if(OrderService::findByTransactionId($transaction_id)) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('微信订单号不存在'),
                'url'      => '',
            ));
        } else {
            return response()->json(array(
                'code'     => 200,
                'messages' => array(json_encode(OrderService::searchOrderByTransactionId($transaction_id))),
                'url'      => '',
            ));
        }
    }

    public function refund()
    {
        return view('admins.tool.wechatRefund');
    }

    public function wechatRefunc()
    {
        $transaction_id = request('transaction_id');
        $money = request('refund_money');
        $orderInfo = OrderService::findByTransactionId($transaction_id);
        if(empty($orderInfo)) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('微信订单号不存在'),
                'url'      => '',
            ));
        } elseif ($orderInfo->real_pay < $money) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('退款金额不能大于付款金额'),
                'url'      => '',
            ));
        } else {
            $refundSn = $orderInfo->order_sn.time();
            $orderRefund                  = new OrderRefund();
            $orderRefund->user_id         = $orderInfo->user_id;
            $orderRefund->order_sn        = $orderInfo->order_sn;
            $orderRefund->out_refund_no   = $refundSn;
            $orderRefund->total_fee       = $orderInfo->real_pay;
            $orderRefund->refund_fee      = $money;
            $orderRefund->real_refund_fee = $money;
            $orderRefund->refund_desc     = '后台手动退款';
            $orderRefund->opera_id        = session('adminUser')->id;
            $orderRefund->state           = 0;

            OrderRefundService::wechatRefundByTransactionId($transaction_id, $refundSn, $orderInfo->real_pay, $money);
            //查询退款
            $res = OrderRefundService::searchRefundResult($transaction_id, 1);
            if ($res['return_code'] === 'SUCCESS' && $res['result_code'] === 'SUCCESS') {
                $orderRefund->state = 1;
            }
            OrderRefundDao::save($orderRefund, session('adminUser')->id);
            return response()->json(array(
                'code'     => 200,
                'messages' => array(json_encode($res)),
                'url'      => '',
            ));
        }
    }
}
