<?php

namespace App\Http\Controllers\Admins\Order;

use App\Http\Controllers\Controller;
use App\Services\Users\UserService;
use App\Services\PayLogsService;
use App\Utils\Page;
use Illuminate\Http\Request;

class PayLogController extends Controller
{
    public function index()
    {
        $params                  = array();
        $curPage                 = trimSpace(request('curPage', 1));
        $pageSize                = trimSpace(request('pageSize', Page::PAGESIZE));
        $params['search']        = trimSpace(request('search', ''));
        $params['payType']         = trimSpace(request('payType', ''));
        $params['startDate']     = trimSpace(request('startPayDate', ''));
        $params['endDate']       = trimSpace(request('endPayDate', ''));

        $params['type'] = array(
            1 => '订单付款',
            5 => '销售提成',
            6 => '协助收益',
            7 => 'vip额外奖励',
            8 => '推荐店铺销售奖励',
            9 => '店铺销售奖励',
            10=> '推荐代理商奖励'
        );

        $page = PayLogsService::getAll($curPage, $pageSize, $params);

        return view('admins.order.payLog')
            ->with('page', $page)
            ->with('type', $params['type'])
            ->with('search', $params['search'])
            ->with('payType', $params['payType'])
            ->with('startPayDate', $params['startDate'])
            ->with('endPayDate', $params['endDate']);
    }
}
