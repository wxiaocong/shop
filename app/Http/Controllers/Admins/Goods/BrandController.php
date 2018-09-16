<?php

namespace App\Http\Controllers\Admins\Goods;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Goods\BrandRequest;
use App\Services\BrandService;
use App\Utils\Page;

class BrandController extends Controller
{
    public function index()
    {
        $curPage  = trimSpace(request('curPage', 1));
        $pageSize = trimSpace(request('pageSize', Page::PAGESIZE));
        $params['search'] = trimSpace(request('search', ''));

        $page = BrandService::findByPage($curPage, $pageSize,$params);
        return view('admins.goods.brandList')->with('page', $page)->with('search', $params['search']);
    }
    
    
    public function create()
    {
        return view('admins.goods.editBrand');
    }
    
    public function store(BrandRequest $request)
    {
        $result = BrandService::existColumn('short_name', $request['short_name']);
        if ($result) {
            return response()->json($result);
        }
        $res = BrandService::saveOrUpdate($request);
        if ($res) {
            return response()->json(array(
                'code'     => 200,
                'messages' => array('新增品牌成功'),
                'url'      => '/admin/goods/brand',
            ));
        } else {
            return response()->json(array(
                    'code'     => 500,
                    'messages' => array('新增品牌失败'),
                    'url'      => '',
            ));
        }
    }
    
    public function edit()
    {
        return view('admins.goods.editBrand', ['brandInfo' => BrandService::findById(request()->brand)]);
    }
    
    public function update(BrandRequest $request, $id)
    {
        if ($request['state'] == 1) {
            $result = BrandService::existColumn('short_name', $request['short_name'], $id);
            if ($result) {
                return response()->json(array(
                    'code'     => 500,
                    'messages' => array('品牌简称已存在'),
                    'url'      => '',
                ));
            }
        }

        if( BrandService::saveOrUpdate($request, $id) ) {
            return response()->json(array(
                'code'     => 200,
                'messages' => array('保存品牌成功'),
                'url'      => '/admin/goods/brand/'.$id.'/edit',
            ));
        } else {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('保存品牌失败'),
                'url'      => '',
            ));
        }
    }
    
    public function destroy($id)
    {
        if ($id) {
            if(BrandService::findById($id)->delete()) {
                return response()->json(array(
                    'code'     => 200,
                    'messages' => array('删除品牌成功'),
                    'url'      => '/admin/goods/brand',
                ));
            } else {
                return response()->json(array(
                    'code'     => 500,
                    'messages' => array('未找到该品牌'),
                    'url'      => '/admin/goods/brand',
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
    public function brandSort()
    {
        $id = intval(request('id', 0));
        $sort = intval(request('sort', 99));
        if(BrandService::brandSort($id, $sort)) {
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
}
