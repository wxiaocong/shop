<?php

namespace App\Daoes;

use App\Daoes\BaseDao;
use App\Models\GoodsAttr;

class GoodsAttrDao extends BaseDao {
    /**
     * 更新删除时间
     * @param int $goodId
     * @return boolean
     */
    public static function updateDeletedAt($goodId) {
        GoodsAttr::where('goods_id', $goodId)->update(array('deleted_at' => date('Y-m-d H:i:s')));
        return true;
    }

    //获取商品属性
    public static function getAttrByGoods($goods_id) {
        return GoodsAttr::join('attributes as a','goods_attr.attr_ids','=','a.id')
            ->where(['goods_attr.goods_id'=>$goods_id, 'a.spec'=>0])
            ->select('a.name','goods_attr.values')
            ->get();
    }

    /**
     * 批量删除
     * @param  array   $ids
     *
     * @return boolean
     */
    public static function batchDelete($ids) {
        $count = GoodsAttr::destroy($ids);
        if ($count > 0) {
            return true;
        }
        return false;
    }
}
