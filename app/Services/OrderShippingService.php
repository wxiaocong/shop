<?php

namespace App\Services;

use App\Daoes\OrderShippingDao;

class OrderShippingService
{
    /**
     * 根据 id查询
     * @param int $id
     *
     * @return App\Models\OrderShipping
     */
    public static function findById($id)
    {
        return OrderShippingDao::find($id);
    }

    /**
     * 根据 订单id查询发货记录
     * @param int $id
     *
     * @return App\Models\OrderShipping
     */
    public static function findByOrderId($id)
    {
        return OrderShippingDao::findByOrderId($id);
    }
}
