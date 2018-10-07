<?php

namespace App\Http\Controllers\Users;
use App\Http\Requests\Users\AgentRequest;
use App\Services\AgentService;
use App\Services\AgentTypeService;
use App\Services\OrderService;
use App\Services\Users\UserService;
use App\Services\AreasService;
use EasyWeChat;

use App\Http\Controllers\Controller;
class AgentController extends Controller {
    public function index() {
        $data['agent'] = AgentTypeService::getAll();
        $agentInfo = AgentService::findByUserId(session('user')->id);
        if (!empty($agentInfo)) {
            abort(404, '您已经申请了代理商，请务重复申请');
        }
        return view('users.agent', $data);
    }

    public function show() {
        $order_sn = request('agent');
        $data['agent'] = AgentService::getByOrderSn($order_sn)->where('user_id', session('user')->id)->whereIn('state', array(2,3))->orderBy('id', 'desc')->first();
        if (empty($data)) {
            abort(404, '您还不是代理商，请先申请');
        }
        $data['agentType'] = AgentTypeService::getAll();
        $data['agent']->region = AreasService::convertAreaIdToName([$data['agent']->province, $data['agent']->city, $data['agent']->area]);
        $data['agentState'] = config('statuses.agentState');
        return view('users.agentDetail', $data);
    }

    //创建订单
    public function store(AgentRequest $request) {
        $res = AgentService::store($request);
        return response()->json($res);
    }

    //收银页面
    public function cashPay() {
        $orderSn = request('ordersn');
        $data['orderInfo'] = AgentService::findByOrderSn($orderSn);
        if (empty($data['orderInfo'])) {
            abort(404, '未找到该订单');
        }
        $data['userInfo'] = UserService::findById(session('user')->id);
        return view('users.agentPay', $data);
    }

    //已创建订单，未支付
    public function prepay() {
        $orderSn = request('ordersn');
        $payType = intval(request('payType'));
        if (! in_array($payType, config('system.payType'))) {
            $res['code'] = 500;
            $res['messages'] = '未开通的支付类型';
            return response()->json($res);
        }
        $res['data'] = AgentService::findByOrderSn($orderSn);
        if (!empty($res['data']) && $res['data']->state != 1) {
            $res['code'] = 500;
            $res['messages'] = '订单状态异常，请联系客服';
            return response()->json($res);
        }
        $res['url'] = config('app.url') . '/agent/orderComplate/' . $orderSn;
        if ($payType == 3) {
            //余额支付
            $res = AgentService::balancePay($res['data']);
            return response()->json($res);
        } else {
            //微信支付
            return response()->json(self::wechatUnity($res));
        }
    }

    //微信统一下单
    private function wechatUnity($res) {
        $app = EasyWeChat::payment();
        $result = $app->order->unify([
            'body' => '订单:' . $res['data']['order_sn'],
            'out_trade_no' => $res['data']['order_sn'],
            'total_fee' => $res['data']['payment'], //单位  分
            'trade_type' => isWeixin() ? 'JSAPI' : 'MWEB',
            'openid' => session('user')->openid,
            'notify_url' => env('WECHAT_AGENT_PAYMENT_NOTIFY_URL'),
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
        $orderInfo = AgentService::findByOrderSn($orderSn);
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
                $result = $searchApp->order->queryByOutTradeNumber($orderInfo->order_sn);
                if ($result['return_code'] !== 'SUCCESS' || $result['trade_state'] !== 'SUCCESS') {
                    $data = array(
                        'code' => 500,
                        'messages' => '未付款成功',
                        'data' => $orderInfo,
                    );
                }
            }
            $data = array(
                'code' => 200,
                'messages' => '已付款',
                'data' => $orderInfo,
            );
        }
        return view('users.agentComplate', $data);
    }
}
