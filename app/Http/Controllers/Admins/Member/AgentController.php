<?php

namespace App\Http\Controllers\Admins\Member;

use App\Http\Controllers\Controller;
use App\Services\AgentService;
use App\Services\AgentTypeService;
use App\Utils\Page;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function index()
    {
        $params                       = array();
        $curPage                      = trimSpace(request('curPage', 1));
        $pageSize                     = trimSpace(request('pageSize', Page::PAGESIZE));
        $params['search']             = trimSpace(request('search', ''));

        $page = AgentService::findByPageAndParams($curPage, $pageSize, $params);
        return view('admins.member.agentApplies')
            ->with('page', $page)
            ->with('agent', AgentTypeService::getAll())
            ->with('agentState',config('statuses.agentState'))
            ->with('search', $params['search']);
    }

    public function show($id)
    {
        $agent = AgentService::findById($id);
        if (!$agent) {
            abort(400, '订单不存在');
        }

        return view('admins.member.agent')
            ->with('agent', $agent)
            ->with('agentLevel', AgentTypeService::getAll())
            ->with('agentState',config('statuses.agentState'));
    }

    public function audit($id)
    {
        $type   = trimSpace(request('type', ''));

        if ($type == '' || !in_array($type, array('pass', 'refuse'))) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('操作类型错误'),
                'url'      => '',
            ));
        }
        $agent = AgentService::findById($id);
        if (!$agent) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('订单不存在'),
                'url'      => '',
            ));
        }
        if ($agent->state != 2) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('当前状态,不能进行审核操作'),
                'url'      => '',
            ));
        }

        if ($type == 'pass') {
            $agent->state = 3;
        } else {
            $agent->state = 4;
        }
        $results = AgentService::getByOrderSn($agent->order_sn)->update(['state'=>$agent->state]);
        if ($results) {
            return response()->json(array(
                'code'     => 200,
                'messages' => array('操作成功'),
                'url'      => '/admin/user/agent/' . $id,
            ));
        }
        return response()->json(array(
            'code'     => 500,
            'messages' => array('操作失败'),
            'url'      => '',
        ));

    }
}
