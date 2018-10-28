<?php

namespace App\Http\Controllers\Admins\Order;

use App\Http\Controllers\Controller;
use App\Services\Users\UserService;
use App\Services\PayLogsService;
use App\Utils\Page;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function index()
    {
        $params                  = array();
        $curPage                 = trimSpace(request('curPage', 1));
        $pageSize                = trimSpace(request('pageSize', Page::PAGESIZE));
        $params['search']        = trimSpace(request('search', ''));
        $params['state']         = trimSpace(request('state', ''));
        $params['startDate']     = trimSpace(request('startPayDate', ''));
        $params['endDate']       = trimSpace(request('endPayDate', ''));

        $page = PayLogsService::findByPage($curPage, $pageSize, $params);
        return view('admins.order.payLog')
            ->with('page', $page)
            ->with('search', $params['search'])
            ->with('state', $params['state'])
            ->with('startPayDate', $params['startDate'])
            ->with('endPayDate', $params['endDate']);
    }
}
