<?php

namespace App\Services;

use App\Daoes\GoodsSpecDao;
use App\Daoes\OrderDao;
use App\Daoes\OrderRefundDao;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\OrderGoodsRefund;
use App\Models\OrderRefund;
use App\Models\OrderShipping;
use App\Services\Users\UserService;
use App\Services\GoodsSpecService;
use App\Services\PayLogsService;
use App\Services\WechatNoticeService;
use App\Services\Users\ExpressAddressService;
use EasyWeChat;
use Illuminate\Support\Facades\DB;

class OrderService {

	//创建订单:单商品
	public static function store($request) {
		//收货地址
		$expressInfo = ExpressAddressService::findById($request['express_id']);
		if (empty($expressInfo)) {
			return array(
				'code' => 500,
				'messages' => array('未找到收货地址'),
				'url' => '',
			);
		}
		//开始事务
		DB::beginTransaction();
		//商品信息
		$goodsInfo = GoodsSpecDao::findSpecGoodsById($request['spec_id']);
		if (empty($goodsInfo)) {
			return array(
				'code' => 500,
				'messages' => array('商品已下架'),
				'url' => '',
			);
		}
		if ($request['num'] > $goodsInfo->number) {
			//检查库存
			return array(
				'code' => 500,
				'messages' => array('库存不足'),
				'url' => '',
			);
		}
		try {
			$order = new Order();
			$order->user_id = session('user')->id;
			$order->openid = session('user')->openid; //session('openid');
			$order->order_sn = createOrderSn();
			$order->payment = $goodsInfo->sell_price * $request['num'];
			$order->express_id = $expressInfo->id;
			$order->province = $expressInfo->province;
			$order->city = $expressInfo->city;
			$order->area = $expressInfo->area;
			$order->receiver_name = $expressInfo->to_user_name;
			$order->receiver_mobile = $expressInfo->mobile;
			$order->receiver_area = $expressInfo->region;
			$order->receiver_address = $expressInfo->address;
			$order->remark = $request['remark'];
			$order->state = 1;
			$order->save();

			$orderGoods = new OrderGoods();
			$orderGoods->order_id = $order->id;
			$orderGoods->goods_id = $goodsInfo->goods_id;
			$orderGoods->spec_id = $goodsInfo->id;
			$orderGoods->spec_values = $goodsInfo->values;
			$orderGoods->goods_name = $goodsInfo->name; //都使用sku名称
			$orderGoods->goods_img = $goodsInfo->img; //都使用sku图片
			$orderGoods->price = $goodsInfo->sell_price;
			$orderGoods->num = $request['num'];
			$orderGoods->prime_cost = $goodsInfo->sell_price;
			$orderGoods->total_price = $orderGoods->price * $request['num'];
			$orderGoods->save();

			if ($order->id && $orderGoods->id) {
				DB::commit();
				return array(
					'code' => 200,
					'messages' => array('保存订单成功'),
					'url' => '/order/cashPay/' . $order->order_sn,
					'data' => array('order_id' => $order->id, 'order_sn' => $order->order_sn, 'payment' => $order->payment),
				);
			} else {
				DB::rollBack();
				return array(
					'code' => 500,
					'messages' => array('写入出错'),
					'url' => '',
				);
			}
		} catch (\Exception $e) {
			DB::rollback();
			return array(
				'code' => 500,
				'messages' => array('保存订单失败'),
				'url' => '',
			);
		}
	}

	public static function balancePay($order) {
		$userInfo = UserService::findById(session('user')->id);
        if (($userInfo->balance-$userInfo->lockBalance) < $order->payment) {
            $res['code'] = 500;
            $res['messages'] = '余额不足，请选择其他支付方式';
            return $res;
        }
		$pay_time = date('Y-m-d H:i:s');
		$updateData = array(
			'real_pay' => $order->payment, //实付款
			'pay_time' => $pay_time, //付款时间
			'pay_type' => 3,
			'state' => 2, //已付款
		);
		//开始事务
		DB::beginTransaction();
		try {
			//更新订单状态
			if (OrderDao::noticeUpdateOrder($order->id, $updateData)) {
				//更新余额
				UserService::balancePay($order->payment, $userInfo->id);
				//更新库存
				GoodsSpecService::updateGoodsSpecNum($order->id);
				//用户级别变更及销售奖励分配
				UserService::upgradeUserLevel($order->user_id);
				//推荐店铺奖励
				UserService::agentRefereeMoney($order);
				//写入支付记录
				$payLogData = array(
					'user_id' => $order->user_id,
					'openid' => $order->openid,
					'pay_type' => 1,
					'gain' => 0,
					'expense' => $order->payment,
					'balance' => $userInfo->balance-$order->payment,
					'order_id' => $order->id,
				);
				PayLogsService::store($payLogData);

				DB::commit();
				//微信通知
				if ($order->openid) {
					$template = config('templatemessage.orderPaySuccess');
					$templateData = array(
						'first' => '您好，您的订单已支付成功',
						'keyword1' => '￥' . $order->payment / 100,
						'keyword2' => $order->order_sn,
						'remark' => '如有问题请联系客服,欢迎再次光临！',
					);
					$url = config('app.url').'/order/detail/'.$order->order_sn;
					WechatNoticeService::sendTemplateMessage($order->user_id, $order->openid, $url, $template['template_id'], $templateData);
				}
				$res['code'] = 200;
				$res['messages'] = '支付成功';
			} else {
				DB::rollback();
				$res['code'] = 500;
				$res['messages'] = '更新失败';
			}
		} catch (\Exception $e) {
			DB::rollback();
			$res['code'] = 500;
			$res['messages'] = '更新失败';
		}
		return $res;
	}

	/**
	 *
	 * @param int $orderId
	 * @param array $orderData
	 * @return int
	 */
	public static function noticeUpdateOrder($orderId, $orderData) {
		return OrderDao::noticeUpdateOrder($orderId, $orderData);
	}

	/**
	 * 微信服务器订单查询
	 * @param string $orderSn
	 * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
	 */
	public static function searchOrderResult($orderSn) {
		$app = EasyWeChat::payment();
		return $app->order->queryByOutTradeNumber($orderSn);
	}

	/**
	 * 取消订单
	 * @param  int $id
	 *
	 * @return array
	 */
	public static function cancel($id) {
		$currentDate = date('Y-m-d H:i:s');

		$order = OrderDao::findById($id);
		if (!$order) {
			return array(
				'code' => 500,
				'messages' => array('订单不存在'),
				'url' => '',
			);
		}

		//状态：1等待付款
		if ($order->state != config('statuses.order.state.waitPay.code')) {
			return array(
				'code' => 500,
				'messages' => array('订单已不能进行取消操作'),
				'url' => '',
			);
		}

		$order->state = config('statuses.order.state.cancel.code');
		$order->updated_at = $currentDate;
		$order->save();

		foreach ($order->orderGoods as &$orderGood) {
			$orderGood->state = config('statuses.orderGood.state.cancel.code');
			$orderGood->updated_at = $currentDate;
			$orderGood->save();
		}

		return array(
			'code' => 200,
			'messages' => array('取消操作成功'),
			'url' => '',
		);
	}

	/**
	 * 发货
	 * @param  int $id
	 *
	 * @return array
	 */
	public static function deliver($request, $id) {
		$currentDate = date('Y-m-d H:i:s');

		$expressName = trimSpace($request->input('expressName', ''));
		$expressNo = trimSpace($request->input('expressNo', ''));
		$orderGoodNums = $request->input('orderGoodNums', array());
		$orderGoodIds = $request->input('orderGoodIds', array());

		if (count($orderGoodNums) == 0) {
			return array(
				'code' => 500,
				'messages' => array('请输入商品的发货数量'),
				'url' => '',
			);
		}
		$count = 0;
		foreach ($orderGoodNums as $num) {
			if (isset($num) && $num != '' && $num > 0) {
				$count++;
			}
		}
		if ($count == 0) {
			return array(
				'code' => 500,
				'messages' => array('请输入商品的发货数量'),
				'url' => '',
			);
		}

		$order = OrderDao::findById($id);
		if (!$order) {
			return array(
				'code' => 500,
				'messages' => array('订单不存在'),
				'url' => '',
			);
		}

		//状态：2已付款准备发货,3等待收货;发货状态：0未发货，1部分发货
		if (!in_array($order->state, array(config('statuses.order.state.waitDelivery.code'), config('statuses.order.state.waitGood.code')))
			|| !in_array($order->deliver_status, array(config('statuses.order.deliverStatus.waitDelivery.code'), config('statuses.order.deliverStatus.partDelivery.code')))) {
			return array(
				'code' => 500,
				'messages' => array('订单已不能进行发货操作'),
				'url' => '',
			);
		}

		$waitDeliveryNums = 0;
		$orderGoods = array();
		foreach ($order->orderGoods as $orderGood) {
			$orderGoods[$orderGood->id] = $orderGood;
			if (in_array($orderGood->state, array(config('statuses.orderGood.state.waitDelivery.code'), config('statuses.orderGood.state.partDelivery.code')))) {
				$waitDeliveryNums += ($orderGood->num - $orderGood->send_num - $orderGood->return_num);
			}
		};

		$count = count($orderGoodIds);
		for ($i = 0; $i < $count; $i++) {
			if (isset($orderGoodNums[$i]) && $orderGoodNums[$i] != '' && $orderGoodNums[$i] > 0
				&& array_key_exists($orderGoodIds[$i], $orderGoods)
				&& in_array($orderGood->state, array(config('statuses.orderGood.state.waitDelivery.code'), config('statuses.orderGood.state.partDelivery.code')))) {
				$waitDeliveryNum = ($orderGoods[$orderGoodIds[$i]]->num - $orderGoods[$orderGoodIds[$i]]->send_num - $orderGoods[$orderGoodIds[$i]]->return_num);
				if ($orderGoodNums[$i] > $waitDeliveryNum) {
					return array(
						'code' => 500,
						'messages' => array($orderGoods[$orderGoodIds[$i]]->goods_name . '发货数量大于未发货数量'),
						'url' => '',
					);
				}

				//更新订单商品状态,发货数量
				$orderGood = $orderGoods[$orderGoodIds[$i]];
				if (($orderGood->send_num + $orderGood->return_num + $orderGoodNums[$i]) == $orderGood->num) {
					$orderGood->state = config('statuses.orderGood.state.delivery.code');
				} else {
					$orderGood->state = config('statuses.orderGood.state.partDelivery.code');
				}
				$orderGood->send_num = $orderGood->send_num + $orderGoodNums[$i];
				$orderGood->save();

				//生成并保存发货记录
				$orderShipping = self::generateOrderShipping($orderGood->id, $expressName, $expressNo, $currentDate);
				$orderShipping->save();

				//发货,相减sku待发货数量
				GoodsSpecDao::decrementWaitNumber($orderGood->spec_id, $orderGoodNums[$i]);

				$waitDeliveryNums -= $orderGoodNums[$i];
			}
		}

		//更新订单发货状态,订单状态,发货时间
		if ($waitDeliveryNums == 0) {
			$order->deliver_status = config('statuses.order.deliverStatus.delivery.code');
			$order->state = config('statuses.order.state.waitGood.code');
		} else {
			$order->deliver_status = config('statuses.order.deliverStatus.partDelivery.code');
			$order->state = config('statuses.order.state.waitGood.code');
		}
		$order->deliver_time = $currentDate;
		$order->save();

		return array(
			'code' => 200,
			'messages' => array('发货成功'),
			'url' => '',
		);
	}

	/**
	 * 申请退款
	 * @param  int $id
	 *
	 * @return array
	 */
	public static function refundment($id) {
		DB::beginTransaction();
		$order = OrderService::findById($id);
		if (!$order) {
			return array(
				'code' => 500,
				'messages' => array('订单不存在'),
				'url' => '',
			);
		}

		//状态：2已付款准备发货,3等待收货;发货状态：0未发货，1部分发货
		if (!in_array($order->state, array(config('statuses.order.state.waitDelivery.code'), config('statuses.order.state.waitGood.code')))
			|| !in_array($order->deliver_status, array(config('statuses.order.deliverStatus.waitDelivery.code'), config('statuses.order.deliverStatus.partDelivery.code')))) {
			return array(
				'code' => 500,
				'messages' => array('订单已不能进行退单退款操作'),
				'url' => '',
			);
		}

		try {
			$nums = 0;
			$refundmentNums = 0;
			$refundmentPrice = 0;
			$orderGoodsRefunds = array();
			foreach ($order->orderGoods as &$orderGood) {
				$nums += $orderGood->num;
				if ($orderGood->num - $orderGood->return_num > 0) {
					$orderGoodsRefunds[] = self::generateOrderGoodsRefund($orderGood);

					//全部退货
					if ($orderGood->return_num == 0) {
						$orderGood->state = config('statuses.orderGood.state.return.code');
					} else {
						//部分退货
						$orderGood->state = config('statuses.orderGood.state.partReturn.code');
					}
					//当次退货数量
					$currentNum = $orderGood->num - $orderGood->return_num;
					$refundmentNums += $currentNum;
					//当次退款金额
					$refundmentPrice += $currentNum * $orderGood->price;
					//当次退货数量
					$orderGood->return_num = $currentNum;
					$orderGood->save();

					//退款,相加sku库存数量,相减sku销售数量,sku待发货数量
					GoodsSpecDao::incrementNumber($orderGood->spec_id, $currentNum);
				}
			};
			//全部退款
			if ($nums == $refundmentNums) {
				$order->state = config('statuses.order.state.refund.code');
			} else {
				//部分退款
				$order->state = config('statuses.order.state.partRefund.code');
			}
			$order->save();

			$refundSn = $order->order_sn . time();
			$orderRefund = self::generateOrderRefund($order, $refundmentPrice, $refundSn);
			$orderRefund = OrderRefundDao::save($orderRefund, session('adminUser')->id);
			if (!$orderRefund) {
				DB::rollback();

				return array(
					'code' => 500,
					'messages' => array('申请退款失败'),
					'url' => '',
				);
			}
			if (count($orderGoodsRefunds) > 0) {
				OrderRefundDao::saveManyGoodsRefunds($orderRefund, $orderGoodsRefunds);
			}

			$orderRefundService = new OrderRefundService();
			$orderRefundService->wechatRefund($order->out_trade_no, $refundSn, $order->real_pay, $refundmentPrice);

			DB::commit();

			return array(
				'code' => 200,
				'messages' => array('申请退款成功'),
				'url' => '',
			);
		} catch (\Exception $e) {
			DB::rollback();

			return array(
				'code' => 500,
				'messages' => array($e->getMessage()),
				'url' => '',
			);
		}
	}

	/**
	 * 退款失败
	 * @param  App\Models\OrderRefund $orderRefund
	 *
	 * @return boolean
	 */
	public static function refundmentFailure($orderRefund) {
		$orderGoodsRefunds = $orderRefund->orderGoodsRefunds->toArray();
		$specIdKeyNumValues = array_column($orderGoodsRefunds, 'refund_num', 'spec_id');

		$returnNums = 0;

		$orderInfo = OrderDao::findByOrderSn($orderRefund->order_sn, true);
		foreach ($orderInfo->orderGoods as &$orderGood) {
			if (array_key_exists($orderGood->spec_id, $specIdKeyNumValues)) {
				$orderGood->return_num = $orderGood->return_num - $specIdKeyNumValues[$orderGood->spec_id];
				//此次退货数量等于商品下单数量时，如有发货修改状态为部分发货，否则为未发货
				if ($orderGood->return_num == 0) {
					$orderGood->state = $orderGood->send_num > 0 ? config('statuses.orderGood.state.partDelivery.code') :
					config('statuses.orderGood.state.waitDelivery.code');
				} else {
					//此次退货数量小于商品下单数量时,修改状态为部分退货
					$orderGood->state = config('statuses.orderGood.state.partReturn.code');
				}
				$orderGood->save();

				//退款失败,相减sku库存数量,相加sku销售数量,sku待发货数量
				GoodsSpecDao::decrementNumber($orderGood->spec_id, $specIdKeyNumValues[$orderGood->spec_id]);
			}

			$returnNums += $orderGood->return_num;
		}

		//此次退货总数量等于商品下单总数量，如有发货，修改状态为等待收货，否则为已付款准备发货
		if ($returnNums == 0) {
			$orderInfo->state = ($orderGood->deliver_status == config('statuses.order.deliverStatus.partDelivery.code')) ?
			config('statuses.order.state.waitGood.code') : config('statuses.order.state.waitDelivery.code');
		} else {
			//此次退货总数量小于商品下单总数量，修改状态为部分退款
			$orderInfo->state = config('statuses.order.state.partRefund.code');
		}
		$orderInfo->save();

		return true;
	}

	public static function findByOrderSn($orderSn, $isNotice = false) {
		if (empty($orderSn)) {
			return null;
		}
		return OrderDao::findByOrderSn($orderSn, $isNotice);
	}

	public static function findAddGoodByOrderSn($orderSn) {
		return OrderDao::findAddGoodByOrderSn($orderSn);
	}

	public static function findById($order_id) {
		return OrderDao::findById($order_id);
	}

	/**
	 * 获取订单
	 * @param int $id
	 * @return object
	 */
	public static function getByOrderSn($orderSn) {
		return OrderDao::getByOrderSn($orderSn);
	}

	/**
	 * @param string $orderSn
	 * @return int
	 */
	public static function confirmReceipt($orderSn) {
		return OrderDao::confirmReceipt($orderSn);
	}

	public static function cancle($orderSn) {
		return OrderDao::cancle($orderSn);
	}

	/**
	 * 分页查询订单
	 * @param  int $pageSize
	 * @param  array $params
	 *
	 * @return array
	 */
	public static function findByPage($curPage, $pageSize, $params = array()) {
		return OrderDao::findByPage($curPage, $pageSize, $params);
	}

	/**
	 * 分页查询订单
	 * @param  int $curPage
	 * @param  int $pageSize
	 * @param  array $params
	 *
	 * @return array
	 */
	public static function findByPageAndParams($curPage, $pageSize, $params = array()) {
		return OrderDao::findByPageAndParams($curPage, $pageSize, $params);
	}

	/**
	 * @param  int $orderGoodId
	 * @param  string $expressName
	 * @param  string $expressNo
	 * @param  string $currentDate
	 *
	 * @return App\Models\OrderShipping
	 */
	private static function generateOrderShipping($orderGoodId, $expressName, $expressNo, $currentDate) {
		$shipping = new OrderShipping();
		$shipping->order_goods_id = $orderGoodId;
		$shipping->express_name = $expressName;
		$shipping->express_no = $expressNo;
		$shipping->express_time = $currentDate;

		return $shipping;
	}

	/**
	 * 生成退货商品
	 * @param  App\Models\OrderGoods $orderGood
	 *
	 * @return App\Models\OrderGoodsRefund
	 */
	public static function generateOrderGoodsRefund($orderGood) {
		$orderGoodsRefunds = new OrderGoodsRefund();
		$orderGoodsRefunds->spec_id = $orderGood->spec_id;
		$orderGoodsRefunds->refund_num = $orderGood->num - $orderGood->return_num;
		$orderGoodsRefunds->refund_fee = ($orderGood->num - $orderGood->return_num) * $orderGood->price;

		return $orderGoodsRefunds;
	}

	/**
	 * 生成退货记录
	 * @param  App\Models\Order $order
	 * @param  int $refundmentPrice
	 * @param  string $refundSn
	 * @return App\Models\OrderRefund
	 */
	public static function generateOrderRefund($order, $refundmentPrice, $refundSn) {
		$orderRefund = new OrderRefund();
		$orderRefund->user_id = $order->user_id;
		$orderRefund->order_sn = $order->order_sn;
		$orderRefund->out_refund_no = $refundSn;
		$orderRefund->total_fee = $order->real_pay;
		$orderRefund->refund_fee = $refundmentPrice;
		$orderRefund->real_refund_fee = 0;
		$orderRefund->refund_desc = '';
		$orderRefund->opera_id = session('adminUser')->id;
		$orderRefund->state = 0;
		return $orderRefund;
	}
}
