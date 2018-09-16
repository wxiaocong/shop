<?php

namespace App\Daoes;

use App\Daoes\BaseDao;
use App\Models\OrderRefund;

class OrderRefundDao extends BaseDao
{
    /**
     * 查询
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params)
    {
        $builder = OrderRefund::with('orderGoodsRefunds', 'orderGoodsRefunds.goodsSpec');

        if (array_key_exists('orderSn', $params) && $params['orderSn'] != '') {
            $builder->where('order_sn', $params['orderSn']);
        }
        if (array_key_exists('orderBy', $params)) {
            foreach ($params['orderBy'] as $key => $value) {
                $builder->orderBy($key, $value);
            }
        }

        return $builder->get();
    }

    public static function findByRefundNo($refundNo, $state = 0)
    {
        $builder = OrderRefund::where('out_refund_no', $refundNo)->where('state', $state);

        return $builder != null ? $builder->first() : null;
    }

    //查询已退金额
    public static function searchOrderRefundTotal($orderSn)
    {
        return OrderRefund::where(array('order_sn' => $orderSn, 'state' => 1))->sum('real_refund_fee');
    }

    /**
     * 保存退款商品明细
     * @param  App\Models\OrderRefund $orderRefund
     * @param  array(App\Models\OrderGoodsRefund) $orderGoodsRefunds
     *
     * @return array
     */
    public static function saveManyGoodsRefunds($orderRefund, $orderGoodsRefunds)
    {
        return $orderRefund->orderGoodsRefunds()->saveMany($orderGoodsRefunds);
    }
}
