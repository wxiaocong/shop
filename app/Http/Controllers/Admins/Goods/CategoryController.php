<?php

namespace App\Http\Controllers\Admins\Goods;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Goods\CategoryRequest;
use App\Services\CategoryService;
use App\Utils\Page;

class CategoryController extends Controller
{
    public function index()
    {
//         $curPage  = trimSpace(request('curPage', 1));
//         $pageSize = trimSpace(request('pageSize', Page::PAGESIZE));
//         $page = CategoryService::findByPage($curPage, $pageSize);

        return view('admins.goods.categoryList',['categoryList'=>CategoryService::getCategoryList()]);
    }

    //获取子分类列表
    public function getCategory()
    {
        $parent_id = intval(request('category_id', 0));
        $level     = intval(request('level', 1));
        $isEdit    = intval(request('isEdit', 0));
        if ($isEdit) {
            return CategoryService::getCategoryList($parent_id);
        } else {
            return view('admins.goods.categoryTemplate', array('level' => $level, 'categoryList' => CategoryService::getCategoryList($parent_id)));
        }
    }

    public function create()
    {
        $first_id       = $parent_id       = intval(request('parent_id', 0));
        $firstCategory  = CategoryService::getCategoryList();
        $secondCategory = null;
        if ($parent_id && !in_array($parent_id, array_column(json_decode($firstCategory), 'id'))) {
            //如果是三级分类，查询出二级
            $categoryInfo   = CategoryService::findById($parent_id);
            $secondCategory = CategoryService::getCategoryList($categoryInfo['parent_id']);
            $first_id       = $categoryInfo['parent_id']; //一级id
        }
        return view('admins.goods.editCategory', array('first_id' => $first_id, 'parent_id' => $parent_id, 'firstCategory' => $firstCategory, 'secondCategory' => $secondCategory));
    }

    public function store(CategoryRequest $request)
    {
        $request['parent_id'] = empty($request['parent_id']) ? $request['first_id'] : $request['parent_id'];
        $result               = CategoryService::existColumn($request['name'], $request['parent_id']);
        if ($result) {
            return response()->json($result);
        }
        $res = CategoryService::saveOrUpdate($request);

        if ($res) {
            return response()->json(array(
                'code'     => 200,
                'messages' => array('新增分类成功'),
                'url'      => '/admin/goods/category',
            ));
        } else {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('新增分类失败'),
                'url'      => '',
            ));
        }
    }

    public function edit()
    {
        $categoryInfo   = CategoryService::findById(request()->category);
        $firstCategory  = CategoryService::getCategoryList();
        $secondCategory = null;
        $first_id       = $categoryInfo['parent_id'];
        if ($categoryInfo['parent_id'] > 0 && !in_array($categoryInfo['parent_id'], array_column(json_decode($firstCategory), 'id'))) {
            //如果是三级分类，查询出二级
            $parentInfo     = CategoryService::findById($categoryInfo['parent_id']);
            $first_id       = $parentInfo['parent_id']; //一级id
            $secondCategory = CategoryService::getCategoryList($first_id);
        }
        return view('admins.goods.editCategory', array('first_id' => $first_id, 'parent_id' => $categoryInfo['parent_id'], 'categoryInfo' => $categoryInfo, 'firstCategory' => $firstCategory, 'secondCategory' => $secondCategory));
    }

    public function update(CategoryRequest $request, $id)
    {
        $request['parent_id'] = empty($request['parent_id']) ? $request['first_id'] : $request['parent_id'];
        $result               = CategoryService::existColumn($request['name'], $request['parent_id'], $id);
        if ($result) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('分类名称已存在'),
                'url'      => '',
            ));
        }

        if (CategoryService::saveOrUpdate($request, $id)) {
            return response()->json(array(
                'code'     => 200,
                'messages' => array('保存分类成功'),
                'url'      => '/admin/goods/category/' . $id . '/edit',
            ));
        } else {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('保存分类失败'),
                'url'      => '',
            ));
        }
    }

    public function destroy($id)
    {
        if ($id) {
            if (CategoryService::delete($id)) {
                return response()->json(array(
                    'code'     => 200,
                    'messages' => array('删除分类成功'),
                    'url'      => '/admin/goods/category',
                ));
            } else {
                return response()->json(array(
                    'code'     => 500,
                    'messages' => array('未找到该分类'),
                    'url'      => '/admin/goods/category',
                ));
            }
        } else {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('参数错误'),
                'url'      => '',
            ));
        }
    }

    //更新排序
    public function categorySort()
    {
        $id   = intval(request('id', 0));
        $sort = intval(request('sort', 99));
        if (CategoryService::categorySort($id, $sort)) {
            return response()->json(
                array(
                    'code'     => 200,
                    'messages' => array('更新成功'),
                    'url'      => '',
                )
            );
        } else {
            return response()->json(
                array(
                    'code'     => 500,
                    'messages' => array('更新失败'),
                    'url'      => '',
                )
            );
        }
    }

    public function findByParentId($id)
    {
        $categories = CategoryService::findByParentId($id);

        return response()->json(array(
            'code'     => 200,
            'messages' => array('查询成功'),
            'url'      => '',
            'datas'    => $categories,
        ));
    }
}
