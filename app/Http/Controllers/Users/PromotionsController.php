<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Services\PromotionService;

class PromotionsController extends Controller
{
    public function index()
    {
        //活动
        $promotionParam = array(
            'type'      =>  config('statuses.promotion.type.speed.code'),
            'awardType' =>  config('statuses.promotion.awardType.speed.code')
        );
        //进行中活动
        $data['onGoingPromotion'] = PromotionService::underWayPromotion($promotionParam);
        //明日活动
        $promotionParam['date'] = date('Y-m-d',strtotime('+1 day'));
        $data['tomorrowPromotion'] = PromotionService::underWayPromotion($promotionParam);
        //是否在活动时间内
        $data['isGoing'] = false;
        $time = intval(date('Hi'));
        $data['promotionTime'] = config('system.promotion');
        $data['multipleLimit'] = config('order.multipleLimit');
        if ($time >= $data['promotionTime']['startTime'] && $time < $data['promotionTime']['endTime']) {
            $data['isGoing'] = true;
        }
        return view('users.promotions',$data);
    }
}
