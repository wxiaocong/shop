<?php

namespace App\Services;

use App\Daoes\PromotionDao;
use App\Models\Promotion;
use App\Services\GoodsSpecService;
use App\Services\PromotionService;
use Illuminate\Http\Request;

class PromotionService
{
    /**
     * 支付完成更新活动已售商品数量
     * @param  int $order_id
     */
    public static function updateSelledNum($order_id)
    {
        return PromotionDao::updateSelledNum($order_id);
    }

    /**
     * 前台进行中活动
     * @param  array $param
     */
    public static function underWayPromotion($param = array())
    {
        return PromotionDao::underWayPromotion($param);
    }

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
        return PromotionDao::findByPage($curPage, $pageSize, $params);
    }

    /**
     * 分页查询
     * @param  int $curPage
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPageAndParams($curPage, $pageSize, $params = array())
    {
        return PromotionDao::findByPageAndParams($curPage, $pageSize, $params);
    }

    /**
     * 根据Id查询
     * @param int $id
     * @param  string $type
     * @param  string $awardType
     *
     */
    public static function findById($id, $type, $awardType, $isValid = false)
    {
        return PromotionDao::findById($id, $type, $awardType, $isValid);
    }

    /**
     * 查询
     * @param  array $params
     *
     * @return array
     */
    public static function findByParams($params = array())
    {
        return PromotionDao::findByParams($params);
    }

    /**
     * saveOrUpdate
     * @param  Request $request
     * @param  string $type
     * @param  string $awardType
     *
     * @return array
     */
    public static function saveOrUpdate(Request $request, $type, $awardType)
    {
        $specId   = $request->input('specId', config('statuses.zeroAndOne.zero.code'));
        $price    = intval($request->input('price', 0.00) * 100);
        $onceNum  = $request->input('onceNum', config('statuses.zeroAndOne.zero.code'));
        $totalNum = $request->input('totalNum', config('statuses.zeroAndOne.zero.code'));
        $spec     = GoodsSpecService::findById($specId);

        $results = self::validParams($price, $onceNum, $totalNum, $spec);
        if ($results['code'] != 200) {
            return $results;
        }

        //生成限时抢购对象
        $promotion = self::generatePromotion($request, $specId, $price, $onceNum, $totalNum, $spec, $type, $awardType);

        $promotion = PromotionDao::save($promotion, session('adminUser')->id);
        if (!$promotion) {
            return array(
                'code'     => 500,
                'messages' => array('保存活动失败'),
                'url'      => '',
            );
        }

        return array(
            'code'     => 200,
            'messages' => array('保存活动成功'),
            'url'      => '',
        );
    }

    /**
     * 保存活动
     * @param  App\Models\Promotion $promotion
     *
     * @return array
     */
    public static function update($promotion)
    {
        $promotion = PromotionDao::save($promotion, session('adminUser')->id);
        if (!$promotion) {
            return array(
                'code'     => 500,
                'messages' => array('保存活动失败'),
                'url'      => '',
            );
        }

        return array(
            'code'     => 200,
            'messages' => array('保存活动成功'),
            'url'      => '',
        );
    }

    /**
     * 批量删除
     * @param  array $ids
     *
     * @return boolean
     */
    public static function destroy($ids)
    {
        return PromotionDao::batchDelete($ids);
    }

    /**
     * 生成限时抢购对象
     * @param  Request $request
     * @param  int $specId
     * @param  int $price
     * @param  int $onceNum
     * @param  int $totalNum
     * @param  App\Models\GoodsSpec $spec
     * @param  string $type
     * @param  string $awardType
     *
     * @return App\Models\Promotion
     */
    private static function generatePromotion(Request $request, $specId, $price, $onceNum, $totalNum, $spec, $type, $awardType)
    {
        $id = intVal($request->input('id', config('statuses.zeroAndOne.zero.code')));

        if ($id != 0) {
            $promotion             = PromotionDao::findById($id, $type, $awardType);
            $promotion->updated_at = date('Y-m-d H:i:s');
        } else {
            $promotion = new Promotion();
        }
        $promotion->name       = trimSpace($request->input('name', ''));
        $promotion->start_time = trimSpace($request->input('startDate', ''));
        $promotion->end_time   = trimSpace($request->input('endDate', ''));
        $promotion->is_close   = trimSpace($request->input('isClose', config('statuses.zeroAndOne.zero.code')));
        $promotion->intro      = trimSpace($request->input('description', ''));
        $promotion->condition  = $specId;
        $promotion->type       = $type;
        $promotion->award_type = $awardType;
        $promotion->user_group = 'all';

        $awardValue = array(
            'id'       => $specId,
            'name'     => $spec->name,
            'totalNum' => $totalNum,
            'onceNum'  => $onceNum,
            'price'    => $price,
            'img'      => $spec->img,
            'imgs'     => json_decode($spec->imgs, true),
        );

        $promotion->award_value = json_encode($awardValue);

        return $promotion;
    }

    /**
     * 验证参数是否合规
     * @param  int $price
     * @param  int $onceNum
     * @param  int $totalNum
     * @param  App\Models\GoodsSpec $spec
     *
     * @return array
     */
    private static function validParams($price, $onceNum, $totalNum, $spec)
    {
        if ($price <= 0) {
            return array(
                'code'     => 500,
                'messages' => array('限时抢购价格要大于0'),
                'url'      => '',
            );
        }
        if ($onceNum <= 0) {
            return array(
                'code'     => 500,
                'messages' => array('单次最大购买数量要大于0'),
                'url'      => '',
            );
        }
        if ($totalNum <= 0) {
            return array(
                'code'     => 500,
                'messages' => array('限时抢购总数量要大于0'),
                'url'      => '',
            );
        }
        if ($onceNum > $totalNum) {
            return array(
                'code'     => 500,
                'messages' => array('单次最大购买数量要小于/等于限时抢购总数量'),
                'url'      => '',
            );
        }
        if (!$spec) {
            return array(
                'code'     => 500,
                'messages' => array('商品参数错误'),
                'url'      => '',
            );
        }
        if ($totalNum > $spec->number) {
            return array(
                'code'     => 500,
                'messages' => array('限时抢购总数量要小于/等于商品库存'),
                'url'      => '',
            );
        }

        return array(
            'code'     => 200,
            'messages' => array(),
            'url'      => '',
        );
    }
}
