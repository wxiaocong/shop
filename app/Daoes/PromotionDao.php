<?php

namespace App\Daoes;

use App\Daoes\BaseDao;
use App\Models\Promotion;
use App\Utils\Page;
use Illuminate\Support\Facades\DB;

class PromotionDao extends BaseDao
{
    /**
     * 支付完成更新活动已售商品数量
     * @param  int $order_id
     */
    public static function updateSelledNum($order_id)
    {
        return DB::update("UPDATE `promotions` p,`order_goods` g SET p.`selled_num` = p.`selled_num` + g.num WHERE p.`id` = g.`promotions_id` AND  g.`order_id` = $order_id");
    }

    /**
     * 下单更新活动下单商品数量
     */
    public static function updateOrderNum($promotion_id, $num)
    {
        return Promotion::where('id', $promotion_id)->increment('order_num', $num);
    }

    /**
     * 退款成功，相减下单商品数量,相减已售数量
     * @param  int $id
     * @param  int $number
     *
     * @return
     */
    public static function updateOrderNumAndSelledNum($id, $number)
    {
        return DB::update('UPDATE `promotions` SET `order_num` = `order_num` - ' . $number . ', `selled_num` = `selled_num` - ' . $number . ' WHERE `id` = ?', array($id));
    }

    //是否在活动时间内
    public static function isGoingTime()
    {
        $isGoing       = false;
        $time          = intval(date('Hi'));
        $promotionTime = config('system.promotion');
        if ($time >= $promotionTime['startTime'] && $time < $promotionTime['endTime']) {
            $isGoing = true;
        }
        return $isGoing;
    }

    //进行中活动
    public static function underWayPromotion($params)
    {
        $builder = Promotion::join('goods_spec as s', 'promotions.condition', '=', 's.id')
            ->where(array('promotions.is_close' => 0, 's.state' => 0));
        if (array_key_exists('date', $params)) {
            $builder->whereRaw("'{$params['date']}' between promotions.start_time and promotions.end_time");
        } else {
            $builder->whereRaw("CURRENT_DATE() between start_time and end_time");
        }
        if (array_key_exists('spec_id', $params)) {
            $builder->where('promotions.condition', $params['spec_id']);
        }
        if (array_key_exists('type', $params)) {
            $builder->where('promotions.type', $params['type']);
        }
        if (array_key_exists('awardType', $params)) {
            $builder->where('promotions.award_type', $params['awardType']);
        }
        if (array_key_exists('orderBy', $params)) {
            foreach ($params['orderBy'] as $key => $value) {
                $builder->orderBy('promotions.' . $key, $value);
            }
        }
        $builder->select('s.name', 's.img', 's.imgs', 's.sell_price', 'promotions.*');
        return array_key_exists('spec_id', $params) ? $builder->first() : $builder->get();
    }

    /**
     * 分页查询
     * @param  int $curPage
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPage($curPage, $pageSize, $params)
    {
        $builder = Promotion::where('is_close', 0)->whereRaw("CURRENT_DATE() between start_time and end_time")->offset($pageSize * ($curPage - 1))->limit($pageSize);
        if (array_key_exists('date', $params)) {
            $builder->where('type', $params['type']);
        }
        if (array_key_exists('type', $params)) {
            $builder->where('type', $params['type']);
        }
        if (array_key_exists('awardType', $params)) {
            $builder->where('award_type', $params['awardType']);
        }
        if (array_key_exists('orderBy', $params)) {
            foreach ($params['orderBy'] as $key => $value) {
                $builder->orderBy($key, $value);
            }
        }
        return $builder->get();
    }

    /**
     * 根据Id查询
     * @param int $id
     * @param  string $type
     * @param  string $awardType
     * @param  boolean $isValid false所有 true 进行中
     * @return App\Models\Promotion
     */
    public static function findById($id, $type, $awardType, $isValid = false)
    {
        $builder = Promotion::where('type', $type)->where('award_type', $awardType)->where('id', $id);
        if ($isValid) {
            $builder->whereRaw("CURRENT_DATE() between start_time and end_time");
        }
        return $builder != null ? $builder->first() : null;
    }

    /**
     * 分页查询
     * @param  int $curPage
     * @param  int $pageSize
     * @param  array $params
     *
     * @return array
     */
    public static function findByPageAndParams($curPage, $pageSize, $params)
    {
        $builder = Promotion::select();

        if (array_key_exists('type', $params)) {
            $builder->where('type', $params['type']);
        }
        if (array_key_exists('awardType', $params)) {
            $builder->where('award_type', $params['awardType']);
        }
        if (array_key_exists('orderBy', $params)) {
            foreach ($params['orderBy'] as $key => $value) {
                $builder->orderBy($key, $value);
            }
        }

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
        $builder = Promotion::select();

        if (array_key_exists('type', $params)) {
            $builder->where('type', $params['type']);
        }
        if (array_key_exists('awardType', $params)) {
            $builder->where('award_type', $params['awardType']);
        }
        if (array_key_exists('ids', $params)) {
            $builder->whereIn('id', $params['ids']);
        }

        return $builder->get();
    }

    /**
     * 批量删除
     * @param  array   $ids
     *
     * @return boolean
     */
    public static function batchDelete($ids)
    {
        $count = Promotion::destroy($ids);
        if ($count > 0) {
            return true;
        }

        return false;
    }
}
