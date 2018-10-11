<?php

namespace App\Http\Controllers\Admins\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\System\AreasRequest;
use App\Services\AreasService;
use Illuminate\Support\Facades\Redis;

class AreasController extends Controller
{
    public function index()
    {
        $areasTree = AreasService::getAreasTree(0);
        return view('admins.system.areasList', ['areasTree' => json_decode($areasTree)]);
    }
    
    //获取地区列表(一级)
    public function getArea() 
    {
        $parent_id = intval(request('area_id', 0));
        $level = intval(request('level', 1));
        return view('admins.system.areasTemplate', ['level' => $level, 'areasTree' => AreasService::getAreasTree($parent_id)]);
    }

    //获取下级
    public function ajaxGetArea($parent_id)
    {
        return json_encode(AreasService::getAreasTree($parent_id));
    }
    
    public function store(AreasRequest $request)
    {
        $result = AreasService::existColumn($request['area_name'], $request['parent_id']);
        if ($result) {
            return response()->json($result);
        }
        $res = AreasService::saveAreas($request);
        if ($res) {
            AreasService::updateAddressRedis();
            return response()->json(array(
                'code'     => 200,
                'messages' => array('新增地区成功'),
                'url'      => '/admin/system/areas',
            ));
        } else {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('新增地区失败'),
                'url'      => '',
            ));
        }
    }
    
    //更新
    public function update(int $id)
    {
        $area_name = request('area_name', '');
        if (empty($area_name)) {
            $data['sort'] = intval(request('sort', 99));
        } else {
            $data['area_name'] = $area_name;
        }
        if(AreasService::updateArea($id, $data)) {
            if( ! empty($area_name) ){
                AreasService::updateAddressRedis();
            }
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
    
    public function destroy($id)
    {
        if ($id) {
            if(AreasService::delete($id)) {
                AreasService::updateAddressRedis();
                return response()->json(array(
                    'code'     => 200,
                    'messages' => array('删除地区成功'),
                    'url'      => '/admin/system/areas',
                ));
            } else {
                return response()->json(array(
                    'code'     => 500,
                    'messages' => array('未找到该地区'),
                    'url'      => '/admin/system/areas',
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
}
