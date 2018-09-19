<?php

namespace App\Daoes;

use App\Daoes\BaseDao;
use App\Daoes\PromotionDao;
use App\Models\Goods;
use App\Models\GoodsSpec;
use App\Utils\Page;
use DB;

class GoodsDao extends BaseDao
{
    /**
     * 查询分类商品列表
     *
     * @return App\Models\Goods
     */
    public static function getList($category_id = 0)
    {
        $builder = Goods::where(array('category_id' => $category_id, 'state' => 0))->orderBy('sort', 'asc');
        return $builder->get();
    }

    /**
     * 分页查询商品
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     * 2018/8/9 商品全部挂在good_spec下面
     */
    public static function findByPage($curPage, $pageSize, $params)
    {
        $builder = GoodsSpec::where('goods_spec.state', 0)->offset($pageSize * ($curPage - 1))->limit($pageSize);

        if (array_key_exists('category_parent_id', $params) && $params['category_parent_id'] > 0) {
            $builder->where('goods_spec.category_parent_id', $params['category_parent_id']);
        }
        if (array_key_exists('category_id', $params) && $params['category_id'] > 0) {
            $builder->where('goods_spec.category_id', $params['category_id']);
        }
        if (array_key_exists('hasStock', $params) && $params['hasStock'] > 0) {
            $params['hasStock'] == 1 ? $builder->where('goods_spec.number', '>', '0') : $builder->where('goods_spec.number', 0);
        }
        if (array_key_exists('state', $params) && $params['state'] != '') {
            $builder->where('state', $params['state']);
        }
        if (array_key_exists('search', $params) && $params['search'] != '') {
            $builder->where(function ($query) use ($params) {
                $query->orWhere('goods_spec.name', 'like', '%' . $params['search'] . '%');
            });
        }
        if (array_key_exists('sort', $params)) {
            switch ($params['sort']) {
                case 1: //销量
                    if (empty($params['hasStock'])) {
                        //没有库存筛选时有库存优先
                        $builder->orderByRaw(DB::raw("IF(goods_spec.number>0,1,0) desc"));
                    }
                    $builder->orderBy('goods_spec.sale_num', $params['sortType']);
                    break;
                case 3: //价格
                    $builder->orderBy('goods_spec.sell_price', $params['sortType']);
                    break;
                default: //人气
                    if (empty($params['hasStock'])) {
                        $builder->orderByRaw(DB::raw("IF(goods_spec.number>0,1,0) desc"));
                    }
                    $builder->orderBy('goods_spec.click', 'desc');
            }

        } else {
            $builder->orderByRaw(DB::raw("IF(goods_spec.number>0,1,0) desc"))->orderBy('goods_spec.click', 'desc');
        }
        return $builder->get();
    }

    /**
     * 根据Id查询商品
     * @param int $id
     *
     * @return App\Models\Goods
     */
    public static function findById($id)
    {
        return Goods::with(array('brand' => function ($query) {
            $query->select('id', 'short_name');
        }))->find($id);
    }

    /**
     * 分页查询商品
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPageAndParams($curPage, $pageSize, $params)
    {
        $builder = Goods::leftJoin('goods_spec', 'goods.id', '=', 'goods_spec.goods_id')->select('goods.*');

        if (array_key_exists('categoryParentId', $params) && $params['categoryParentId'] != 0) {
            $builder->where('goods.category_parent_id', $params['categoryParentId']);
        }
        if (array_key_exists('search', $params) && $params['search'] != '') {
            $builder->where(function ($query) use ($params) {
                $query->where('goods.name', 'like', '%' . $params['search'] . '%')
                    ->orWhere('goods_spec.name', 'like', '%' . $params['search'] . '%');
            });
        }
        if (array_key_exists('sort', $params)) {
            foreach ($params['sort'] as $key => $value) {
                $builder->orderBy($key, $value);
            }
        } else {
            $builder->orderBy('goods.category_id', 'desc')->orderBy('goods.sort', 'asc')->orderBy('goods.created_at', 'desc');
        }

        $builder->groupBy('goods.id');
        return new Page($builder->paginate($pageSize, array('*'), 'page', $curPage));
    }

    /**
     * 查询
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params)
    {
        $builder = Goods::select();

        if (array_key_exists('ids', $params)) {
            $builder->whereIn('id', $params['ids']);
        }

        return $builder->get();
    }

    /**
     * 保存sku
     * @param  App\Models\Goods $good
     * @param  array(App\Models\GoodsSpec) $goodsSpecs
     *
     * @return array
     */
    public static function saveManyGoodsSpecs($good, $goodsSpecs)
    {
        return $good->goodsSpecs()->saveMany($goodsSpecs);
    }

    /**
     * 保存属性
     * @param  App\Models\Goods $good
     * @param  array(App\Models\GoodsAttr) $goodsAttrs
     *
     * @return array
     */
    public static function saveManyGoodsAttrs($good, $goodsAttrs)
    {
        return $good->goodsAttrs()->saveMany($goodsAttrs);
    }

    /**
     * 批量删除
     * @param  array   $ids
     *
     * @return boolean
     */
    public static function batchDelete($ids)
    {
        $count = Goods::destroy($ids);
        if ($count > 0) {
            return true;
        }

        return false;
    }
}
