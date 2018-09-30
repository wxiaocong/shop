<?php

namespace App\Http\Controllers\Users;

use App\Daoes\GoodsAttrDao;
use App\Daoes\GoodsSpecDao;
use App\Http\Controllers\Controller;
use App\Services\GoodsSpecService;
use App\Services\Users\ExpressAddressService;
use App\Utils\Page;
use EasyWeChat;
use Session;

class GoodsController extends Controller {
    public function show() {
        $spec_id = intval(request('good', 0));
        //商品信息
        $data['goodsInfo'] = GoodsSpecService::findById($spec_id);
        if (!empty($data['goodsInfo'])) {
            //规格
            $data['skus'] = GoodsSpecService::getSkuByGoods($data['goodsInfo']->goods_id);
            //属性
            $data['attrs'] = GoodsAttrDao::getAttrByGoods($data['goodsInfo']->goods_id);
            //默认大图
            $data['defaultImgs'] = json_decode($data['goodsInfo']->imgs);

            //生成分享配置
            $data['shareConfig'] = '';
            if (isWeixin()) {
                $app = EasyWeChat::officialAccount();
                $data['shareConfig'] = $app->jssdk->buildConfig(array('onMenuShareTimeline', 'onMenuShareAppMessage'), false);
            }
            return view('users.goodsDetail', $data);
        }
        abort(500, '商品不存在或已下架');
    }

    //商品搜索结果页面
    public function search() {
        $data['pageSize'] = Page::PAGESIZE;
        $data['searchKey'] = request('searchKey');
        return view('users.search', $data);
    }

    //抢购页面
    public function purchase() {
        $specId = intval(request('spec_id', 0));
        $goodsId = intval(request('goods_id', 0));
        $data['num'] = intval(request('num', 1));
        $spec = request('spec');
        Session::forget('expressReferer');
        
        if ($goodsId < 1 || (empty($spec) && $specId < 1)) {
            abort(404, '缺少参数');
        }

        //默认地址
        $data['defaultAddress'] = ExpressAddressService::getDefault();
        if ($data['num'] < 1) {
            abort(500, '商品数量错误');
        }
        //商品详情
        $data['goodsInfo'] = GoodsSpecDao::changeSpec($goodsId, $spec, $specId, $data['num']);
        if (empty($data['goodsInfo'])) {
            abort(500, '未找到该商品信息');
        }
        if ($data['goodsInfo']->state > 0) {
            abort(500, '商品已下架');
        }
        $data['maxSpec'] = $data['goodsInfo']->number; //默认单次最大可下单数量,活动限制单用户下单数量
        if ($data['maxSpec'] < $data['num']) {
            abort(500, '库存不足');
        }
        return view('users.purchase', $data);
    }

    /**
     * change num
     */
    public function changeNum() {
        $specId = intval(request('spec_id', 0));
        $num = intval(request('num', 1));
        if (empty($specId)) {
            return response()->json(NULL);
        }
        return response()->json(GoodsSpecDao::changeNum($specId, $num));
    }

    /**
     * change spec
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeSpec() {
        $goodsId = intval(request('goods_id', 0));
        $specId = intval(request('spec_id', 0));
        $num = intval(request('num', 1));
        $spec = request('spec');
        if (empty($goodsId) || (empty($spec) && empty($specId))) {
            return response()->json(NULL);
        }
        return response()->json(GoodsSpecDao::changeSpec($goodsId, $spec, $specId, $num));
    }
}
