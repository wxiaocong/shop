<?php

namespace App\Services;

use App\Daoes\PayLogsDao;
use App\Models\PayLogs;

class PayLogsService
{
    public static function store($request)
    {
        $payLog           = new PayLogs();
        $payLog->user_id  = $request['user_id'];
        $payLog->openid   = $request['openid'] ?? '';
        $payLog->pay_type = $request['pay_type'] ?? 1;
        $payLog->gain     = $request['gain'];
        $payLog->expense  = $request['expense'];
        $payLog->balance  = $request['balance'];
        $payLog->order_id = $request['order_id'] ?? 0;
        $payLog->opera_id = $request['opera_id'] ?? 0;
        $payLog->remark   = $request['remark'] ?? '';

        return $payLog->save();
    }

    /**
     * 查询
     * @param  array $params
     *
     * @return array
     */
    public static function getAll($curPage, $pageSize, $params)
    {
        $page = PayLogsDao::getAll($curPage, $pageSize, $params);
        $page->total = PayLogsDao::sumPay($params);
        return $page;
    }

    /**
     * 查询
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params = array())
    {
        return PayLogsDao::findByParams($params);
    }
    
    /**
     * 分页查询
     * @param  array $params
     *
     * @return array
     */
    public static function findByPage($curPage, $pageSize, $params)
    {
        return PayLogsDao::findByPage($curPage, $pageSize, $params);
    }

    /**
     * 余额变动记录
     */
    public static function getAllByUser($user_id, $curPage, $pageSize) {
        return PayLogsDao::getAllByUser($user_id, $curPage, $pageSize);
    }
}
