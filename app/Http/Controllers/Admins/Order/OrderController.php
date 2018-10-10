<?php

namespace App\Http\Controllers\Admins\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Order\OrderRequest;
use App\Services\OrderRefundService;
use App\Services\OrderService;
use App\Services\OrderShippingService;
use App\Services\PayLogsService;
use App\Utils\Page;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $params                  = array();
        $curPage                 = trimSpace(request('curPage', 1));
        $pageSize                = trimSpace(request('pageSize', Page::PAGESIZE));
        $params['search']        = trimSpace(request('search', ''));
        $params['state']         = trimSpace(request('state', ''));
        $params['deliverStatus'] = trimSpace(request('deliverStatus', ''));
        $params['startDate']     = trimSpace(request('startPayDate', ''));
        $params['endDate']       = trimSpace(request('endPayDate', ''));

        $page = OrderService::findByPageAndParams($curPage, $pageSize, $params);
        return view('admins.order.orders')
            ->with('page', $page)
            ->with('search', $params['search'])
            ->with('state', $params['state'])
            ->with('deliverStatus', $params['deliverStatus'])
            ->with('startPayDate', $params['startDate'])
            ->with('endPayDate', $params['endDate'])
            ->with('province', json_decode(AreasService::getAreasTree(0)));
    }

    public function show($id)
    {
        $order = OrderService::findById($id);
        if (!$order) {
            abort(400, '订单不存在');
        }

        $orderShipingArr   = array();
        $orderShippingList = OrderShippingService::findByOrderId($id);
        foreach ($orderShippingList as $orderShipping) {
            if (array_key_exists($orderShipping->express_no, $orderShipingArr)) {
                $orderShipingArr[$orderShipping->express_no]['text'] .= '; ' . $orderShipping->orderGood->goods_name;
            } else {
                $orderShipingArr[$orderShipping->express_no] = array(
                    'express_name' => $orderShipping->express_name,
                    'express_no'   => $orderShipping->express_no,
                    'express_time' => $orderShipping->express_time,
                    'text'         => '发货商品：' . $orderShipping->orderGood->goods_name,
                );
            }
        }

        return view('admins.order.order')
            ->with('order', $order)
            ->with('orderShipingList', $orderShipingArr)
            ->with('orderPayLogs', PayLogsService::findByParams(array('orderId' => $order->id, 'payType' => 1, 'orderBy' => array('created_at' => 'desc'))))
            ->with('orderRefundLogs', OrderRefundService::findByParams(array('orderSn' => $order->order_sn, 'orderBy' => array('created_at' => 'desc'))));
    }

    public function cancel($id)
    {
        $results        = OrderService::cancel($id);
        $results['url'] = '/admin/order/' . $id;
        return response()->json($results);
    }

    public function refundment($id)
    {
        $results        = OrderService::refundment($id);
        $results['url'] = '/admin/order/' . $id;
        return response()->json($results);
    }

    public function deliver(OrderRequest $request, $id)
    {
        $results        = OrderService::deliver($request, $id);
        $results['url'] = '/admin/order/' . $id;
        return response()->json($results);
    }
}
