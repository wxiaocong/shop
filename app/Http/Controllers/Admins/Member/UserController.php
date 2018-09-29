<?php

namespace App\Http\Controllers\Admins\Member;

use App\Http\Controllers\Controller;
use App\Services\Users\UserService;
use App\Services\Users\WechatUserService;
use App\Utils\Page;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $params               = array();
        $curPage              = trimSpace(request('curPage', 1));
        $pageSize             = trimSpace(request('pageSize', Page::PAGESIZE));
        $params['search']     = trimSpace(request('search', ''));

        $page = UserService::findByPageAndParams($curPage, $pageSize, $params);
        return view('admins.member.users')
            ->with('page', $page)
            ->with('search', $params['search']);
    }

    public function wechatUserIndex()
    {
        $params           = array();
        $curPage          = trimSpace(request('curPage', 1));
        $pageSize         = trimSpace(request('pageSize', Page::PAGESIZE));
        $params['search'] = trimSpace(request('search', ''));
        $params['isBind'] = trimSpace(request('isBind', 0));

        $page = WechatUserService::findByPageAndParams($curPage, $pageSize, $params);
        return view('admins.member.weChatUsers')
            ->with('page', $page)
            ->with('search', $params['search'])
            ->with('isBind', $params['isBind']);
    }

    public function merchantApplyIndex()
    {
        $params                       = array();
        $curPage                      = trimSpace(request('curPage', 1));
        $pageSize                     = trimSpace(request('pageSize', Page::PAGESIZE));
        $params['search']             = trimSpace(request('search', ''));
        $params['businessAuditState'] = config('statuses.user.businessAuditState.apply.code');

        $page = UserService::findByPageAndParams($curPage, $pageSize, $params);
        return view('admins.member.merchantApplies')
            ->with('page', $page)
            ->with('search', $params['search']);
    }

    public function show($id)
    {
        $user = UserService::findById($id);
        if (!$user) {
            abort(400, '用户不存在');
        }

        return view('admins.member.user')
            ->with('user', $user);
    }

    public function merchantAudit($id)
    {
        $type   = trimSpace(request('type', ''));
        $remark = trimSpace(request('remark', ''));

        if ($type == '' || !in_array($type, array('pass', 'refuse'))) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('操作类型错误'),
                'url'      => '',
            ));
        }
        if ($remark == '') {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('审核备注不能为空'),
                'url'      => '',
            ));
        }
        $user = UserService::findById($id);
        if (!$user) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('用户不存在'),
                'url'      => '',
            ));
        }
        if ($user->business_audit_state != config('statuses.user.businessAuditState.apply.code')) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('当前状态,不能进行审核操作'),
                'url'      => '',
            ));
        }

        $user->business_audit_time = date('Y-m-d H:i:s');
        if ($type == 'pass') {
            $user->business_audit_state = config('statuses.user.businessAuditState.pass.code');
            $user->is_business          = config('statuses.zeroAndOne.one.code');
        } else {
            $user->business_audit_state = config('statuses.user.businessAuditState.refuse.code');
        }
        $user->business_audit_remark = $remark;
        $results                     = UserService::update($user);
        if ($results['code'] != 200) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array(translateStatus('user.businessAuditState', config('statuses.user.businessAuditState.pass.code')) . '操作失败'),
                'url'      => '',
            ));
        }
        return response()->json(array(
            'code'     => 200,
            'messages' => array(translateStatus('user.businessAuditState', config('statuses.user.businessAuditState.refuse.code')) . '操作成功'),
            'url'      => '/admin/user/' . $id,
        ));

    }

    public function updateState($id)
    {
        $user = UserService::findById($id);
        if (!$user) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('用户不存在'),
                'url'      => '',
            ));
        }

        $state = config('statuses.user.state.normal.code');
        if ($user->state == config('statuses.user.state.normal.code')) {
            $state = config('statuses.user.state.lock.code');
        }

        $user->state      = $state;
        $user->updated_at = date('Y-m-d H:i:s');
        $results          = UserService::update($user);
        if ($results['code'] != 200) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array(translateStatus('user.state', $state) . '操作失败'),
                'url'      => '',
            ));
        }
        return response()->json(array(
            'code'     => 200,
            'messages' => array(translateStatus('user.state', $state) . '操作成功'),
            'url'      => '',
        ));
    }
}
