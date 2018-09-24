<?php

namespace App\Services;

use App\Daoes\OrderGoodsDao;

class OrderGoodsService {
	public static function findByOrderId($id) {
		return OrderGoodsDao::findByOrderId($id);
	}

	/**
	 * 检查订单商品活动是否有效
	 * @param int $order_id
	 * @return object
	 */
	public static function checkOrderPromotion($order_id, $new_order = false) {
		$result = OrderGoodsDao::checkOrderPromotion($order_id);
		$currentDate = strtotime(date('Y-m-d'));
		foreach ($result as $val) {
			if ($val->spec_state > 0) {
				return array(
					'code' => 500,
					'messages' => $val->goods_name . '商品已下架',
					'data' => '',
				);
			}
			//检查库存
			if ($val->num > $val->stock) {
				return array(
					'code' => 500,
					'messages' => $val->goods_name . '库存不足,请重新下单',
					'data' => '',
				);
			}
		}
		return array(
			'code' => 200,
			'messages' => 'pass',
			'data' => $result,
		);
	}
}
