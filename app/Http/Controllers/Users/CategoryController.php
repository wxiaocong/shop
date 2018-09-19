<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\GoodsService;
use App\Utils\Page;

class CategoryController extends Controller
{
    public function index()
    {
        $parent_id = trim(request('category_id', 0));
        //一级分类列表
        $data['firstCategoryList'] = CategoryService::getCategoryList();
        if (!empty($data['firstCategoryList']) && count($data['firstCategoryList']) > 0){
            //二级分类列表
            $data['parent_id'] = empty($data['parent_id']) ? $data['firstCategoryList'][0]->id : $data['parent_id'];
            $data['secondCategoryList'] = CategoryService::getCategoryList($data['parent_id']);
        }
        return view('users.category', $data);
    }
    
    public function getCategoryList() {
        $parent_id = intval(request('parent_id'));
        $data['secondCategoryList'] = CategoryService::getCategoryList($parent_id);
        return view('users.categoryData', $data);
    }
    
    //分类商品列表
    public function show($category_id) {
        //分类
        $data['categorys'] = CategoryService::getCategoryList($category_id);
        $data['pageSize'] = Page::PAGESIZE;
        return view('users.goods', $data);
    }
    
    //获取分类商品数据
    public function getGoodsList() {
        $param = array();
        $param['sort'] = intval(request('sort'));
        $param['sortType'] = request('sortType');
        $param['category_parent_id'] = intval(request('category_parent_id'));
        $param['category_id'] = intval(request('category_id'));
        $param['hasStock'] = intval(request('hasStock'));
        $param['search'] = request('searchKey');
        
        $curPage  = trimSpace(request('curPage', 1));
        $pageSize = trimSpace(request('pageSize', Page::PAGESIZE));
        $data['goodsList'] = GoodsService::findByPage($curPage, $pageSize, $param);
        return view('users.goodsData', $data);
    }
}
