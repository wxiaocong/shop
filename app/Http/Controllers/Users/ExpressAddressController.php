<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\ExpressAddressRequest;
use App\Services\AreasService;
use App\Services\Users\ExpressAddressService;
// use Session;

class ExpressAddressController extends Controller
{
    public function index()
    {
        Session::forget('expressReferer');
        return view('users.address', ['addressList' => ExpressAddressService::getList(session('user')->id)]);
    }
    
    public function create()
    {
        //记录来源
        session(array('expressReferer'=>url()->previous()));
        return view('users.editAddress');
    }
    
    public function store(ExpressAddressRequest $request)
    {
        $res = ExpressAddressService::saveOrUpdate($request);
        $url = session('expressReferer') ?? '/address';
        if ($res) {
            return response()->json(array(
                'code'     => 200,
                'messages' => array('新增收货地址成功'),
                'url'      => $url,
            ));
        } else {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('新增收货地址失败'),
                'url'      => '',
            ));
        }
    }
    
    public function edit()
    {
        //记录来源
        session(array('expressReferer'=>url()->previous()));
        $addressInfo = ExpressAddressService::findById(request()->address);
        return view('users.editAddress', ['addressInfo' => $addressInfo]);
    }
    
    public function update(ExpressAddressRequest $request, $id)
    {
        if( ExpressAddressService::saveOrUpdate($request, $id) ) {
            $url = session('expressReferer') ?? '/address';
            return response()->json(array(
                'code'     => 200,
                'messages' => array('保存收货地址成功'),
                'url'      => $url,
            ));
        } else {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('保存收货地址失败'),
                'url'      => '',
            ));
        }
    }
    
    public function destroy($id)
    {
        if ($id) {
            if(ExpressAddressService::destroy($id)) {
                return response()->json(array(
                    'code'     => 200,
                    'messages' => array('删除收货地址成功'),
                    'url'      => '/address',
                ));
            } else {
                return response()->json(array(
                    'code'     => 500,
                    'messages' => array('未找到该收货地址'),
                    'url'      => '/address',
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
    
    //设置默认地址
    public function setDefault()
    {
        $id = intval(request('id'));
        if ($id) {
            if( ExpressAddressService::setDefault($id) ) {
                return response()->json(
                    array(
                        'code'     => 200,
                        'messages' => array('更新成功'),
                        'url'      => '/address',
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
        } else {
            return response()->json(
                array(
                    'code'     => 500,
                    'messages' => array('参数错误'),
                    'url'      => '',
                )
            );
        }
    }
    
    //获取收货地址模板
    public function getExpressAddress()
    {
        echo view('users.purchaseExpressData', ['addressList'=>ExpressAddressService::getList(session('user')->id)]);
    }
    
    //获取所有收货地址
    public function getAllAreas() {
        return AreasService::getAllAreas();
    }
}
