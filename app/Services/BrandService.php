<?php

namespace App\Services;

use App\Daoes\BrandDao;
use App\Models\Brand;

class BrandService
{
    /**
     * 判断name-parent_id是否重复
     * @param  int $id
     *
     * @return boolean
     */
    public static function existColumn($key, $name, $id = 0)
    {
        $result = BrandDao::existColumn($key, $name, $id);
        if ($result) {
            return array(
                'code'     => 500,
                'messages' => array(' 品牌简称已存在'),
                'url'      => '',
            );
        }
    }
    
    /**
     * 保存更新category
     * @param unknown $request
     * @param unknown $id
     */
    public static function saveOrUpdate($request, $id = 0)
    {
        if ($id) {
            return self::findById($id)->update($request->all());
        } else {
            $brand = new Brand();
            
            $brand->logo_cname   = $request['logo_cname'];
            $brand->logo_ename   = $request['logo_ename'];
            $brand->short_name   = $request['short_name'];
            $brand->mini_desc    = $request['mini_desc'];
            $brand->short_desc   = $request['short_desc'];
            $brand->detail_desc  = $request['detail_desc'];
            $brand->sort         = $request['sort'];
            $brand->state        = $request['state'];
            return BrandDao::save($brand);
        }
    }
    
    /**
     * 更新排序
     * @param int $id
     * @param int $sort
     */
    public static function brandSort($id, $sort)
    {
        return BrandDao::brandSort($id, $sort);
    }
    
    /**
     * 根据Id查询分类
     * @param int $id
     *
     */
    public static function findById($id)
    {
        return BrandDao::findById($id);
    }
    
    
    /**
     * 分页查询分类
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPage($curPage, $pageSize, $params = array())
    {
        return BrandDao::findByPage($curPage, $pageSize, $params);
    }

    /**
     * 查询分类
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params = array())
    {
        return BrandDao::findByParams($params);
    }
}
