<?php

namespace App\Services;

use App\Daoes\GoodsDao;

class GoodsService
{

    /**
     * 根据category_id查询goods
     * 
     * @return App\Models\Goods
     */
    public static function getList($category_id)
    {
        return GoodsDao::getList($category_id);
    }
    
    /**
     * 分页查询商品
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPage($curPage, $pageSize, $params = array())
    {
        return GoodsDao::findByPage($curPage, $pageSize, $params);
    }
    
    /**
     * 根据Id查询商品
     * @param int $id
     *
     */
    public static function findById($id)
    {
        return GoodsDao::findById($id);
    }
}
