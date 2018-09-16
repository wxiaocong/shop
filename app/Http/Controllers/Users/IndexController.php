<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Services\PromotionService;
use App\Services\AdPositionService;
use App\Daoes\GoodsSpecDao;

class IndexController extends Controller
{
    public function index()
    {
        //swiper
        $data['adPositions'] = AdPositionService::findByParams();
        //热卖推荐
        $data['recommends'] = GoodsSpecDao::recommend();
        return view('users.index', $data);
    }
}
