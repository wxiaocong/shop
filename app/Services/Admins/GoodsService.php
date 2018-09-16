<?php

namespace App\Services\Admins;

use App\Daoes\GoodsAttrDao;
use App\Daoes\GoodsDao;
use App\Daoes\GoodsSpecDao;
use App\Models\Goods;
use App\Models\GoodsAttr;
use App\Models\GoodsSpec;
use App\Services\CategoryService;
use App\Services\GoodsSpecService;
use DB;
use Illuminate\Http\Request;

class GoodsService
{
    /**
     * 根据ID查询
     */
    public static function findById($id)
    {
        return GoodsDao::findById($id);
    }

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

    /**
     * 分页查询商品
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPageAndParams($curPage, $pageSize, $params = array())
    {
        return GoodsDao::findByPageAndParams($curPage, $pageSize, $params);
    }

    /**
     * @param  array $ids
     *
     * @return boolean
     */
    public static function destroy($ids)
    {
        $flag = false;

        DB::beginTransaction();

        try {
            $goods = GoodsDao::findByParams(array('ids' => $ids));
            if (isset($goods) && count($goods) > 0) {
                foreach ($goods as $good) {
                    GoodsAttrDao::batchDelete($good->goodsAttrs->pluck('id')->all());
                    GoodsSpecDao::batchDelete($good->goodsSpecs->pluck('id')->all());
                }

                $flag = GoodsDao::batchDelete($goods->pluck('id')->all());
                if (!$flag) {
                    DB::rollback();

                    return $flag;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }

        return $flag;
    }

    /**
     * update
     * @param App\Models\Goods $good
     *
     * @return array
     */
    public static function update($good)
    {
        $good->updated_at = date('Y-m-d H:i:s');
        return GoodsDao::save($good, session('adminUser')->id);
    }

    /**
     * save
     * @param  Request $request
     *
     * @return array
     */
    public static function save(Request $request)
    {
        $modelTypeId   = $request->input('modelTypeId', 0);
        $goodSkuParams = $request->input('goodSku', array());
        $goodParams    = $request->input('good', array());

        DB::beginTransaction();

        try {
            //生成商品
            $good = self::generateGood($request, $modelTypeId);
            $good = GoodsDao::save($good, session('adminUser')->id);
            if (!$good) {
                DB::rollback();

                return array(
                    'code'     => 500,
                    'messages' => array('保存商品失败'),
                    'url'      => '',
                );
            }

            $totalNum = 0;
            //生成sku数组
            $goodSpecs = self::generateGoodSkus($request, $good, $goodSkuParams, $totalNum);
            if (count($goodSpecs) > 0) {
                GoodsDao::saveManyGoodsSpecs($good, $goodSpecs);
            }
            //生成商品属性数组
            $goodAttrs = self::generateGoodAttrs($request, $good, $goodParams);
            if (count($goodAttrs) > 0) {
                GoodsDao::saveManyGoodsAttrs($good, $goodAttrs);
            }

            //更新总数量
            $good->total_num = $totalNum;
            GoodsDao::save($good, session('adminUser')->id);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return array(
                'code'     => 500,
                'messages' => array($e->getMessage()),
                'url'      => '',
            );
        }

        return array(
            'code'     => 200,
            'messages' => array('保存商品成功'),
            'url'      => '',
        );
    }

    /**
     * update
     * @param  Request $request
     * @param  int $id
     *
     * @return array
     */
    public static function updateGood(Request $request, $id)
    {
        $modelTypeId   = $request->input('modelTypeId', 0);
        $goodSkuParams = $request->input('goodSku', array());
        $goodParams    = $request->input('good', array());

        DB::beginTransaction();

        try {
            $good = GoodsService::findById($id);
            if (!$good) {
                DB::rollback();

                return array(
                    'code'     => 500,
                    'messages' => array('商品不存在'),
                    'url'      => '',
                );
            }

            //生成商品
            $good = self::generateGood($request, $modelTypeId, $good);
            $good = GoodsDao::save($good, session('adminUser')->id);
            if (!$good) {
                DB::rollback();

                return array(
                    'code'     => 500,
                    'messages' => array('保存商品失败'),
                    'url'      => '',
                );
            }

            if ($id > 0) {
                //删除旧有属性
                GoodsAttrDao::updateDeletedAt($id);
            }

            $totalNum = 0;
            //生成sku数组
            $goodSpecs = self::generateGoodSkus($request, $good, $goodSkuParams, $totalNum);
            if (count($goodSpecs) > 0) {
                GoodsDao::saveManyGoodsSpecs($good, $goodSpecs);
            }
            //生成商品属性数组
            $goodAttrs = self::generateGoodAttrs($request, $good, $goodParams);
            if (count($goodAttrs) > 0) {
                GoodsDao::saveManyGoodsAttrs($good, $goodAttrs);
            }

            //更新总数量
            $good->total_num = $totalNum;
            GoodsDao::save($good, session('adminUser')->id);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return array(
                'code'     => 500,
                'messages' => array($e->getMessage()),
                'url'      => '',
            );
        }

        return array(
            'code'     => 200,
            'messages' => array('保存商品成功'),
            'url'      => '',
        );
    }

    /**
     * 上/下架
     * @param  App\Models\Goods $good
     *
     * @return boolean
     */
    public static function updateState($good)
    {
        $currentDate = date('Y-m-d H:i:s');
        if (count($good->goodsSpecs) > 0 && $good->state == config('statuses.good.state.soldOut.code')) {
            foreach ($good->goodsSpecs as &$goodsSpec) {
                $goodsSpec->state      = $good->state;
                $goodsSpec->updated_at = $currentDate;
                GoodsSpecDao::save($goodsSpec, session('adminUser')->id);
            }
        }
        $good->updated_at = $currentDate;
        $good             = GoodsDao::save($good, session('adminUser')->id);
        if (!$good) {
            return false;
        }

        return true;
    }

    /**
     * 更新商品库存
     * @param  App\Models\Goods $good
     * @param  array $totalNums
     *
     * @return boolean
     */
    public static function updateGoodNum($good, $totalNums)
    {
        $currentDate = date('Y-m-d H:i:s');
        $number      = 0;
        foreach ($totalNums as $key => $value) {
            $number += intval($value);
            $goodsSpec             = GoodsSpecDao::findById($key, array('states' => array(config('statuses.good.state.soldOut.code'), config('statuses.good.state.putaway.code'))));
            $goodsSpec->number     = intval($value);
            $goodsSpec->updated_at = $currentDate;
            GoodsSpecDao::save($goodsSpec, session('adminUser')->id);
        }

        $good->total_num = $number;
        $good            = GoodsDao::save($good, session('adminUser')->id);
        if (!$good) {
            return false;
        }

        return true;
    }

    /**
     * 更新商品价格
     * @param  App\Models\Goods $good
     * @param  array $prices
     *
     * @return boolean
     */
    public static function updateGoodPrice($good, $prices)
    {
        $currentDate = date('Y-m-d H:i:s');
        foreach ($prices as $key => $value) {
            $goodsSpec                  = GoodsSpecDao::findById($key, array('states' => array(config('statuses.good.state.soldOut.code'), config('statuses.good.state.putaway.code'))));
            $goodsSpec->sell_price      = intval($value['sellPrice'] * 100);
            $goodsSpec->member_price    = intval($value['memberPrice'] * 100);
            $goodsSpec->wholesale_price = intval($value['wholesalePrice'] * 100);
            $goodsSpec->cost_price      = intval($value['costPrice'] * 100);
            $goodsSpec->updated_at      = $currentDate;
            GoodsSpecDao::save($goodsSpec, session('adminUser')->id);
        }

        return true;
    }

    /**
     * 生成属性数组
     * @param  Request $request
     * @param  int  $goodId
     * @param  array $goodParams
     *
     * @return array
     */
    private static function generateGoodAttrs(Request $request, $goodId, $goodParams)
    {
        //商品属性
        $goodAttrs = array();

        if (count($goodParams) > 0 && isset($goodParams['attribute']) && count($goodParams['attribute']) > 0) {
            $attributes = $goodParams['attribute'];
        } else {
            $attributes = array();
        }
        foreach ($attributes as $key => $value) {
            $attrValues = array();
            foreach ($value as $attribute) {
                $attrValues[] = $attribute;
            }

            $goodsAttr           = new GoodsAttr();
            $goodsAttr->goods_id = $goodId;
            $goodsAttr->attr_ids = $key;
            $goodsAttr->values   = implode(',', $attrValues);
            $goodAttrs[]         = $goodsAttr;
        }

        return $goodAttrs;
    }

    /**
     * 生成商品
     * @param  Request $request
     * @param  [int] $modelTypeId
     * @param  App\Models\Goods $good
     *
     * @return App\Models\Goods
     */
    private static function generateGood(Request $request, $modelTypeId, $good = null)
    {
        if ($good == null) {
            $good = new Goods();
        } else {
            $good->updated_at = date('Y-m-d H:i:s');
        }

        $categoryId = intVal($request->input('goodCategoryId', config('statuses.zeroAndOne.zero.code')));
        $category   = CategoryService::findById($categoryId);
        if ($category->parentCategory->parent_id == 0) {
            $good->category_parent_id = $categoryId;
        } else {
            $good->category_parent_id = $category->parent_id;
        }

        $good->category_id     = $categoryId;
        $good->keywords        = trimSpace($request->input('keywords', ''));
        $good->description     = trimSpace($request->input('description', ''));
        $good->content         = trimSpace($request->input('content', ''));
        $good->name            = trimSpace($request->input('name', ''));
        $good->category_id     = intVal(trimSpace($request->input('goodCategoryId', config('statuses.zeroAndOne.zero.code'))));
        $good->brand_id        = intVal(trimSpace($request->input('brandId', config('statuses.zeroAndOne.zero.code'))));
        $good->merchant_id     = intVal(trimSpace($request->input('merchantId', config('statuses.zeroAndOne.zero.code'))));
        $good->goods_type      = intVal(trimSpace($request->input('modelTypeId', config('statuses.zeroAndOne.zero.code'))));
        $good->virtual_type    = trimSpace($request->input('virtualType', config('statuses.good.virtualType.entity.code')));
        $good->bar_code        = trimSpace($request->input('barCode', ''));
        $good->cust_partno     = trimSpace($request->input('custNo', ''));
        $good->unit            = trimSpace($request->input('unit', ''));
        $good->total_num       = intVal(trimSpace($request->input('totalNum', config('statuses.zeroAndOne.zero.code'))));
        $good->warning_num     = intVal(trimSpace($request->input('warningNum', config('statuses.zeroAndOne.zero.code'))));
        $good->weight          = intVal(trimSpace($request->input('weight', config('statuses.zeroAndOne.zero.code'))));
        $good->sell_price      = intval(trimSpace($request->input('sellPrice', config('statuses.zeroAndOne.zero.code'))) * 100);
        $good->member_price    = intval(trimSpace($request->input('memberPrice', config('statuses.zeroAndOne.zero.code'))) * 100);
        $good->wholesale_price = intval(trimSpace($request->input('wholesalePrice', config('statuses.zeroAndOne.zero.code'))) * 100);
        $good->cost_price      = intval(trimSpace($request->input('costPrice', config('statuses.zeroAndOne.zero.code'))) * 100);
        $good->state           = trimSpace($request->input('state', config('statuses.good.state.putaway.code')));
        $good->hot             = trimSpace($request->input('hot', config('statuses.zeroAndOne.zero.code')));
        $good->new             = trimSpace($request->input('new', config('statuses.zeroAndOne.zero.code')));
        $good->best            = trimSpace($request->input('best', config('statuses.zeroAndOne.zero.code')));
        $good->recommend       = trimSpace($request->input('recommend', config('statuses.zeroAndOne.zero.code')));
        $good->freeshipping    = trimSpace($request->input('freeshipping', config('statuses.zeroAndOne.zero.code')));
        $good->sort            = intVal(trimSpace($request->input('sort', 99)));

        return $good;
    }

    /**
     * 生成sku数组
     * @param  Request $request
     * @param  App\Models\Goods  $good
     * @param  array $goodSkuParams
     * @param  int $totalNum
     *
     * @return array
     */
    private static function generateGoodSkus(Request $request, $good, $goodSkuParams, &$totalNum)
    {
        $goodSkus    = array();
        $oldSepcIds  = array();
        $currentDate = date('Y-m-d H:i:s');

        if (count($goodSkuParams) > 0 && isset($goodSkuParams['storeNum']) && count($goodSkuParams['storeNum']) > 0) {
            $count = count($goodSkuParams['storeNum']);
        } else {
            $count = 0;
        }
        for ($i = 0; $i < $count; $i++) {
            $totalNum += intVal($goodSkuParams['storeNum'][$i]);

            //已存在的spec,更改属性值
            if (isset($goodSkuParams['id']) && $goodSkuParams['id'][$i] > 0) {
                $oldSepcIds[]          = $goodSkuParams['id'][$i];
                $goodsSpec             = GoodsSpecService::findById($goodSkuParams['id'][$i], array('states' => array(config('statuses.good.state.soldOut.code'), config('statuses.good.state.putaway.code'))));
                $goodsSpec->updated_at = $currentDate;
                $goodSkus[]            = self::setGoodSkuValues($goodsSpec, $good, $goodSkuParams, $i, $goodsSpec->click, $goodsSpec->sale_num, $goodsSpec->jiyou_good_id);
            } else {
                //新的spec，设置属性值
                $goodsSpec  = new GoodsSpec();
                $goodSkus[] = self::setGoodSkuValues($goodsSpec, $good, $goodSkuParams, $i, 0, 0, 0);
            }
        }

        //需删除的spec,设置删除时间
        if (count($oldSepcIds) > 0) {
            $delGoodsSpecs = GoodsSpecService::findByParams(array('goodsId' => $good->id, 'notIn' => array('id' => $oldSepcIds)));
            foreach ($delGoodsSpecs as &$delGoodsSpec) {
                $delGoodsSpec->deleted_at = $currentDate;
                $goodSkus[]               = $delGoodsSpec;
            }
        }

        return $goodSkus;
    }

    /**
     *  设置sku属性值
     *  @param  App\Models\GoodsSpec $goodsSpec
     *  @param  App\Models\Goods $good
     *  @param  array $goodSkuParams
     *  @param  int $index
     *  @param  int $click
     *  @param  int $saleNum
     *  @param  string $jiyouId
     *
     * @return App\Models\GoodsSpec
     */
    private static function setGoodSkuValues($goodsSpec, $good, $goodSkuParams, $index, $click, $saleNum, $jiyouId)
    {
        $attrIds    = '';
        $attrValues = '';
        if (isset($goodSkuParams['specIds']) && isset($goodSkuParams['specValues'])) {
            $attrIds    = $goodSkuParams['specIds'][$index];
            $attrValues = ($goodSkuParams['specValues'][$index] == '0') ? '' : $goodSkuParams['specValues'][$index];
            $value      = ($attrValues == null || $attrValues == '') ? '0' : $attrValues;
            if (isset($goodSkuParams['pic']) && isset($goodSkuParams['pic'][$value])) {
                $goodsSpec->img  = $goodSkuParams['pic'][$value][0];
                $goodsSpec->imgs = json_encode($goodSkuParams['pic'][$value]);
            } else {
                $goodsSpec->img  = '';
                $goodsSpec->imgs = json_encode(array());
            }
        } else {
            $goodsSpec->img  = '';
            $goodsSpec->imgs = json_encode(array());
        }

        $goodsSpec->goods_id           = $good->id;
        $goodsSpec->category_id        = $good->category_id;
        $goodsSpec->category_parent_id = $good->category_parent_id;
        $goodsSpec->brand_id           = $good->brand_id;
        $goodsSpec->name               = trimSpace((isset($goodSkuParams['name'][$index]) && $goodSkuParams['name'][$index] != '') ?
            $goodSkuParams['name'][$index] : $good->name . ' ' . implode(' ', explode(',', $attrValues)));
        $goodsSpec->attr_ids        = $attrIds;
        $goodsSpec->values          = $attrValues;
        $goodsSpec->number          = intVal($goodSkuParams['storeNum'][$index]);
        $goodsSpec->warning_num     = intVal($goodSkuParams['warningNum'][$index]);
        $goodsSpec->sell_price      = intval($goodSkuParams['sellPrice'][$index] * 100);
        $goodsSpec->member_price    = intval($goodSkuParams['memberPrice'][$index] * 100);
        $goodsSpec->wholesale_price = intval($goodSkuParams['wholesalePrice'][$index] * 100);
        $goodsSpec->cost_price      = intval($goodSkuParams['costPrice'][$index] * 100);
        $goodsSpec->weight          = intval($goodSkuParams['weight'][$index]);
        $goodsSpec->bar_code        = trimSpace($goodSkuParams['barCode'][$index]);
        $goodsSpec->cust_partno     = trimSpace($goodSkuParams['custNo'][$index]);
        $goodsSpec->click           = intval($click);
        $goodsSpec->sale_num        = intval($saleNum);
        $goodsSpec->jiyou_good_id   = intval($jiyouId);

        //当商品是下架状态/sku库存数量为0/sku销售价为0,设置sku状态为下架
        if ($good->state == config('statuses.good.state.soldOut.code') || $goodsSpec->number == 0 || $goodsSpec->sell_price == 0) {
            $goodsSpec->state = config('statuses.good.state.soldOut.code');
        } else {
            $goodsSpec->state = $goodSkuParams['state'][$index];
        }

        return $goodsSpec;
    }
}
