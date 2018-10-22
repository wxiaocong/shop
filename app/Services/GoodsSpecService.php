<?php

namespace App\Services;

use App\Daoes\GoodsDao;
use App\Daoes\GoodsSpecDao;

class GoodsSpecService
{
    /**
     * 分页查询
     * @param  int $curPage
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPage($curPage, $pageSize, $params = array())
    {
        return GoodsDao::findByPage($curPage, $pageSize, $params);
    }

    //获取商品sku
    public static function getSkuByGoods($goods_id)
    {
        return GoodsSpecDao::getSkuByGoods($goods_id);
    }

    /**
     * 查询
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params = array())
    {
        return GoodsSpecDao::findByParams($params);
    }

    /**
     * 根据Id查询商品
     * @param int $id
     *
     */
    public static function findById($id, $params = array())
    {
        return GoodsSpecDao::findById($id, $params);
    }

    /**
     * 根据Ids查询商品
     * @param array $ids
     * @param boolean $isQueryAll (是否查询所有数据,包含软删除的数据)
     *
     */
    public static function findByIds($ids, $isQueryAll = false)
    {
        return GoodsSpecDao::findByIds($ids, $isQueryAll);
    }
    
    /**
     * 支付完成更新库存
     * @param  int $order_id
     */
    public static function updateGoodsSpecNum($order_id)
    {
        return GoodsSpecDao::updateGoodsSpecNum($order_id);
    }

    /**
     * 首页商品
     * @return [type] [description]
     */
    public static function getIndexGood() {
        $goods = array();
        $res = array_map('get_object_vars', GoodsSpecDao::getIndexGood());
        if (!empty($res)) {
            foreach ($res as $key => $value) {
                $goods[$value['first_id']][] = $value;
            }
        }
        \Log::error($goods);
        return $goods;
    }
}
