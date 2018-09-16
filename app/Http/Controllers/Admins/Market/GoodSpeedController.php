<?php

namespace App\Http\Controllers\Admins\Market;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Market\GoodSpeedRequest;
use App\Services\BrandService;
use App\Services\CategoryService;
use App\Services\GoodsSpecService;
use App\Services\PromotionService;
use App\Utils\Page;
use Illuminate\Http\Request;

class GoodSpeedController extends Controller
{
    public function index()
    {
        $params              = array();
        $curPage             = trimSpace(request('curPage', 1));
        $pageSize            = trimSpace(request('pageSize', Page::PAGESIZE));
        $params['type']      = config('statuses.promotion.type.speed.code');
        $params['awardType'] = config('statuses.promotion.awardType.speed.code');

        $page = PromotionService::findByPageAndParams($curPage, $pageSize, $params);

        return view('admins.market.goodSpeeds')
            ->with('page', $page);
    }

    public function create()
    {
        return view('admins.market.editGoodSpeed')
            ->with('categoryList', CategoryService::findByParentId())
            ->with('brandList', BrandService::findByParams());
    }

    public function store(GoodSpeedRequest $request)
    {
        $results        = PromotionService::saveOrUpdate($request, config('statuses.promotion.type.speed.code'), config('statuses.promotion.awardType.speed.code'));
        $results['url'] = '/admin/speed';
        return response()->json($results);
    }

    public function edit($id)
    {
        $speed = PromotionService::findById($id, config('statuses.promotion.type.speed.code'), config('statuses.promotion.awardType.speed.code'));
        if (!$speed) {
            abort(400, '活动不存在。');
        }

        $nowDate       = date('Y-m-d');
        $operationType = 'edit';
        //活动时间范围内
        if ($nowDate >= $speed->start_time && $nowDate <= $speed->end_time) {
            $operationType = 'editIsClose';
        }
        //活动结束
        if ($nowDate > $speed->end_time) {
            $operationType = 'query';
        }

        return view('admins.market.editGoodSpeed')
            ->with('categoryList', CategoryService::findByParentId())
            ->with('brandList', BrandService::findByParams())
            ->with('speed', $speed)
            ->with('awardValue', json_decode($speed->award_value))
            ->with('spec', GoodsSpecService::findById($speed->condition))
            ->with('operationType', $operationType);
    }

    public function update(GoodSpeedRequest $request, $id)
    {
        $speed = PromotionService::findById($id, config('statuses.promotion.type.speed.code'), config('statuses.promotion.awardType.speed.code'));
        if (!$speed) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('活动不存在'),
                'url'      => '',
            ));
        }

        $nowDate = date('Y-m-d');
        if ($nowDate > $speed->end_time) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('活动已过期不能修改'),
                'url'      => '',
            ));
        }

        //活动期间只允许修改是否关闭
        if ($nowDate >= $speed->start_time && $nowDate <= $speed->end_time) {
            $speed->is_close = trimSpace($request->input('isClose', config('statuses.zeroAndOne.zero.code')));
            $results         = PromotionService::update($speed);
        } else {
            $results = PromotionService::saveOrUpdate($request, config('statuses.promotion.type.speed.code'), config('statuses.promotion.awardType.speed.code'));
        }

        $results['url'] = '/admin/speed';
        return response()->json($results);
    }

    public function destroy($id)
    {
        $speed = PromotionService::findById($id, config('statuses.promotion.type.speed.code'), config('statuses.promotion.awardType.speed.code'));
        if (!$speed) {
            return response()->json(array(
                'code'     => 500,
                'messages' => array('活动不存在'),
                'url'      => '',
            ));
        }

        PromotionService::destroy(array($id));
        return response()->json(array(
            'code'     => 200,
            'messages' => array('删除成功'),
            'url'      => '/admin/speed',
        ));
    }

    public function findGoods()
    {
        $curPage            = trimSpace(request('curPage', 1));
        $pageSize           = trimSpace(request('pageSize', 20));
        $params['brand_id'] = trimSpace(request('brandId', 0));
        $params['search']   = trimSpace(request('search', ''));
        $params['state']    = config('statuses.good.state.putaway.code');

        $datas = GoodsSpecService::findByPage($curPage, $pageSize, $params);

        $goods = array();
        if (isset($datas) && count($datas) > 0) {
            foreach ($datas as $data) {
                $img     = (isset($data->img) && $data->img != '') ? $data->img . '?x-oss-process=image/resize,w_45' : '';
                $goods[] = array(
                    'id'     => $data->id,
                    'name'   => $data->name,
                    'brand'  => $data->brand->short_name,
                    'price'  => round($data->sell_price / 100, 2),
                    'number' => $data->number,
                    'img'    => $img,
                );
            }
        }

        return response()->json(array(
            'code'     => 200,
            'messages' => array('查询成功'),
            'datas'    => $goods,
            'url'      => '',
        ));
    }
}
