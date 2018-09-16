<?php

namespace App\Daoes;

use App\Daoes\BaseDao;
use App\Models\Areas;

class AreasDao extends BaseDao
{
    /**
     * 根据parent_id查询area
     * @param  int $parent_id
     *
     * @return App\Models\Areas
     */
    public static function getList($parent_id = 0)
    {
        return Areas::where('parent_id', $parent_id)->orderBy('sort','asc')->get();
    }
    
    //获取所有地区
    public static function getAllAreas()
    {
        return Areas::orderBy('parent_id','asc')->get(['id','parent_id','area_name'])->toArray();
    }
    
    /**
     * 省市区转换(id->name)
     */
    public static function convertAreaIdToName($ids) {
        return Areas::whereIn('id', $ids)->pluck('area_name')->toArray();
    }
    
    /**
     * 更新地区
     * @param int $id
     * @param int $data
     */
    public static function updateArea($id, $data)
    {
        $areas = Areas::find($id);
        if (empty($data['area_name'])) {
            $areas->sort = $data['sort'];
        } else {
            $areas->area_name = $data['area_name'];
        }
        return $areas->save();
    }
    
    /**
     * 判断area_name-parent_id是否重复
     * @param  int $id
     *
     * @return boolean
     */
    public static function existColumn($area_name, $parent_id, $id = 0)
    {
        $builder = Areas::where(['area_name'=>$area_name, 'parent_id'=>$parent_id]);
        if ($id > 0) {
            $builder->where('id', '!=', $id);
        }
        $areas = $builder->get();
        
        if (isset($areas) && count($areas) > 0) {
            return true;
        }
        
        return false;
    }
    
    /**
     * 根据Id删除地区及其子地区
     * @param int $id
     *
     */
    public static  function deleteAllAreas($id) {
        $builder = Areas::where('id', $id);
        
        $builder = self::findLowerAreas($builder, $id);
        return $builder->delete();
    }
    
    //查询子地区
    protected static function findLowerAreas($builder, int $area_id) {
        $lowerAreas = Areas::where('parent_id', $area_id)->get(['id']);
        if (!empty($lowerAreas)) {
            foreach ($lowerAreas as $val) {
                $builder = self::findLowerAreas($builder, $val->id);
            }
            return $builder->orWhereIn('id', $lowerAreas);
        }
    }
}
