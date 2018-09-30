<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\OrderRequest;
use App\Services\OrderGoodsService;
use App\Services\OrderRefundService;
use App\Services\OrderService;
use App\Services\OrderShippingService;
use App\Utils\Page;
use EasyWeChat;

class OrderController extends Controller {
    public function index() {
        $data['pageSize'] = Page::PAGESIZE;
        $data['showState'] = config('order.show_state');
        $data['orderType'] = intval(request('order_type'));
        $data['orderType'] = array_key_exists($data['orderType'], $data['showState']) ? $data['orderType'] : 0;
        return view('users.order', $data);
    }

    public function detail() {
        $orderSn = request('ordersn');
        $data['orderInfo'] = OrderService::findByOrderSn($orderSn);
        if (empty($data['orderInfo'])) {
            abort(404, '未找到改订单');
        }
        $data['orderState'] = config('order.order_state');
        $data['orderGoodsInfo'] = OrderGoodsService::findByOrderId($data['orderInfo']->id);

        $orderShipingArr = array();
        $orderShippingList = OrderShippingService::findByOrderId($data['orderInfo']->id);
        foreach ($orderShippingList as $orderShipping) {
            if (array_key_exists($orderShipping->express_no, $orderShipingArr)) {
                $orderShipingArr[$orderShipping->express_no]['text'] .= '; ' . $orderShipping->orderGood->goods_name;
            } else {
                $orderShipingArr[$orderShipping->express_no] = array(
                    'express_name' => $orderShipping->express_name,
                    'express_no' => $orderShipping->express_no,
                    'express_time' => $orderShipping->express_time,
                    'text' => '发货商品：' . $orderShipping->orderGood->goods_name,
                );
            }
        }
        $data['orderShipingList'] = $orderShipingArr;

        return view('users.orderDetail', $data);
    }

    //抢购创建订单:单商品
    public function store(OrderRequest $request) {
        $res = OrderService::store($request);
        return response()->json($res);
    }

    //收银页面
    public function cashPay() {
        $orderSn = request('ordersn');
        $data['orderInfo'] = OrderService::findByOrderSn($orderSn);
        if (empty($data['orderInfo'])) {
            abort(404, '未找到改订单');
        }
        //检查商品、活动、库存
        $check = OrderGoodsService::checkOrderPromotion($data['orderInfo']->id);
        if ($check['code'] == 500) {
            abort(500, $check['messages']);
        }

        return view('users.cashPay', $data);
    }

    //已创建订单，未支付
    public function prepay() {
        $orderSn = request('ordersn');
        $res['data'] = OrderService::findByOrderSn($orderSn);
        if (!empty($res['data']) && $res['data']->state != 1) {
            $res['code'] = 500;
            $res['messages'] = '订单状态异常，请联系客服';
            return response()->json($res);
        }
        //订单超时取消订单
        if ($res['data']->state == 1 && (strtotime($res['data']['created_at']) + config('system.orderOvertime') < time())) {
            OrderService::cancle($res['data']->id);
            $res['code'] = 500;
            $res['messages'] = '订单超时已取消,请重新下单';
            return response()->json($res);
        }
        //检查商品、活动、库存
        $check = OrderGoodsService::checkOrderPromotion($res['data']->id);
        if ($check['code'] == 500) {
            return response()->json($check);
        }
        $res['url'] = config('app.url') . '/order/orderComplate/' . $orderSn;

        return response()->json(self::wechatUnity($res));
    }

    //微信统一下单
    private function wechatUnity($res) {
        //每次下单用新商户订单号
        $out_trade_no = createOutTradeNo($res['data']['order_sn']);
        if (!OrderService::getByOrderSn($res['data']['order_sn'])->update(['out_trade_no' => $out_trade_no])) {
            $res['code'] = 500;
            $res['messages'] = '生成商户订单号异常';
            return response()->json($res);
        }
        $app = EasyWeChat::payment();
        $result = $app->order->unify([
            'body' => '订单:' . $res['data']['order_sn'],
            'out_trade_no' => $out_trade_no,
            'total_fee' => $res['data']['payment'], //单位  分
            'trade_type' => isWeixin() ? 'JSAPI' : 'MWEB',
            'openid' => session('user')->openid,
            'notify_url' => env('WECHAT_PAYMENT_NOTIFY_URL'),
        ]);
        if ($result['return_code'] === 'SUCCESS' && $result['result_code'] === 'SUCCESS') {
            if (isWeixin()) {
                $prepayId = $result['prepay_id'];
                $jssdk = $app->jssdk;
                $res['data']['config'] = $jssdk->sdkConfig($prepayId);
            } else {
                $res['h5Url'] = $result['mweb_url'];
            }
            $res['code'] = 200;
        } else {
            $res['code'] = 500;
            $res['messages'] = $result['err_code_des'] ?? '统一下单支付失败';
        }
        return $res;
    }

    //完成支付
    public function orderComplate() {
        //查询订单
        $orderSn = request('ordersn');
        $orderInfo = OrderService::findByOrderSn($orderSn);
        //未找到订单或订单不是未付款状态，退出
        if (empty($orderInfo)) {
            $data = array(
                'code' => 500,
                'messages' => '订单异常,请联系客服',
            );
        } else {
            if ($orderInfo->state != 2) {
                //订单状态不为已付款，微信查询订单状态
                $searchApp = EasyWeChat::payment();
                $result = $searchApp->order->queryByOutTradeNumber($orderInfo->out_trade_no);
                if ($result['return_code'] !== 'SUCCESS' || $result['trade_state'] !== 'SUCCESS') {
                    return redirect('/order/detail/' . $orderInfo->order_sn);
                }
            }
            $data = array(
                'code' => 200,
                'messages' => '已付款',
                'data' => $orderInfo,
            );
        }
        return view('users.orderComplate', $data);
    }

    //获取订单列表数据
    public function getData() {
        $param['user_id'] = session('user')->id;
        $param['order_type'] = intval(request('order_type', 0));

        $curPage = trimSpace(request('curPage', 1));
        $pageSize = trimSpace(request('pageSize', Page::PAGESIZE));

        $data['orderList'] = OrderService::findByPage($curPage, $pageSize, $param);
        $data['order_state'] = config('order.order_state');
        return view('users.orderData', $data);
    }

    /**
     * 微信服务器查询订单
     */
    public function searchOrderResult() {
        $orderSn = request('ordersn');
        if ($orderSn) {
            $res = OrderService::searchOrderResult($orderSn);
            \Log::error($res);
        }
    }

    /**
     * 微信服务器查询退款订单
     */
    public function searchOrderRefundResult() {
        $orderSn = request('ordersn');
        if ($orderSn) {
            $res = OrderRefundService::searchRefundResult($orderSn);
            \Log::error($res);
        }
    }

    /**
     * 确认收货
     */
    public function confirmReceipt() {
        $orderSn = request('order_sn');
        if ($orderSn) {
            if (OrderService::confirmReceipt($orderSn)) {
                return response()->json(
                    array(
                        'code' => 200,
                        'messages' => array('订单确认收货成功'),
                        'url' => '/order/detail/' . $orderSn,
                    )
                );
            } else {
                return response()->json(
                    array(
                        'code' => 500,
                        'messages' => array('订单异常,无法确认收货,请重试'),
                        'url' => '',
                    )
                );
            }
        } else {
            return response()->json(
                array(
                    'code' => 500,
                    'messages' => array('参数错误'),
                    'url' => '',
                )
            );
        }
    }

    /**
     * 取消订单
     */
    public function cancle() {
        $orderSn = request('order_sn');
        if ($orderSn) {
            if (OrderService::cancle($orderSn)) {
                return response()->json(
                    array(
                        'code' => 200,
                        'messages' => array('订单已取消'),
                        'url' => '/order',
                    )
                );
            } else {
                return response()->json(
                    array(
                        'code' => 500,
                        'messages' => array('订单异常,无法取消,请重试'),
                        'url' => '',
                    )
                );
            }
        } else {
            return response()->json(
                array(
                    'code' => 500,
                    'messages' => array('参数错误'),
                    'url' => '',
                )
            );
        }
    }
}
