<?php

namespace App\Http\Controllers\Admins\Goods;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Goods\GoodRequest;
use App\Services\Admins\GoodsService;
use App\Services\Admins\ModelService;
use App\Services\BrandService;
use App\Services\CategoryService;
use App\Services\GoodsSpecService;
use App\Utils\Page;
use Illuminate\Http\Request;

class GoodController extends Controller
{
    public function index()
    {
        $params                     = array();
        $curPage                    = trimSpace(request('curPage', 1));
        $pageSize                   = trimSpace(request('pageSize', Page::PAGESIZE));
        $params['search']           = trimSpace(request('search', ''));
        $params['brandId']          = trimSpace(request('brandId', 0));
        $params['categoryParentId'] = trimSpace(request('goodCategoryId', 0));

        $page = GoodsService::findByPageAndParams($curPage, $pageSize, $params);

        return view('admins.goods.goods')
            ->with('categoryList', CategoryService::findByParentId())
            ->with('brandList', BrandService::findByParams())
            ->with('search', $params['search'])
            ->with('brandId', $params['brandId'])
            ->with('goodCategory', CategoryService::findById($params['categoryParentId']))
            ->with('page', $page);
    }

    public function create()
    {
        return view('admins.goods.addGood')
            ->with('categoryList', CategoryService::findByParentId())
            ->with('modelList', ModelService::findByParams())
            ->with('brandList', BrandService::findByParams());
    }

    public function store(GoodRequest $request)
    {
        $results        = GoodsService::save($request);
        $results['url'] = '/admin/good';
        return response()->json($results);
    }

    public function edit($id)
    {
        $good = GoodsService::findById($id);
        if (!$good) {
            abort(400, '商品不存在。');
        }

        $modelAttrs = array();
        $modelSpecs = array();
        if ($good->goods_type != config('statuses.zeroAndOne.zero.code')) {
            $model = ModelService::findById($good->goods_type);
            if ($model) {
                foreach ($model->attributes as $attribute) {
                    $attr           = $attribute->toArray();
                    $attr['values'] = explode(',', $attribute->value);
                    if ($attribute->spec == config('statuses.zeroAndOne.one.code')) {
                        $modelSpecs[] = $attr;
                    } else {
                        $modelAttrs[] = $attr;
                    }
                }
            }
        }

        return view('admins.goods.editGood')
            ->with('categoryList', CategoryService::findByParentId())
            ->with('modelList', ModelService::findByParams())
            ->with('brandList', BrandService::findByParams())
            ->with('goodAttrs', $this->disposeGoodAttrs($good->goodsAttrs, $modelAttrs))
            ->with('goodSpecs', $this->generateSpecDatas($good, $good->goodsSpecs, $modelSpecs))
            ->with('good', $good);
    }

    public function update(GoodRequest $request, $id)
    {
        $results        = GoodsService::updateGood($request, $id);
        $results['url'] = '/admin/good';
        return response()->json($results);
    }

    public function destroy($id)
    {
        GoodsService::destroy(array($id));
        return response()->json(array(
            'code'     => 200,
            'messages' => array('删除成功'),
            'url'      => '/admin/good',
        ));
    }

    public function destroyAll()
    {
        $ids = request('ids', array());
        if (count($ids) == 0) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('参数错误'),
                'url'      => '',
            ));
        }

        GoodsService::destroy($ids);
        return response()->json(array(
            'code'     => 200,
            'messages' => array('删除成功'),
            'url'      => '/admin/good',
        ));
    }

    public function sort($id)
    {
        $good = GoodsService::findById($id);
        if (!$good) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('商品不存在'),
                'url'      => '',
            ));
        }

        $sort       = request('sort', 99);
        $good->sort = $sort;

        return $this->updateGood($good);
    }

    public function updateState($id)
    {
        $good = GoodsService::findById($id);
        if (!$good) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('商品不存在'),
                'url'      => '',
            ));
        }

        if ($good->state == config('statuses.good.state.putaway.code')) {
            $good->state = config('statuses.good.state.soldOut.code');
        } elseif ($good->state == config('statuses.good.state.soldOut.code')) {
            $good->state = config('statuses.good.state.putaway.code');
        } else {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('商品已删除,不能进行上/下架操作'),
                'url'      => '',
            ));
        }

        $results = GoodsService::updateState($good);
        if (!$results) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('上/下架操作失败'),
                'url'      => '',
            ));
        }
        return response()->json(array(
            'code'     => 200,
            'messages' => array('上/下架操作成功'),
            'url'      => '',
        ));
    }

    public function editGoodNum($id)
    {
        $good = GoodsService::findById($id);
        if (!$good) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('商品不存在'),
                'url'      => '',
            ));
        }

        $html = '<form name="updateGoodNumForm" method="post" style="max-height:450px;" action="/admin/good/' . $id . '/updateGoodNum">';
        $html .= '<table class="table"><thead><tr><th>商品</th><th>库存量</th></tr></thead><tbody>';
        $goodSpecs = $good->goodsSpecs;
        if (isset($goodSpecs) && count($goodSpecs) > 0) {
            foreach ($goodSpecs as $spec) {
                $html .= '<tr><td>' . $good->name . '&nbsp;&nbsp;&nbsp;' . implode('&nbsp;&nbsp;&nbsp;', explode(',', $spec->values)) . '</td>';
                $html .= '<td><input type="text" class="form-control input-sm" name="totalNum[' . $spec->id . ']" value="' . $spec->number . '" onkeyup="onlyNum(this)"/></td></tr>';
            }
        }
        $html .= '</tbody></table></form>';

        return response()->json(array(
            'code'     => 200,
            'messages' => array('查询成功'),
            'datas'    => $html,
            'url'      => '',
        ));
    }

    public function updateGoodNum($id)
    {
        $good = GoodsService::findById($id);
        if (!$good) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('商品不存在'),
                'url'      => '',
            ));
        }

        $totalNums = request('totalNum', array());

        if (!GoodsService::updateGoodNum($good, $totalNums)) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('更新商品库存失败'),
                'url'      => '',
            ));
        }

        return response()->json(array(
            'code'     => 200,
            'messages' => array('更新商品库存成功'),
            'url'      => '/admin/good',
        ));
    }

    public function editGoodPrice($id)
    {
        $good = GoodsService::findById($id);
        if (!$good) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('商品不存在'),
                'url'      => '',
            ));
        }

        $html = '<form name="updateGoodPriceForm" method="post" style="max-height:450px;" action="/admin/good/' . $id . '/updateGoodPrice">';
        $html .= '<table class="table"><thead><tr><th>商品</th><th>销售价</th><th>会员价</th><th>批发价</th><th>成本</th></tr></thead><tbody>';
        $goodSpecs = $good->goodsSpecs;
        if (isset($goodSpecs) && count($goodSpecs) > 0) {
            foreach ($goodSpecs as $spec) {
                $html .= '<tr><td>' . $good->name . '&nbsp;&nbsp;&nbsp;' . implode('&nbsp;&nbsp;&nbsp;', explode(',', $spec->values)) . '</td>';
                $html .= '<td><input type="text" class="form-control input-sm" name="price[' . $spec->id . '][sellPrice]" value="' . round($spec->sell_price / 100, 2) . '" onkeyup="onlyAmount(this)"/></td>';
                $html .= '<td><input type="text" class="form-control input-sm" name="price[' . $spec->id . '][memberPrice]" value="' . round($spec->member_price / 100, 2) . '" onkeyup="onlyAmount(this)"/></td>';
                $html .= '<td><input type="text" class="form-control input-sm" name="price[' . $spec->id . '][wholesalePrice]" value="' . round($spec->wholesale_price / 100, 2) . '" onkeyup="onlyAmount(this)"/></td>';
                $html .= '<td><input type="text" class="form-control input-sm" name="price[' . $spec->id . '][costPrice]" value="' . round($spec->cost_price / 100, 2) . '" onkeyup="onlyAmount(this)"/></td></tr>';
            }
        }
        $html .= '</tbody></table></form>';

        return response()->json(array(
            'code'     => 200,
            'messages' => array('查询成功'),
            'datas'    => $html,
            'url'      => '',
        ));
    }

    public function updateGoodPrice($id)
    {
        $good = GoodsService::findById($id);
        if (!$good) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('商品不存在'),
                'url'      => '',
            ));
        }

        $prices = request('price', array());

        if (!GoodsService::updateGoodPrice($good, $prices)) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('更新商品价格失败'),
                'url'      => '',
            ));
        }

        return response()->json(array(
            'code'     => 200,
            'messages' => array('更新商品价格成功'),
            'url'      => '/admin/good',
        ));
    }

    /**
     * 更新
     * @param App\Models\Goods $good
     *
     * @return json
     */
    private function updateGood($good)
    {
        if (!GoodsService::update($good)) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('操作失败'),
                'url'      => '',
            ));
        }

        return response()->json(array(
            'code'     => 200,
            'messages' => array('操作成功'),
            'url'      => '/admin/good',
        ));
    }

    /**
     * 处理商品规格
     * @param array $goodAttrs
     * @param array $modelAttrs
     *
     * @return array
     */
    private function disposeGoodAttrs($goodAttrs, $modelAttrs)
    {
        if (isset($goodAttrs) && count($goodAttrs) > 0) {

            foreach ($modelAttrs as &$modelAttr) {
                foreach ($modelAttr['values'] as &$value) {
                    foreach ($goodAttrs as $goodAttr) {
                        if ($modelAttr['id'] == $goodAttr->attr_ids && in_array($value, explode(',', $goodAttr->values))) {
                            $value = array('value' => $value, 'selected' => true);
                            break;
                        }
                    }
                }
            }
        }

        return $modelAttrs;
    }

    /**
     * 生成规格相关数据
     * @param App\Models\Goods $good
     * @param array $goodsSpecs
     * @param array $modelSpecs
     *
     * @return array
     */
    private function generateSpecDatas($good, $goodsSpecs, $modelSpecs)
    {
        $specDatas = array('specNames' => $this->generateSpecNames($modelSpecs), 'specs' => array(), 'skuSpecNames' => array(), 'skus' => array());

        $skus  = $this->generateSkus($goodsSpecs);
        $specs = $this->generateSpecs($good, $modelSpecs);

        //商品没有选择规格
        if ($good->goods_type == config('statuses.zeroAndOne.zero.code')) {
            foreach ($specs as &$spec) {
                //将规格关联的sku信息写入
                $spec = $this->setSpecValues($skus[0]);
            }

            foreach ($skus as &$sku) {
                //将sku关联的规格ids写入
                $sku = $this->setSkuValues($sku, $specs[0]['values']);
            }
        } else {
            foreach ($skus as &$sku) {
                foreach ($specs as $key => &$spec) {
                    if (!isset($sku['values']) || $sku['values'] == '' || $sku['values'] === $key) {
                        //将规格关联的sku信息写入
                        $spec = $this->setSpecValues($sku);
                        //将sku关联的规格ids写入
                        $sku = $this->setSkuValues($sku, $spec['attrIds']);

                        break;
                    }
                }
            }
        }

        //获取sku的规格名称
        $skuSpecNames    = array();
        $oldAttrIds      = explode(',', $goodsSpecs[0]->attr_ids);
        $goodSpecList    = GoodsSpecService::findByIds($oldAttrIds);
        $IdKeyNameValues = array_column($goodSpecList->toArray(), 'name', 'id');
        if (count($IdKeyNameValues) > 0) {
            foreach ($oldAttrIds as $id) {
                $skuSpecNames[] = $IdKeyNameValues[$id];
            }
        }

        $specDatas['skuSpecNames'] = $skuSpecNames;
        $specDatas['specs']        = $specs;
        $specDatas['skus']         = $skus;

        return $specDatas;
    }

    /**
     * 将sku关联的规格ids写入
     * @param array $sku
     * @param string $values
     *
     * @return array
     */
    private function setSkuValues($sku, $values)
    {
        $sku['isRelation']     = '1';
        $sku['relationValues'] = $values;

        return $sku;
    }

    /**
     * 将规格关联的sku信息写入
     * @param array $sku
     *
     * @return array
     */
    private function setSpecValues($sku)
    {
        return array(
            'name'           => $sku['name'],
            'specIdStr'      => $sku['specIdStr'],
            'attrIds'        => $sku['attrIds'],
            'valueStr'       => $sku['valueStr'],
            'values'         => $sku['values'],
            'idKeyValues'    => $sku['idKeyValues'],
            'storeNum'       => $sku['storeNum'],
            'warningNum'     => $sku['warningNum'],
            'sellPrice'      => $sku['sellPrice'],
            'memberPrice'    => $sku['memberPrice'],
            'wholesalePrice' => $sku['wholesalePrice'],
            'costPrice'      => $sku['costPrice'],
            'weight'         => $sku['weight'],
            'barCode'        => $sku['barCode'],
            'custNo'         => $sku['custNo'],
            'imgs'           => $sku['imgs'],
            'state'          => $sku['state'],
            'isRelation'     => '1',
            'relationSpecId' => $sku['id'],
        );
    }

    /**
     * 生成div隐藏内容
     * @param  array $goodsSpecs
     *
     * @return array
     */
    private function generateSkus($goodsSpecs)
    {
        $skus = array();
        foreach ($goodsSpecs as $goodsSpec) {
            $value        = (!isset($goodsSpec->values) || $goodsSpec->values == '') ? '0' : $goodsSpec->values;
            $skus[$value] = array(
                'id'             => $goodsSpec->id,
                'name'           => $goodsSpec->name,
                'specIdStr'      => implode('', explode(',', $goodsSpec->attr_ids)),
                'attrIds'        => $goodsSpec->attr_ids,
                'valueStr'       => replaceSpecialChar(implode('', explode(',', $goodsSpec->values))),
                'values'         => $goodsSpec->values,
                'idKeyValues'    => array_combine(explode(',', $goodsSpec->attr_ids), explode(',', $goodsSpec->values)),
                'storeNum'       => $goodsSpec->number,
                'warningNum'     => $goodsSpec->warning_num,
                'sellPrice'      => round($goodsSpec->sell_price / 100, 2),
                'memberPrice'    => round($goodsSpec->member_price / 100, 2),
                'wholesalePrice' => round($goodsSpec->wholesale_price / 100, 2),
                'costPrice'      => round($goodsSpec->cost_price / 100, 2),
                'weight'         => $goodsSpec->weight,
                'barCode'        => $goodsSpec->bar_code,
                'custNo'         => $goodsSpec->cust_partno,
                'imgs'           => $goodsSpec->imgs,
                'state'          => $goodsSpec->state,
                'isRelation'     => '0',
                'relationValues' => 0,
            );
        }

        return $skus;
    }

    /**
     * 生成表格th内容
     * @param  array $modelSpecs
     *
     * @return array
     */
    private function generateSpecNames($modelSpecs)
    {
        $specNames = array();
        foreach ($modelSpecs as $modelSpec) {
            $specNames[] = $modelSpec['name'];
        }

        return $specNames;
    }

    /**
     * 生成表格td内容
     * @param  App\Models\Goods $good
     * @param  array $modelSpecs
     *
     * @return array
     */
    private function generateSpecs($good, $modelSpecs)
    {
        $specs = array();
        if (count($modelSpecs) == 0) {
            $idKeyValues    = array();
            $idKeyValues[0] = null;
            $specs[0]       = array(
                'name'           => $good->name,
                'specIdStr'      => 0,
                'attrIds'        => 0,
                'valueStr'       => null,
                'values'         => null,
                'idKeyValues'    => $idKeyValues,
                'storeNum'       => 0,
                'warningNum'     => 0,
                'sellPrice'      => 0.00,
                'memberPrice'    => 0.00,
                'wholesalePrice' => 0.00,
                'costPrice'      => 0.00,
                'weight'         => 0,
                'barCode'        => null,
                'custNo'         => null,
                'imgs'           => null,
                'state'          => $good->state,
                'isRelation'     => '0',
                'relationSpecId' => 0,
            );
        } else {
            $len      = count($modelSpecs);
            $nl       = 1;
            $h        = array($len);
            $rowspans = array($len);
            for ($i = 0; $i < $len; $i++) {
                $itemlen = count($modelSpecs[$i]['values']);
                if ($itemlen <= 0) {
                    $itemlen = 1;
                }
                $nl *= $itemlen;
                $h[$i] = array($nl);
                for ($j = 0; $j < $nl; $j++) {
                    $h[$i][$j] = array();
                }
                $l            = count($modelSpecs[$i]['values']);
                $rowspans[$i] = 1;
                for ($j = $i + 1; $j < $len; $j++) {
                    $rowspans[$i] *= count($modelSpecs[$j]['values']);
                }
            }

            for ($m = 0; $m < $len; $m++) {
                $k = 0;
                $b = 0;
                $n = 0;
                for ($j = 0; $j < $nl; $j++) {
                    $rowspan   = $rowspans[$m];
                    $h[$m][$j] = array(
                        'name' => $modelSpecs[$m]['values'][$b],
                        'id'   => $modelSpecs[$m]['id']);

                    $n++;
                    if ($n == $rowspan) {
                        $b++;
                        if ($b > count($modelSpecs[$m]['values']) - 1) {
                            $b = 0;
                        }
                        $n = 0;
                    }
                }
            }

            for ($i = 0; $i < $nl; $i++) {
                $specIds    = array();
                $specValues = array();
                for ($j = 0; $j < $len; $j++) {
                    $specIds[]    = $h[$j][$i]['id'];
                    $specValues[] = $h[$j][$i]['name'];
                }
                $specIdStr    = implode(',', $specIds);
                $specValueStr = implode(',', $specValues);

                $specs[$specValueStr] = array(
                    'name'           => $good->name . ' ' . implode(' ', $specValues),
                    'specIdStr'      => implode('', $specIds),
                    'attrIds'        => $specIdStr,
                    'valueStr'       => replaceSpecialChar(implode('', $specValues)),
                    'values'         => $specValueStr,
                    'idKeyValues'    => array_combine($specIds, $specValues),
                    'storeNum'       => 0,
                    'warningNum'     => 0,
                    'sellPrice'      => 0.00,
                    'memberPrice'    => 0.00,
                    'wholesalePrice' => 0.00,
                    'costPrice'      => 0.00,
                    'weight'         => 0,
                    'barCode'        => null,
                    'custNo'         => null,
                    'imgs'           => null,
                    'state'          => $good->state,
                    'isRelation'     => '0',
                    'relationSpecId' => 0,
                );
            }
        }

        return $specs;
    }
}
