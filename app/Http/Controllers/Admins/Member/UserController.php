<?php

namespace App\Http\Controllers\Admins\Member;

use App\Http\Controllers\Controller;
use App\Services\Users\UserService;
use App\Services\Admins\StatisticalService;
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
            ->with('search', $params['search'])
            ->with('userLevel', config('statuses.user.levelState'));
    }

    public function show($id)
    {
        $user = UserService::findUserAndRefee($id);
        if (!$user) {
            abort(400, '用户不存在');
        }

        return view('admins.member.user')
            ->with('user', $user)
            ->with('userLevel', config('statuses.user.levelState'));
    }

    public function changeLevel($id)
    {
        $level = trimSpace(request('level'));
        $user = UserService::findById($id);
        if (!$user) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('用户不存在'),
                'url'      => '',
            ));
        }
        if ($level != $user->level && $level > $user->level && in_array($level,array_keys(config('statuses.user.levelState')))) {
            //前9000名VIP，基数1000
             $userUpdateData = array('level' => $level);
            if ($user->vip == 0) {
                $vipCount = StatisticalService::findVipCount();
                if ($vipCount < 9000) {
                    $userUpdateData['vip'] = 1;
                    $userUpdateData['vipNumber'] = $vipCount + 1001;
                }
            }
            UserService::getById($id)->update($userUpdateData);
            return response()->json(array(
                'code'     => 200,
                'messages' => array('修改成功'),
                'url'      => '',
            ));
        } else {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('级别错误,未修改'),
                'url'      => '',
            ));
        }
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
