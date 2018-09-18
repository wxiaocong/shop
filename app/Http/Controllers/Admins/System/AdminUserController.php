<?php

namespace App\Http\Controllers\Admins\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\System\AdminUserRequest;
use App\Services\Admins\AdminRoleService;
use App\Services\Admins\AdminUserService;
use App\Utils\Page;
use Hash;
use RSA;

class AdminUserController extends Controller {
    public function index() {
        $params = array();
        $curPage = trimSpace(request('curPage', 1));
        $pageSize = trimSpace(request('pageSize', Page::PAGESIZE));
        $params['search'] = trimSpace(request('search', ''));

        $page = AdminUserService::findByPageAndParams($curPage, $pageSize, $params);

        return view('admins.system.adminUsers')
            ->with('search', $params['search'])
            ->with('page', $page);
    }

    public function create() {
        return view('admins.system.editAdminUser')
            ->with('roles', AdminRoleService::findByParams());
    }

    public function store(AdminUserRequest $request) {
        $results = AdminUserService::saveOrUpdate();
        $results['url'] = '/admin/adminUser';
        return response()->json($results);
    }

    public function edit($id) {
        $adminUser = AdminUserService::findById($id);
        if (!$adminUser) {
            abort(400, '管理员不存在');
        }

        return view('admins.system.editAdminUser')
            ->with('existRoleIds', $adminUser->roles->pluck('id')->all())
            ->with('roles', AdminRoleService::findByParams())
            ->with('user', $adminUser);
    }

    public function update(AdminUserRequest $request, $id) {
        $results = AdminUserService::saveOrUpdate();
        $results['url'] = '/admin/adminUser';
        return response()->json($results);
    }

    public function destroy($id) {
        AdminUserService::destroy(array($id));
        return response()->json(array(
            'code' => 200,
            'messages' => array('删除成功'),
            'url' => '/admin/adminUser',
        ));
    }

    public function destroyAll() {
        $ids = request('ids', array());
        if (count($ids) == 0) {
            return response()->json(array(
                'code' => 500,
                'messages' => array('参数错误'),
                'url' => '',
            ));
        }

        AdminUserService::destroy($ids);
        return response()->json(array(
            'code' => 200,
            'messages' => array('删除成功'),
            'url' => '/admin/adminUser',
        ));
    }

    public function editPassword() {
        return view('admins.system.adminUserEditPwd');
    }

    public function updatePassword() {
        $oldPassword = trimSpace(clean(request('oldPassword', '')));
        $password = trimSpace(clean(request('password', '')));
        $oldPassword = RSA::decrypt($oldPassword);
        $password = RSA::decrypt($password);

        $adminUser = session('adminUser');
        if (!Hash::check(env('ADMIN_PASSWORD_SALT') . $oldPassword, $adminUser->password)) {
            return response()->json(
                array(
                    'code' => 500,
                    'messages' => array('原密码不正确'),
                    'url' => '',
                )
            );
        }

        $adminUser->password = Hash::make(env('ADMIN_PASSWORD_SALT') . $password);
        AdminUserService::update($adminUser);

        return response()->json(array(
            'code' => 200,
            'messages' => array('修改密码成功'),
            'url' => '/admin/logout',
        ));
    }
}
