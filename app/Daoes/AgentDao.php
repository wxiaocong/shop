<?php

namespace App\Daoes;

use App\Daoes\BaseDao;
use App\Utils\DateUtils;
use App\Models\Agent;
use App\Utils\Page;
use Illuminate\Support\Facades\DB;

class AgentDao extends BaseDao {

    /**
     * 跟据订单地址查询取货店
     * @param  [type] $province [description]
     * @param  [type] $city     [description]
     * @return [type]           [description]
     */
    public static function findAgentByAddress($province, $city, $area = 0) {
    	if ($area) {
    		//区域店
    		return Agent::where(['province'=>$province, 'city'=>$city, 'area'=>$area, 'level' => 2, 'state'=>3])->orderBy('id', 'desc')->first();
    	} else {
    		//旗舰店
    		return Agent::where(['province'=>$province, 'city'=>$city, 'level' => 1, 'state'=>3])->orderBy('id', 'desc')->first();
    	}
    	
    }

    /**
     * 跟据用户查找店铺
     * @param  [type] $user_id [description]
     * @return [type]          [description]
     */
    public static function findByUserId($user_id) {
        return Agent::where('user_id', $user_id)->whereIn('state', array(1,2,3))->orderBy('id', 'desc')->first();
    }

	/**
	 * 根据id查询订单,没加用户
	 * @param int $id
	 *
	 * @return App\Models\User\Agent
	 */
	public static function findById($id) {
		return Agent::find($id);
	}

	/**
	 * 获取订单
	 * @param int $id
	 * @return object
	 */
	public static function getByOrderSn($orderSn) {
		return Agent::where('order_sn', $orderSn);
	}

	/**
	 * 订单号查询订单
	 * @param unknown $orderSn
	 * @return object
	 */
	public static function findByOrderSn($orderSn, $isNotice = false) {
		$builder = Agent::where('order_sn', $orderSn);
		if (!$isNotice) {
			$builder->where('user_id', session('user')->id);
		}
		return $builder->first();
	}

	public static function findOrderSnBalance($orderSn) {
		return Agent::leftJoin('users', 'agent.user_id', '=', 'users.id')
			->where('agent.order_sn', $orderSn)
			->groupBy('agent.id')
			->select('agent.*', 'users.balance')
			->first();
	}


	//取消订单
	public static function cancle($orderSn) {
		return Order::where(array('order_sn' => $orderSn, 'user_id' => session('user')->id, 'state' => 1))->update(array('state' => 6));
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
		$builder = Agent::leftJoin('users as u', 'agent.user_id', '=', 'u.id')
			->leftJoin('users as r', 'agent.referee_id', '=', 'r.id')->select('agent.*', 'u.nickname', 'r.nickname as referee_name');

		if (array_key_exists('search', $params) && $params['search'] != '') {
			$builder->where(function ($query) use ($params) {
				$query->where('agent.order_sn', 'like', '%' . $params['search'] . '%')
					->orWhere('agent.mobile', 'like', '%' . $params['search'] . '%')
					->orWhere('agent.agent_name', 'like', '%' . $params['search'] . '%')
					->orWhere('u.nickname', 'like', '%' . $params['search'] . '%');
			});
		}
		if (array_key_exists('state', $params) && $params['state'] != '') {
			$builder->where('agent.state', $params['state']);
		}
		if (array_key_exists('startPayDate', $params) && $params['startPayDate'] != '') {
			$builder->where('agent.pay_time', '>=', DateUtils::addDay(0, $params['startPayDate']));
		}
		if (array_key_exists('endPayDate', $params) && $params['endPayDate'] != '') {
			$builder->where('agent.pay_time', '<', DateUtils::addDay(1, $params['endPayDate']));
		}
		if (array_key_exists('startDate', $params) && $params['startDate'] != '') {
			$builder->where('agent.created_at', '>=', DateUtils::addDay(0, $params['startDate']));
		}
		if (array_key_exists('endDate', $params) && $params['endDate'] != '') {
			$builder->where('agent.created_at', '<', DateUtils::addDay(1, $params['endDate']));
		}
		if (array_key_exists('in', $params)) {
			foreach ($params['in'] as $key => $value) {
				$builder->whereIn($key, $value);
			}
		}
		if (array_key_exists('sort', $params)) {
			foreach ($params['sort'] as $key => $value) {
				$builder->agent($key, $value);
			}
		} else {
			$builder->orderBy('agent.created_at', 'desc');
		}

		return new Page($builder->paginate($pageSize, array('*'), 'page', $curPage));
	}

	/**
	 *
	 * @param int $agentId
	 * @param array $agentData
	 * @return int
	 */
	public static function noticeUpdateAgent($agentId, $agentData) {
		return Agent::where(array('id' => $agentId, 'state' => 1))->update($agentData);
	}
}
