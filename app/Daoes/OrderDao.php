<?php

namespace App\Daoes;

use App\Daoes\BaseDao;
use App\Models\Order;
use App\Utils\DateUtils;
use App\Utils\Page;
use Illuminate\Support\Facades\DB;

class OrderDao extends BaseDao {

	/**
	 * 根据id查询订单,没加用户
	 * @param int $id
	 *
	 * @return App\Models\User\Order
	 */
	public static function findById($id) {
		return Order::find($id);
	}

	/**
	 * 获取订单
	 * @param int $id
	 * @return object
	 */
	public static function getByOrderSn($orderSn) {
		return Order::where('order_sn', $orderSn);
	}

	/**
	 * 订单号查询订单
	 * @param unknown $orderSn
	 * @return object
	 */
	public static function findByOrderSn($orderSn, $isNotice = false) {
		$builder = Order::where('order_sn', $orderSn);
		if (!$isNotice) {
			$builder->where('user_id', session('user')->id);
		}
		return $builder->first();
	}

	public static function findAddGoodByOrderSn($orderSn) {
		return Order::leftJoin('users', 'order.user_id', '=', 'users.id')
			->where('order.order_sn', $orderSn)
			->groupBy('order.id')
			->select('order.*', 'users.balance')
			->first();
	}

	/**
	 *
	 * @param int $orderId
	 * @param array $orderData
	 * @return int
	 */
	public static function noticeUpdateOrder($orderId, $orderData) {
		return Order::where(array('id' => $orderId, 'state' => 1))->update($orderData);
	}

	/**
	 * @param string $orderSn
	 * @return int
	 */
	public static function confirmReceipt($orderSn) {
		return Order::where(array('order_sn' => $orderSn, 'user_id' => session('user')->id))->whereIn('state', array(3, 4))->update(array('state' => 8));
	}

	//取消订单
	public static function cancle($orderSn) {
		return Order::where(array('order_sn' => $orderSn, 'user_id' => session('user')->id, 'state' => 1))->update(array('state' => 6));
	}

	/**
	 * 分页查询订单
	 * @param  int $pageSize
	 * @param  array $params
	 *
	 * @return array
	 */
	public static function findByPage($curPage, $pageSize, $params) {
		$builder = Order::join('order_goods as og', 'order.id', '=', 'og.order_id')
			->where('order.user_id', $params['user_id'])
			->groupBy('order.id')
			->select('order.id', 'order.order_sn', 'order.payment', 'order.express_fee', 'order.state', DB::raw('SUM(og.num) as num'), 'og.goods_name', 'og.spec_values as values', DB::raw('GROUP_CONCAT(og.goods_img) as img'))
			->orderBy('order.created_at', 'desc')
			->offset($pageSize * ($curPage - 1))->limit($pageSize);
		if (!empty($params['order_type']) && array_key_exists($params['order_type'], config('order.order_state'))) {
			$builder->where('order.state', $params['order_type']);
		}
		return $builder->get();
	}

	/**
	 * 分页查询订单
	 * @param  int $curPage
	 * @param  int $pageSize
	 * @param  array $params
	 *
	 * @return array
	 */
	public static function findByPageAndParams($curPage, $pageSize, $params) {
		$builder = Order::leftJoin('users', 'order.user_id', '=', 'users.id')->select('order.*');

		if (array_key_exists('search', $params) && $params['search'] != '') {
			$builder->where(function ($query) use ($params) {
				$query->where('order.order_sn', 'like', '%' . $params['search'] . '%')
					->orWhere('order.receiver_mobile', 'like', '%' . $params['search'] . '%')
					->orWhere('order.receiver_name', 'like', '%' . $params['search'] . '%')
					->orWhere('users.mobile', 'like', '%' . $params['search'] . '%')
					->orWhere('users.nickname', 'like', '%' . $params['search'] . '%');
			});
		}
		if (array_key_exists('state', $params) && $params['state'] != '') {
			$builder->where('order.state', $params['state']);
		}
		if (array_key_exists('deliverStatus', $params) && $params['deliverStatus'] != '') {
			$builder->where('order.deliver_status', $params['deliverStatus']);
		}
		if (array_key_exists('startPayDate', $params) && $params['startPayDate'] != '') {
			$builder->where('order.pay_time', '>=', DateUtils::addDay(0, $params['startPayDate']));
		}
		if (array_key_exists('endPayDate', $params) && $params['endPayDate'] != '') {
			$builder->where('order.pay_time', '<', DateUtils::addDay(1, $params['endPayDate']));
		}
		if (array_key_exists('startDate', $params) && $params['startDate'] != '') {
			$builder->where('order.created_at', '>=', DateUtils::addDay(0, $params['startDate']));
		}
		if (array_key_exists('endDate', $params) && $params['endDate'] != '') {
			$builder->where('order.created_at', '<', DateUtils::addDay(1, $params['endDate']));
		}
		if (array_key_exists('in', $params)) {
			foreach ($params['in'] as $key => $value) {
				$builder->whereIn($key, $value);
			}
		}
		if (array_key_exists('sort', $params)) {
			foreach ($params['sort'] as $key => $value) {
				$builder->orderBy($key, $value);
			}
		} else {
			$builder->orderBy('order.created_at', 'desc');
		}

		return new Page($builder->paginate($pageSize, array('*'), 'page', $curPage));
	}

	/**
	 * 查询
	 * @param  array $params
	 *
	 * @return array
	 */
	public static function findByParams($params) {
		$builder = Order::select();
		if (array_key_exists('state', $params) && $params['state'] != '') {
			$builder->where('state', $params['state']);
		}
		if (array_key_exists('deliverStatus', $params) && $params['deliverStatus'] != '') {
			$builder->where('deliver_status', $params['deliverStatus']);
		}
		if (array_key_exists('startDate', $params) && $params['startDate'] != '') {
			$builder->where('created_at', '>=', DateUtils::addDay(0, $params['startDate']));
		}
		if (array_key_exists('endDate', $params) && $params['endDate'] != '') {
			$builder->where('created_at', '<', DateUtils::addDay(1, $params['endDate']));
		}
		if (array_key_exists('states', $params) && count($params['states']) > 0) {
			$builder->whereIn('state', $params['states']);
		}

		return $builder->get();
	}
}
