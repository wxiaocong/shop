<?php

namespace App\Http\Controllers\Admins\Order;

use App\Http\Controllers\Controller;
use App\Services\WithdrawService;
use App\Services\Users\UserService;
use App\Services\PayLogsService;
use App\Utils\Page;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function index()
    {
        $params                  = array();
        $curPage                 = trimSpace(request('curPage', 1));
        $pageSize                = trimSpace(request('pageSize', Page::PAGESIZE));
        $params['search']        = trimSpace(request('search', ''));
        $params['state']         = trimSpace(request('state', ''));
        $params['startDate']     = trimSpace(request('startPayDate', ''));
        $params['endDate']       = trimSpace(request('endPayDate', ''));

        $page = WithdrawService::findByPage($curPage, $pageSize, $params);
        return view('admins.order.withdraw')
            ->with('page', $page)
            ->with('search', $params['search'])
            ->with('state', $params['state'])
            ->with('startPayDate', $params['startDate'])
            ->with('endPayDate', $params['endDate']);
    }

    public function show($id)
    {
        $withdraw = WithdrawService::getDetail($id);
        if (!$withdraw) {
            abort(400, '提现单不存在');
        }

        return view('admins.order.withdrawDetail')
            ->with('withdraw', $withdraw)
            ->with('bank_no', config('system.bank_no'));
    }

    public function audit() {
        $id = intval(request('id'), 0);
        if ($id < 1) {
            return response()->json(array(
                'code'     => 400,
                'messages' => array('参数错误'),
                'url'      => '',
            ));
        }
        $withdrawInfo = WithdrawService::findById($id);
        if (empty($withdrawInfo)) {
            return response()->json(array(
                'code'     => 400,
                'messages' => array('提现单不存在'),
                'url'      => '',
            ));
        }
        if(WithdrawService::audit($id)) {
            $userInfo = UserService::findById($withdrawInfo->user_id);
            //更新用户余额
            UserService::withdraw($withdrawInfo->amount, $withdrawInfo->user_id);
            //写入支付日志
            $payLogData = array(
                'user_id' => $withdrawInfo->user_id,
                'openid' => $withdrawInfo->openid,
                'pay_type' => config('statuses.payLog.payType.withdraw.code'),
                'gain' => 0,
                'expense' => $withdrawInfo->amount,
                'balance' => $userInfo->balance + $withdrawInfo->amount*100,
                'order_id' => $withdrawInfo->id,
            );
            PayLogsService::store($payLogData);
            return response()->json(array(
                'code'     => 200,
                'messages' => array('提现成功'),
                'url'      => '',
            ));
        } else {
            return response()->json(array(
                'code'     => 301,
                'messages' => array('没有改变'),
                'url'      => '',
            ));
        }
    }

    public function cancel($id)
    {
        $id = intval(request('id'), 0);
        if ($id < 1) {
            return response()->json(array(
                'code'     => 400,
                'messages' => array('参数错误'),
                'url'      => '',
            ));
        }
        $withdrawInfo = WithdrawService::findById($id);
        if (empty($withdrawInfo)) {
            return response()->json(array(
                'code'     => 400,
                'messages' => array('提现单不存在'),
                'url'      => '',
            ));
        }
        if(WithdrawService::cancle($id)) {
            //解除锁定
            UserService::unLockBalance($withdrawInfo->amount, $withdrawInfo->user_id);
            return response()->json(array(
                'code'     => 200,
                'messages' => array('取消提现成功'),
                'url'      => '',
            ));
        } else {
            return response()->json(array(
                'code'     => 301,
                'messages' => array('没有改变'),
                'url'      => '',
            ));
        }
    }
}
