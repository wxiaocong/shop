<?php

namespace App\Services\Users;

use App\Daoes\Users\ExpressAddressDao;
use App\Models\Users\ExpressAddress;
use App\Services\AreasService;

class ExpressAddressService
{
    /**
     * 保存更新expressAddress
     * @param unknown $request
     * @param unknown $id
     */
    public static function saveOrUpdate($request, $id = 0)
    {
        if ($id) {
            return ExpressAddressDao::findById($id)->update(array_filter($request->all()));
        } else {
            $expressAddress = new ExpressAddress();
            
            $expressAddress->user_id        = session('user')->id;
            $expressAddress->to_user_name   = $request['to_user_name'];
            $expressAddress->mobile         = $request['mobile'];
            $expressAddress->province         = $request['province'];
            $expressAddress->city         = $request['city'];
            $expressAddress->area         = $request['area'];
            $expressAddress->address        = $request['address'];
            return ExpressAddressDao::save($expressAddress);
        }
    }
    
    /**
     * 查询收货地址
     *
     * @return App\Models\Category
     */
    public static function getList($user_id = 0)
    {
        return ExpressAddressDao::getList($user_id);
    }
    
    /**
     * 获取默认地址
     * @param int $id
     *
     */
    public static function getDefault()
    {
        $addressInfo = ExpressAddressDao::getDefault();
        if (!empty($addressInfo)) {
            //组合省市区
            $addressInfo['region'] = AreasService::convertAreaIdToName([$addressInfo->province, $addressInfo->city, $addressInfo->area]);
        }
        return $addressInfo;
    }
    
    /**
     * 设置默认地址
     * @param int $id
     *
     */
    public static function setDefault($id)
    {
        return ExpressAddressDao::setDefault($id);
    }
    
    /**
     * 删除收货地址
     *
     * @return App\Models\Category
     */
    public static function destroy($id)
    {
        if ($id) {
            if(ExpressAddressDao::destroy($id)) {
                return response()->json(array(
                        'code'     => 200,
                        'messages' => array('删除收货地址'),
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
    
    /**
     * 根据Id查询地址
     * @param int $id
     *
     */
    public static function findById($id)
    {
        $addressInfo = ExpressAddressDao::findById($id);
        if (!empty($addressInfo)) {
            //组合省市区
            $addressInfo['region'] = AreasService::convertAreaIdToName([$addressInfo->province, $addressInfo->city, $addressInfo->area]);
        }
        return $addressInfo;
    }
}
