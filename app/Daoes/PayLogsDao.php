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

        if (array_key_exists('payType', $params) && $params['payType'] != '') {
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
}
