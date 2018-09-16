<?php

namespace App\Services;

use App\Daoes\OrderGoodsDao;
use App\Daoes\PromotionDao;
use App\Daoes\OrderDao;

class OrderGoodsService
{
    public static function findByOrderId($id)
    {
        return OrderGoodsDao::findByOrderId($id);
    }

    
    /**
     * 检查订单商品活动是否有效
     * @param int $order_id
     * @return object
     */
    public static function checkOrderPromotion($order_id, $new_order = false)
    {
        $result = OrderGoodsDao::checkOrderPromotion($order_id);
        $currentDate = strtotime(date('Y-m-d'));
        $multipleLimit     = config('order.multipleLimit'); //预下单倍数
        foreach ($result as $val) {
            if ($val->spec_state > 0) {
                return array(
                    'code'      => 500,
                    'messages'  => $val->goods_name.'商品已下架',
                    'data'  =>  ''
                );
            }
            //检查库存
            if ($val->num > $val->stock) {
                return array(
                    'code'      => 500,
                    'messages'  => $val->goods_name.'库存不足,请重新下单',
                    'data'  =>  ''
                );
            }
            if ($val->id > 0) { //有活动，检查活动状态
                if($val->type != config('statuses.promotion.type.speed.code') || $val->award_type != config('statuses.promotion.awardType.speed.code') || 
                        $val->is_close != 0 || ! ($currentDate >= strtotime($val->start_time) && $currentDate <= strtotime($val->end_time)) || ! PromotionDao::isGoingTime()) {
                    return array(
                        'code'      => 500,
                        'messages'  => $val->goods_name.'活动已结束,请重新下单',
                        'data'  =>  ''
                    );
                }
                $award_value       = json_decode($val->award_value);
                //活动可购数量
                $result->promotion_number = min($award_value->onceNum, ($award_value->totalNum - $val->selled_num));
                if ($new_order) {
                    $result->promotion_number = min($result->promotion_number,($multipleLimit * $award_value->totalNum - $val->order_num));
                }
                if ($result->promotion_number < $val['num']) {
                    //活动商品库存不足，取消订单
                    $orderInfo = OrderDao::findById($order_id);
                    OrderDao::cancle($orderInfo->order_sn);
                    return array(
                        'code'      => 500,
                        'messages'  => $val->goods_name.'活动商品库存不足,请重新下单',
                        'data'  =>  ''
                    );
                }
            }
        }
        return array(
            'code'      => 200,
            'messages'  => 'pass',
            'data'      => $result
        );
    }
}
