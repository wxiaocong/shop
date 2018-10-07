<?php

namespace App\Daoes;

use App\Daoes\BaseDao;
use App\Models\PayLogs;
use App\Utils\DateUtils;

class PayLogsDao extends BaseDao
{
    /**
     * 查询
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params)
    {
        $builder = PayLogs::select();

        if (array_key_exists('userId', $params) && $params['userId'] > 0) {
            $builder->where('user_id', $params['userId']);
        }
        if (array_key_exists('payType', $params) && $params['payType'] > 0) {
            $builder->where('pay_type', $params['payType']);
        }
        if (array_key_exists('startDate', $params) && $params['startDate'] != '') {
            $builder->where('created_at', '>=', DateUtils::addDay(0, $params['startDate']));
        }
        if (array_key_exists('endDate', $params) && $params['endDate'] != '') {
            $builder->where('created_at', '<', DateUtils::addDay(1, $params['endDate']));
        }
        if (array_key_exists('orderId', $params) && $params['orderId'] > 0) {
            $builder->where('order_id', $params['orderId']);
        }
        if (array_key_exists('orderBy', $params)) {
            foreach ($params['orderBy'] as $key => $value) {
                $builder->orderBy($key, $value);
            }
        }

        return $builder->get();
    }
    
    /**
     * 分页查询资金记录
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPage($curPage, $pageSize, $params) {
        $builder = PayLogs::join('users as u','pay_logs.order_id','=','u.id')
            ->offset($pageSize * ($curPage - 1))->limit($pageSize)
            ->select('pay_logs.*', 'u.nickname');
        if (array_key_exists('userId', $params) && $params['userId'] > 0) {
            $builder->where('pay_logs.user_id', $params['userId']);
        }
        if (array_key_exists('payType', $params) && $params['payType'] > 0) {
            $builder->where('pay_logs.pay_type', $params['payType']);
        }
        if (array_key_exists('startDate', $params) && $params['startDate'] != '') {
            $builder->where('pay_logs.created_at', '>=', DateUtils::addDay(0, $params['startDate']));
        }
        if (array_key_exists('endDate', $params) && $params['endDate'] != '') {
            $builder->where('pay_logs.created_at', '<', DateUtils::addDay(1, $params['endDate']));
        }
        if (array_key_exists('pageType', $params) && !empty($params['pageType'])) {
            //页面分类-收入明细
            $builder->whereIn('pay_logs.pay_type', $params['pageType']);
        }
        if (array_key_exists('orderBy', $params)) {
            foreach ($params['orderBy'] as $key => $value) {
                $builder->orderBy('pay_logs.'.$key, $value);
            }
        }
        
        return $builder->get();
    }

        /**
     * 余额变动记录
     */
    public static function getAllByUser($user_id, $curPage, $pageSize) {
        return PayLogs::where('user_id', $user_id)->offset($pageSize * ($curPage - 1))->limit($pageSize)->get();
    }
}
