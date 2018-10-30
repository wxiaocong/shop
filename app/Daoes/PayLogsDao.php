<?php

namespace App\Daoes;

use App\Daoes\BaseDao;
use App\Models\PayLogs;
use App\Utils\DateUtils;
use App\Utils\Page;
use Illuminate\Support\Facades\DB;

class PayLogsDao extends BaseDao
{
    
    public static function getAll($curPage, $pageSize, $params)
    {
        $builder = PayLogs::join('users as u','pay_logs.user_id','=','u.id');
        if (array_key_exists('type', $params)) {
            $builder->whereIn('pay_logs.pay_type', array_keys($params['type']));
        }
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
            $builder->whereIn('pay_logs.pay_type', $params['pageType']);
        }
        if (array_key_exists('search', $params) && $params['search'] != '') {
            $search = $params['search'];
            $builder->where('u.nickname', 'like', "%{$search}%");
        }

        $builder->orderBy('pay_logs.id', 'desc');
        return new Page($builder->paginate($pageSize, array('pay_logs.*', 'u.nickname'), 'page', $curPage));
    }

    public static function sumPay($params)
    {
        $builder = PayLogs::join('users as u','pay_logs.user_id','=','u.id');
        if (array_key_exists('type', $params)) {
            $builder->whereIn('pay_logs.pay_type', array_keys($params['type']));
        }
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
            $builder->whereIn('pay_logs.pay_type', $params['pageType']);
        }
        if (array_key_exists('search', $params) && $params['search'] != '') {
            $search = $params['search'];
            $builder->where('u.nickname', 'like', "%{$search}%");
        }
        return $builder->select(DB::raw('SUM(IF(pay_type!=1,gain,0)) AS total_gain,SUM(expense) AS total_expense'))->first();
    }

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
     * 分页查询资金记录,此时pay_logs.order_id存的下单用户id
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
        return PayLogs::where('user_id', $user_id)->orderBy('id', 'desc')->offset($pageSize * ($curPage - 1))->limit($pageSize)->get();
    }
}
