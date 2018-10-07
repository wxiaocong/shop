<?php

namespace App\Services;

use App\Models\Agent;
use App\Daoes\AgentDao;
use App\Services\Users\UserService;
use App\Services\PayLogsService;
use EasyWeChat;
use Illuminate\Support\Facades\DB;

class AgentService {

	//创建订单:单商品
	public static function store($request) {
		$levelType = AgentTypeService::getAll();
		//开始事务
		DB::beginTransaction();
		try {
			$agent = new Agent();
			$agent->user_id = session('user')->id;
			$agent->openid = session('user')->openid;
			$agent->order_sn = createOrderSn();
			if (! in_array($request['level'], array_keys($levelType))) {
				return array(
					'code' => 500,
					'messages' => array('错误的合伙类型'),
					'url' => '',
				);
			}
			$agent->level = $request['level'];
			$agent->payment = $levelType[$request['level']]->price;
			$agent->goodsNum = $levelType[$request['level']]->goodsNum;
			$agent->agent_name = $request['agent_name'];
			$agent->mobile = $request['mobile'];
			if (substr( $request['front_identity_card'], 0, 1 ) != '/') {
				$request['front_identity_card'] = '/'.$request['front_identity_card'];
			}
			if (substr( $request['back_identity_card'], 0, 1 ) != '/') {
				$request['back_identity_card'] = '/'.$request['back_identity_card'];
			}
			$agent->front_identity_card = $request['front_identity_card'];
			$agent->back_identity_card = $request['back_identity_card'];
			$agent->province = $request['province'];
			$agent->city = $request['city'];
			$agent->area = $request['area'];
			$agent->address = $request['address'];
			$agent->remark = $request['remark'];
			$agent->state = 1;
			$agent->save();

			if ($agent->id) {
				DB::commit();
				return array(
					'code' => 200,
					'messages' => array('保存订单成功'),
					'url' => '/agent/cashPay/' . $agent->order_sn,
					'data' => array('order_id' => $agent->id, 'order_sn' => $agent->order_sn, 'payment' => $agent->payment),
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

	public static function balancePay($orderInfo) {
		$userInfo = UserService::findById(session('user')->id);
        if (($userInfo->balance-$userInfo->lockBalance) < $orderInfo->payment) {
            $res['code'] = 500;
            $res['messages'] = '余额不足，请选择其他支付方式';
            return $res;
        }
		$pay_time = date('Y-m-d H:i:s');
		$updateData = array(
			'real_pay' => $orderInfo->payment, //实付款
			'pay_time' => $pay_time, //付款时间
			'pay_type' => 3,
			'state' => 2, //已付款
		);
        //推荐人是否为艾天使
        $refereeLevel = UserService::findRefereeLevel($orderInfo->user_id);
        if (! empty($refereeLevel) && $refereeLevel['level'] == 2) {
            $updateData['referee_id'] = $refereeLevel['referee_id'];
        }
		//开始事务
		DB::beginTransaction();
		try {
			//更新订单状态
            if (AgentDao::noticeUpdateAgent($orderInfo->id, $updateData)) {
                //扣减余额
                if(UserService::getById(session('user')->id)->decrement('balance', $orderInfo->payment)) {
                    //写入支付记录
                    $payLogData = array(
                        'user_id' => $orderInfo->user_id,
                        'openid' => $orderInfo->openid,
                        'pay_type' => 11,
                        'gain' => 0,
                        'expense' => $orderInfo->payment,
                        'balance' => $userInfo->balance-$orderInfo->payment,
                        'order_id' => $orderInfo->id,
                    );
                    PayLogsService::store($payLogData);
                }
                DB::commit();
                //微信通知
                if ($orderInfo->openid) {
                    $template = config('templatemessage.orderPaySuccess');
                    $templateData = array(
                        'first' => '您好，您的订单已支付成功',
                        'keyword1' => '￥' . $result['cash_fee'] / 100,
                        'keyword2' => $orderInfo->order_sn,
                        'remark' => '如有问题请联系客服,欢迎再次光临！',
                    );
                    $url = config('app.url').'/agent/detail/'.$orderSn;
                    WechatNoticeService::sendTemplateMessage($orderInfo->user_id, $orderInfo->openid, $url, $template['template_id'], $templateData);
                }
                $res['code'] = 200;
				$res['messages'] = '支付成功';
			} else {
				DB::rollback();
				$res['code'] = 500;
				$res['messages'] = '支付失败';
			}
		} catch (\Exception $e) {
			DB::rollback();
			$res['code'] = 500;
			$res['messages'] = '支付失败';
		}
		return $res;
	}

	/**
	* 保存
	* @param  App\Models\Users\User $user
	*
	* @return array
	*/
    public static function update($agent) {
        $agent = AgentDao::save($agent, null);
        if (!$agent) {
            return array(
                'code' => 500,
                'messages' => array('更新失败'),
                'url' => '',
            );
        }

        return array(
            'code' => 200,
            'messages' => array('更新成功'),
            'url' => '',
        );
    }

    /**
     * 跟据订单地址查询取货店
     * @param  [type] $province [description]
     * @param  [type] $city     [description]
     * @return [type]           [description]
     */
    public static function findAgentByAddress($province, $city, $area = 0) {
    	return AgentDao::findAgentByAddress($province, $city, $area);
    }

	/**
	 *
	 * @param int $orderId
	 * @param array $orderData
	 * @return int
	 */
	public static function noticeUpdateAgent($agentId, $agentData) {
		return AgentDao::noticeUpdateAgent($agentId, $agentData);
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

		$order = AgentDao::findById($id);
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

		return array(
			'code' => 200,
			'messages' => array('取消操作成功'),
			'url' => '',
		);
	}

	public static function findByOrderSn($orderSn, $isNotice = false) {
		if (empty($orderSn)) {
			return null;
		}
		return AgentDao::findByOrderSn($orderSn, $isNotice);
	}

	public static function findOrderSnBalance($orderSn) {
		return AgentDao::findOrderSnBalance($orderSn);
	}

	public static function findById($order_id) {
		return AgentDao::findById($order_id);
	}

	/**
	 * 获取订单
	 * @param int $id
	 * @return object
	 */
	public static function getByOrderSn($orderSn) {
		return AgentDao::getByOrderSn($orderSn);
	}

	public static function cancle($orderSn) {
		return AgentDao::cancle($orderSn);
	}

	/**
	 * 分页查询订单
	 * @param  int $pageSize
	 * @param  array $params
	 *
	 * @return array
	 */
	public static function findByPage($curPage, $pageSize, $params = array()) {
		return AgentDao::findByPage($curPage, $pageSize, $params);
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
		return AgentDao::findByPageAndParams($curPage, $pageSize, $params);
	}
}
