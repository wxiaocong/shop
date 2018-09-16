<?php

namespace App\Daoes;

use App\Daoes\BaseDao;
use App\Models\OrderShipping;

class OrderShippingDao extends BaseDao
{
    /**
     * 根据 id查询
     * @param int $id
     *
     * @return App\Models\OrderShipping
     */
    public static function findById($id)
    {
        return OrderShipping::find($id);
    }

    /**
     * 根据 订单id查询发货记录
     * @param int $id
     *
     * @return App\Models\OrderShipping
     */
    public static function findByOrderId($id)
    {
        return OrderShipping::select('order_shipping.*')
            ->join('order_goods', function ($join) use ($id) {
                $join->on('order_shipping.order_goods_id', '=', 'order_goods.id')
                    ->where('order_goods.order_id', '=', $id);
            })->orderBy('order_shipping.express_time', 'desc')->orderBy('order_shipping.express_no', 'desc')->get();
    }
}
