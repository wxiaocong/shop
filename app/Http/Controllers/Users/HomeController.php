<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\BusinessRequest;
use App\Http\Requests\Users\UsersRequest;
use App\Services\AreasService;
use App\Services\Users\UserService;
use Hash;
use RSA;
use Session;

class HomeController extends Controller
{
    public function index()
    {
        return view('users.home', array('userInfo' => UserService::findById(session('user')->id)));
    }

    //个人信息
    public function show()
    {
        $userInfo = UserService::findById(session('user')->id);
        if ($userInfo->province) {
            //组合省市区
            $userInfo['region'] = AreasService::convertAreaIdToName(array($userInfo->province, $userInfo->city, $userInfo->area));
        }
        return view('users.person', array('userInfo' => $userInfo));
    }

    //商家信息
    public function business()
    {
        $userInfo = UserService::findById(session('user')->id);
        if ($userInfo->company_province) {
            //组合省市区
            $userInfo['region'] = AreasService::convertAreaIdToName(array($userInfo->company_province, $userInfo->company_city, $userInfo->company_area));
        }
        return view('users.business', array('userInfo' => $userInfo));
    }

    //保存商家信息
    public function saveBusiness(BusinessRequest $request)
    {
        //未修改省市区
        if (empty($request['company_province']) || empty($request['company_city']) || empty($request['company_area'])) {
            unset($request['company_province'], $request['company_city'], $request['company_area']);
        }
        $request['business_audit_state'] = 1; //申请中
        $request['business_apply_time']  = date('Y-m-d H:i:s');
        if (UserService::getById(session('user')->id)->whereIn('business_audit_state', array(0, 3))->update($request->all())) {
            return response()->json(array(
                'code'     => 200,
                'messages' => array('保存成功'),
                'url'      => '/home',
            ));
        } else {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('保存失败'),
                'url'      => '',
            ));
        }
    }

    //修改密码页面
    public function changePwd()
    {
        return view('users.changePwd');
    }

    //修改密码
    public function updatePwd()
    {
        $oldPwd   = request('oldPwd', '');
        $password = request('password', '');

        $password = RSA::decrypt($password);
        if (strlen($password) < 6 || strlen($password) > 20) {
            return response()->json(
                array(
                    'code'     => 500,
                    'messages' => array('请输入6-20位密码'),
                    'url'      => '',
                )
            );
        }
        $oldPwd = RSA::decrypt($oldPwd);
        if (!Hash::check(env('USER_PASSWORD_SALT') . $oldPwd, session('user')->password)) {
            return response()->json(
                array(
                    'code'     => 500,
                    'messages' => array('密码不正确'),
                    'url'      => '',
                )
            );
        }

        $request['password'] = Hash::make(env('USER_PASSWORD_SALT') . $password);
        if (UserService::getById(session('user')->id)->update($request)) {
            return response()->json(
                array(
                    'code'     => 200,
                    'messages' => array('修改密码成功'),
                    'url'      => '/login/logout',
                )
            );
        } else {
            return response()->json(
                array(
                    'code'     => 500,
                    'messages' => array('修改密码失败'),
                    'url'      => '',
                )
            );
        }
    }

    //保存个人信息
    public function update(UsersRequest $request, int $id)
    {
        $id     = session('user')->id;
        $result = UserService::existColumn('nickname', $request['nickname'], $id);
        if ($result) {
            return response()->json($result);
        }
        if ($request['sex'] == '男') {
            $request['sex'] = 1;
        } elseif ($request['sex'] == '女') {
            $request['sex'] = 2;
        } else {
            $request['sex'] = 0;
        }
        //日期控件默认值问题，结尾有空格
        $request['birthday'] = empty(trim($request['birthday'])) ? null : trim($request['birthday']);
        //未修改省市区
        if (empty($request['province']) || empty($request['city']) || empty($request['area'])) {
            unset($request['province'], $request['city'], $request['area']);
        }
        if (UserService::getById($id)->update($request->all())) {
            Session(array('user' => UserService::findById($id)));
            return response()->json(array(
                'code'     => 200,
                'messages' => array('保存成功'),
                'url'      => '/home',
            ));
        } else {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('保存失败'),
                'url'      => '',
            ));
        }
    }
}
