<?php

namespace App\Http\Controllers\Users;

use App\Daoes\GoodsSpecDao;
use App\Http\Controllers\Controller;
use App\Services\AdPositionService;
use App\Services\CategoryService;

class IndexController extends Controller {
	public function index() {
		//swiper
		$data['adPositions'] = AdPositionService::findByParams();
        //分类
        $data['category'] = CategoryService::getCategoryList();
		//热卖推荐
		$data['recommends'] = GoodsSpecDao::recommend();
		return view('users.index', $data);
	}

    public function getRecommend() {
        $category_id = intval(request('category_id'), 0);
        $child = CategoryService::getCategoryList($category_id);
        if (!empty($child)) {
            foreach ($child as $value) {
            
            }
        }
        

    }
}
