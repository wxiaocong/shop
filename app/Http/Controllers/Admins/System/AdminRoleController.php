<?php

namespace App\Http\Controllers\Admins\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\System\AdminRoleRequest;
use App\Services\Admins\AdminCategoryService;
use App\Services\Admins\AdminRightService;
use App\Services\Admins\AdminRoleService;
use App\Utils\Page;

class AdminRoleController extends Controller
{
    public function index()
    {
        $params           = array();
        $curPage          = trimSpace(request('curPage', 1));
        $pageSize         = trimSpace(request('pageSize', Page::PAGESIZE));
        $params['search'] = trimSpace(request('search', ''));

        $page = AdminRoleService::findByPageAndParams($curPage, $pageSize, $params);

        return view('admins.system.adminRoles')
            ->with('search', $params['search'])
            ->with('page', $page);
    }

    public function create()
    {
        return view('admins.system.editAdminRole')
            ->with('rights', $this->dealWithRoleRight());
    }

    public function store(AdminRoleRequest $request)
    {
        $results        = AdminRoleService::saveOrUpdate();
        $results['url'] = '/admin/adminRole';
        return response()->json($results);
    }

    public function edit($id)
    {
        $role = AdminRoleService::findById($id);
        if (!$role) {
            abort(400, '角色不存在');
        }

        $roleRightIds = array();
        $rights       = $role->rights;
        if (count($rights) > 0) {
            $roleRightIds = $rights->pluck('id')->all();
        }

        return view('admins.system.editAdminRole')
            ->with('rights', $this->dealWithRoleRight($roleRightIds))
            ->with('role', $role);
    }

    public function update(AdminRoleRequest $request, $id)
    {
        $results        = AdminRoleService::saveOrUpdate();
        $results['url'] = '/admin/adminRole';
        return response()->json($results);
    }

    public function destroy($id)
    {
        AdminRoleService::destroy(array($id));
        return response()->json(array(
            'code'     => 200,
            'messages' => array('删除成功'),
            'url'      => '/admin/adminRole',
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

        AdminRoleService::destroy($ids);
        return response()->json(array(
            'code'     => 200,
            'messages' => array('删除成功'),
            'url'      => '/admin/adminRole',
        ));
    }

    /**
     * 将权限数据进行封装并返回
     * @param  array $roleRightIds
     *
     * @return array
     */
    private function dealWithRoleRight($roleRightIds = array())
    {
        //查询所有一级权限分类
        $topCategoryList = AdminCategoryService::findByParams(array('orderBy' => array('sort_num' => 'asc'), 'isNull' => array('parent_id')))->values()->all();
        //查询所有二级权限分类
        $secondCategoryList = AdminCategoryService::findByParams(array('orderBy' => array('sort_num' => 'asc'), 'isNotNull' => array('parent_id')))->values()->all();
        //查询所有权限
        $rightList = AdminRightService::findByParams(array('orderBy' => array('category_id' => 'asc')))->values()->all();

        //将权限归类到二级分类下
        $secondCategoryList = $this->dealWithSecondCategories($secondCategoryList, $rightList, $roleRightIds);

        //将二级分类归类到一级分类下
        $topCategoryList = $this->dealWithTopCategories($topCategoryList, $secondCategoryList);

        return $topCategoryList;
    }

    /**
     * 将二级分类归类到一级分类下
     * @param  array $topCategoryList
     * @param  array $secondCategoryList
     *
     * @return array
     */
    private function dealWithTopCategories($topCategoryList, $secondCategoryList)
    {
        $categoryList = array();

        $index = 0;
        foreach ($topCategoryList as $key => $topCategory) {
            $categoryList[$index] = array(
                'id'              => $topCategory['id'],
                'name'            => $topCategory['name'],
                'childCategories' => array(),
            );

            $count         = 0;
            $categoryCount = count($secondCategoryList);
            foreach ($secondCategoryList as $secondCategory) {
                if ($topCategory['id'] == $secondCategory['parentId']) {
                    $categoryList[$index]['childCategories'][] = $secondCategory;

                    if ($categoryCount == 1 && count($categoryList[$index]['childCategories']) > 0 && count($categoryList[$index]['childCategories']) == count(array_column($categoryList[$index]['childCategories'], 'selected'))) {
                        $categoryList[$index]['selected'] = true;
                    }

                    $count++;
                } else if ($count > 0) {
                    if (count($categoryList[$index]['childCategories']) == count(array_column($categoryList[$index]['childCategories'], 'selected'))) {
                        $categoryList[$index]['selected'] = true;
                    }
                    break;
                }

                $categoryCount--;
            }
            if (count($categoryList[$index]['childCategories']) > 0) {
                $index++;
            } else {
                array_splice($categoryList, $index, 1);
            }
        }

        return $categoryList;
    }

    /**
     * 将权限归类到相应的二级分类下
     * @param  array(App\Models\Admins\AdminRightCategory) $secondCategoryList
     * @param  array(App\Models\Admins\AdminRight) $rightList
     * @param  array $roleRightIds
     *
     * @return array
     */
    private function dealWithSecondCategories($secondCategoryList, $rightList, $roleRightIds)
    {
        $categoryList = array();

        $index = 0;
        foreach ($secondCategoryList as $key => $category) {
            $categoryList[$index] = array(
                'id'       => $category['id'],
                'name'     => $category['name'],
                'parentId' => $category['parent_id'],
                'rights'   => array(),
            );

            $count      = 0;
            $rightCount = count($rightList);
            foreach ($rightList as $right) {
                if ($category['id'] == $right['category_id']) {
                    $adminRight = array(
                        'id'         => $right['id'],
                        'name'       => $right['name'],
                        'categoryId' => $right['category_id'],
                        'showMenu'   => $right['show_menu'],
                    );
                    if (count($roleRightIds) > 0 && in_array($right['id'], $roleRightIds)) {
                        $adminRight['selected'] = true;
                    }
                    $categoryList[$index]['rights'][] = $adminRight;

                    if ($rightCount == 1 && count($categoryList[$index]['rights']) > 0 && count($categoryList[$index]['rights']) == count(array_column($categoryList[$index]['rights'], 'selected'))) {
                        $categoryList[$index]['selected'] = true;
                    }

                    $count++;
                } else if ($count > 0) {
                    if (count($categoryList[$index]['rights']) == count(array_column($categoryList[$index]['rights'], 'selected'))) {
                        $categoryList[$index]['selected'] = true;
                    }
                    break;
                }

                $rightCount--;
            }
            if (count($categoryList[$index]['rights']) > 0) {
                $index++;
            } else {
                array_splice($categoryList, $index, 1);
            }
        }

        return $categoryList;
    }
}
