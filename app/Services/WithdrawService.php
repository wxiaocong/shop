<?php

namespace App\Services;
use App\Daoes\WithdrawDao;
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
        $withDraw->pay_type = $request['pay_type'];
        $withDraw->bank_code = $request['bank_code'];
        $withDraw->enc_bank_no = $request['enc_bank_no'];
        $withDraw->pay_time = date('Y-m-d H:i:s');
        $withDraw->desc  = '余额提现';

        return $withDraw->save();
    }

    /**
     * 提现记录
     */
    public static function getAllByUser($user_id, $curPage, $pageSize) {
        return WithdrawDao::getAllByUser($user_id, $curPage, $pageSize);
    }

    /**
     * 分页查询提现记录
     * @param  int $curPage
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPage($curPage, $pageSize, $params = array()) {
        return WithdrawDao::findByPage($curPage, $pageSize, $params);
    }

    public static function findById($id) {
        return WithdrawDao::findById($id);
    }

    public static function getDetail($id) {
        return WithdrawDao::getDetail($id);
    }

    public static function audit($id) {
        return WithdrawDao::audit($id);
    }

    public static function cancle($id) {
        return WithdrawDao::cancle($id);
    }
}
