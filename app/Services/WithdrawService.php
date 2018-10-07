<?php

namespace App\Services;

use App\Models\Withdraw;

class WithdrawService
{
    public static function store($request)
    {
        $withDraw           = new Withdraw();
        $withDraw->order_sn  = $request['order_sn'];
        $withDraw->user_id   = session('user')->id;
        $withDraw->openid = session('user')->openid;
        $withDraw->realname     = session('user')->realname;
        $withDraw->amount  = $request['amount'];
        $withDraw->desc  = '余额提现';

        return $withDraw->save();
    }
}
