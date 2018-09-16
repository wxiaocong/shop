<?php

namespace App\Http\Controllers\Admins\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\System\AdminRightRequest;
use App\Services\Admins\AdminCategoryService;
use App\Services\Admins\AdminRightService;
use App\Utils\Page;

class AdminRightController extends Controller
{
    public function index()
    {
        $params               = array();
        $curPage              = trimSpace(request('curPage', 1));
        $pageSize             = trimSpace(request('pageSize', Page::PAGESIZE));
        $params['search']     = trimSpace(request('search', ''));
        $params['categoryId'] = trimSpace(request('categoryId', 0));

        $page = AdminRightService::findByPageAndParams($curPage, $pageSize, $params);

        return view('admins.system.adminRights')
            ->with('categoryList', AdminCategoryService::findByParams())
            ->with('search', $params['search'])
            ->with('categoryId', $params['categoryId'])
            ->with('page', $page);
    }

    public function create()
    {
        return view('admins.system.editAdminRight')
            ->with('categoryList', AdminCategoryService::findByParams(array('isNotNull' => array('parent_id'))));
    }

    public function store(AdminRightRequest $request)
    {
        $results        = AdminRightService::saveOrUpdate($request);
        $results['url'] = '/admin/right';
        return response()->json($results);
    }

    public function edit($id)
    {
        $right = AdminRightService::findById($id);
        if (!$right) {
            abort(400, '权限不存在');
        }
        return view('admins.system.editAdminRight')
            ->with('categoryList', AdminCategoryService::findByParams(array('isNotNull' => array('parent_id'))))
            ->with('right', $right);
    }

    public function update(AdminRightRequest $request, $id)
    {
        $results        = AdminRightService::saveOrUpdate($request);
        $results['url'] = '/admin/right';
        return response()->json($results);
    }

    public function destroy($id)
    {
        AdminRightService::destroy(array($id));
        return response()->json(array(
            'code'     => 200,
            'messages' => array('删除成功'),
            'url'      => '/admin/right',
        ));
    }

    public function destroyAll()
    {
        $ids = request('ids', array());
        if (count($ids) == 0) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('参数错误'),
                'url'      => '',
            ));
        }

        AdminRightService::destroy($ids);
        return response()->json(array(
            'code'     => 200,
            'messages' => array('删除成功'),
            'url'      => '/admin/right',
        ));
    }

    public function sort($id)
    {
        $right = AdminRightService::findById($id);
        if (!$right) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('权限不存在'),
                'url'      => '',
            ));
        }

        $sort            = request('sort', 99);
        $right->sort_num = $sort;

        return $this->updateRight($right);
    }

    public function updateShowMenu($id)
    {
        $right = AdminRightService::findById($id);
        if (!$right) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('权限不存在'),
                'url'      => '',
            ));
        }

        if ($right->show_menu == config('statuses.adminRight.showMenu.yes.code')) {
            $right->show_menu = config('statuses.adminRight.showMenu.no.code');
        } else {
            $right->show_menu = config('statuses.adminRight.showMenu.yes.code');
        }

        return $this->updateRight($right);
    }

    /**
     * 更新
     * @param App\Models\Admins\AdminRight $right
     *
     * @return json
     */
    private function updateRight($right)
    {
        if (!AdminRightService::update($right)) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('操作失败'),
                'url'      => '',
            ));
        }

        return response()->json(array(
            'code'     => 200,
            'messages' => array('操作成功'),
            'url'      => '/admin/right',
        ));
    }
}
