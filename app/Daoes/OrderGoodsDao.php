<?php

namespace App\Daoes;

use App\Daoes\BaseDao;
use App\Models\OrderGoods;

class OrderGoodsDao extends BaseDao
{
    public static function findById($id)
    {
        return OrderGoods::find($id);
    }

    /**
     * 根据 订单id查询订单商品列表
     * @param int $id
     *
     * @return App\Models\User\Order
     */
    public static function findByOrderId($id)
    {
        return OrderGoods::join('order', 'order.id', '=', 'order_goods.order_id')
            ->select('order_goods.*')
            ->where('order_goods.order_id', $id)
            ->where('order.user_id', session('user')->id)
            ->get();
    }

    /**
     * 查询订单商品关联活动
     * @param int $order_id
     * @return object
     */
    public static function checkOrderPromotion($order_id)
    {
        return OrderGoods::leftJoin('goods_spec as s', 'order_goods.spec_id', '=', 's.id')
            ->leftJoin('promotions as p', 'order_goods.promotions_id', '=', 'p.id')
            ->where('order_goods.order_id', $order_id)
            ->whereNull('p.deleted_at')
            ->select('order_goods.id as order_goods_id', 'order_goods.goods_name', 'order_goods.num', 's.state as spec_state', 's.number as stock', 'p.*')
            ->get();
    }
}
