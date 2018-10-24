<?php

namespace App\Http\Controllers\Admins\Member;

use App\Http\Controllers\Controller;
use App\Services\AgentService;
use App\Services\AgentTypeService;
use App\Services\Admins\SystemService;
use App\Services\Users\UserService;
use App\Services\Admins\StatisticalService;
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
            if ($agent->state == 3) {
                $userUpdateData = array('level' => 2);
                $userInfo = UserService::findById($agent->user_id);
                //前9000名VIP，基数1000
                if ($userInfo->vip == 0) {
                    $vipCount = StatisticalService::findVipCount();
                    if ($vipCount < 9000) {
                        $userUpdateData['vip'] = 1;
                        $userUpdateData['vipNumber'] = $vipCount + 1001;
                    }
                }
                //审核通过用户升级艾天使
                UserService::getById($agent->user_id)->update($userUpdateData);
                //审核通过有推荐人发放推荐开店奖励
                if ($agent->referee_id > 0) { 
                    switch ($agent->level) {
                        case '1':
                            $reward = SystemService::findByName('recommended_city_shop_commission');
                            break;
                        case '2':
                            $reward = SystemService::findByName('recommended_area_shop_commission');
                            break;
                        default:
                            $reward = SystemService::findByName('recommended_inside_shop_commission');
                            break;
                    }
                    UserService::buildShopRecommend($agent, $reward->val);
                }
            }
            return response()->json(array(
                'code'     => 200,
                'messages' => array('操作成功'),
                'url'      => '/admin/agent/' . $agent->id,
            ));
        }
        return response()->json(array(
            'code'     => 500,
            'messages' => array('操作失败'),
            'url'      => '',
        ));

    }

    public function increStock() {
        $id = intval(request('id'),0);
        $stock = intval(request('stock'),0);
        if ($id < 1 || $stock == 0) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('参数错误'),
                'url'      => '',
            ));
        }
        if(AgentService::increStock($id, $stock)) {
            return response()->json(array(
                'code'     => 200,
                'messages' => array('更新成功'),
                'url'      => '/admin/agent/' . $id,
            ));
        } else {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('该代理商状态不允许增加库存'),
                'url'      => '',
            ));
        }
    }
}
