<?php

namespace App\Services;

use App\Daoes\AreasDao;
use App\Models\Areas;
use Illuminate\Support\Facades\Redis;

class AreasService
{

    /**
     * 根据parent_id查询areasTree
     * 
     * @return App\Models\Areas
     */
    public static function getAreasTree($parent_id = 0)
    {
        if( $parent_id == 0 && ! Redis::exists('areasTree') ){
            if ( ! Redis::exists('areasTree') ) {
                $areasTree = self::updateAddressRedis(2);
            }
            return $areasTree ?? Redis::get('areasTree');
        }
        return AreasDao::getList($parent_id);
    }
    
    /**
     * 省市区转换(id->name)
     */
    public static function convertAreaIdToName($ids) {
        return implode(' ',AreasDao::convertAreaIdToName($ids));
    }
    
    /**
     *  获取所有地区
     */
    public static function getAllAreas()
    {
        if ( ! Redis::exists('addressTree') ) {
            $addressTree = self::updateAddressRedis(1);
        }
        return $addressTree ?? Redis::get('addressTree');
    }
    
    /**
     * 生成地区缓存
     * $type 0更新所有 1更新前端2后端
     */
    public static function updateAddressRedis($type = 0)
    {
        if ($type == 0 || $type == 1) {
            $addressTree = getTree(AreasDao::getAllAreas());
            Redis::set('addressTree', json_encode($addressTree), 'EX', 3600*24*30);
            return $addressTree;
        }
        if ($type == 0 || $type == 2) {
            $areaTree = AreasDao::getList(0);
            Redis::set('areasTree', $areaTree, 'EX', 3600*24*30);
            return $areaTree;
        }
    }
    
    /**
     * 更新地区
     * @param int $id
     * @param int $data
     */
    public static function updateArea($id, $data)
    {
        return AreasDao::updateArea($id, $data);
    }
    
    /**
     * 判断area_name-parent_id是否重复
     * @param  int $id
     *
     * @return boolean
     */
    public static function existColumn($area_name, $parent_id, $id = 0)
    {
        $result = AreasDao::existColumn($area_name, $parent_id, $id);
        if ($result) {
            return array(
                'code'     => 500,
                'messages' => array('地区名称已存在'),
                'url'      => '',
            );
        }
    }
    
    /**
     * 保存area
     * @param unknown $request
     * @param unknown $id
     */
    public static function saveAreas($request)
    {
        $areas = new Areas();
        
        $areas->area_name    = $request['area_name'];
        $areas->parent_id    = intval($request['parent_id']);
        return AreasDao::save($areas);
    }
    
    /**
     * 根据Id删除地区及其子地区
     * @param int $id
     *
     */
    public static function delete($id) {
        return AreasDao::deleteAllAreas($id);
    }
}
