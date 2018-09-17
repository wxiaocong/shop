<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Services\Admins\StatisticalService;
use App\Services\OrderService;

class HomeController extends Controller {
	public function show($id) {
		$adminTopMenus = session('adminTopMenus');
		if (!array_key_exists($id, $adminTopMenus)) {
			abort(401, '您没有权限,请联系管理员赋予权限');
		}

		$subMenu = array();
		$adminLeftMenus = session('adminLeftMenus');
		foreach ($adminLeftMenus[$id] as $leftMenu) {
			if ($leftMenu['isShow'] == 1) {
				$subMenu = $leftMenu['subMenus'][0];
				break;
			}
		}
		if (count($subMenu) == 0) {
			abort(401, '您没有权限,请联系管理员赋予权限');
		}

		session(array('currentTopMenuId' => $id));
		session(array('currentTopMenuName' => $adminTopMenus[$id]['name']));

		setcookie('selectedSubMenu', $subMenu['id'], time() + 24 * 60 * 60 * 1000, '/');

		return redirect($subMenu['url']);
	}

	public function index() {
		session(array('currentTopMenuId' => 6));
		session(array('currentTopMenuName' => '系统'));

		setcookie('selectedSubMenu', 1, time() + 24 * 60 * 60 * 1000, '/');

		$params = array(
			'sort' => array(
				'order.pay_time' => 'desc',
			),
			'in' => array(
				'order.state' => array(
					config('statuses.order.state.waitDelivery.code'),
					config('statuses.order.state.waitGood.code'),
				),
				'order.deliver_status' => array(
					config('statuses.order.deliverStatus.waitDelivery.code'),
					config('statuses.order.deliverStatus.partDelivery.code'),
				),
			),
		);
		$page = OrderService::findByPageAndParams(1, 10, $params);

		$today = date('Y-m-d'); //今天
		$yesterday = date('Y-m-d', strtotime($today . '-1 days')); //昨天
		$currentFirstDay = date('Y-m-01'); //当月第一天
		$beforeMonthFirstDay = date('Y-m-d', strtotime($currentFirstDay . '-1 month')); //上个月第一天
		$beforeMonthLastDay = date('Y-m-d', strtotime($currentFirstDay . ' -1 day')); //上个月最后一天

		return view('admins.home')
			->with('orderPage', $page)
			->with('skuCount', StatisticalService::findSkuCount())
			->with('validSkuCount', StatisticalService::findSkuCount(true))
			->with('registerUserCount', StatisticalService::findRegisterCount())
			->with('weChatAccessUserCount', StatisticalService::findWeChatAccessCount())
			->with('bindWeChatUserCount', StatisticalService::findBindWeChatCount())
			->with('todayNewUserCount', StatisticalService::findNewUserCount($today, $today))
			->with('yesterdayNewUserCount', StatisticalService::findNewUserCount($yesterday, $yesterday))
			->with('currentMonthNewUserCount', StatisticalService::findNewUserCount($currentFirstDay, $today))
			->with('beforeMonthNewUserCount', StatisticalService::findNewUserCount($beforeMonthFirstDay, $beforeMonthLastDay))
			->with('waitPayOrderCount', StatisticalService::findOrderCount(array('state' => config('statuses.order.state.waitPay.code'))))
			->with('waitDeliveryOrderCount', StatisticalService::findOrderCount(array('state' => config('statuses.order.state.waitDelivery.code'))))
			->with('partDeliveryOrderCount', StatisticalService::findOrderCount(array('state' => config('statuses.order.state.waitDelivery.code'), 'deliverStatus' => config('statuses.order.deliverStatus.partDelivery.code'))))
			->with('todayOrderCount', StatisticalService::findOrderCount(array('startDate' => $today, 'endDate' => $today, 'states' => array(config('statuses.order.state.waitDelivery.code'), config('statuses.order.state.waitGood.code'), config('statuses.order.state.finish.code')))))
			->with('yesterdayOrderCount', StatisticalService::findOrderCount(array('startDate' => $yesterday, 'endDate' => $yesterday, 'states' => array(config('statuses.order.state.waitDelivery.code'), config('statuses.order.state.waitGood.code'), config('statuses.order.state.finish.code')))))
			->with('currentMonthOrderCount', StatisticalService::findOrderCount(array('startDate' => $currentFirstDay, 'endDate' => $today, 'states' => array(config('statuses.order.state.waitDelivery.code'), config('statuses.order.state.waitGood.code'), config('statuses.order.state.finish.code')))))
			->with('beforeMonthOrderCount', StatisticalService::findOrderCount(array('startDate' => $beforeMonthFirstDay, 'endDate' => $beforeMonthLastDay, 'states' => array(config('statuses.order.state.waitDelivery.code'), config('statuses.order.state.waitGood.code'), config('statuses.order.state.finish.code')))))
			->with('todayOrderSaleCount', StatisticalService::findOrderSaleCount(array('startDate' => $today, 'endDate' => $today)))
			->with('yesterdayOrderSaleCount', StatisticalService::findOrderSaleCount(array('startDate' => $yesterday, 'endDate' => $yesterday)))
			->with('currentMonthOrderSaleCount', StatisticalService::findOrderSaleCount(array('startDate' => $currentFirstDay, 'endDate' => $today)))
			->with('beforeMonthOrderSaleCount', StatisticalService::findOrderSaleCount(array('startDate' => $beforeMonthFirstDay, 'endDate' => $beforeMonthLastDay)));
	}
}
