<?php

namespace App\Daoes\Users;

use App\Daoes\BaseDao;
use App\Models\Goods;

class GoodsDao extends BaseDao {
	/**
	 * 查询分类商品列表
	 *
	 * @return App\Models\Goods
	 */
	public static function getList($category_id = 0) {
		$builder = Goods::where(['category_id' => $category_id, 'state' => 0])->orderBy('sort', 'asc');
		return $builder->get();
	}

	/**
	 * 分页查询商品
	 * @param  int $pageSize
	 * @param  array $params
	 *
	 * @return array
	 */
	public static function findByPage($curPage, $pageSize, $params) {
		$builder = Goods::where('category_parent_id', $params['category_parent_id'])->offset($pageSize * ($curPage - 1))->limit($pageSize);

		if ($params['category_id'] > 0) {
			$builder->where('category_id', $params['category_id']);
		}
		if ($params['hasStock'] > 0) {
			$params['hasStock'] == 1 ? $builder->where('total_num', '>', '0') : $builder->where('total_num', 0);
		}
		if ($params['sort'] == 1) {
			$builder->orderBy('sale_num', $params['sortType']);
		} elseif ($params['sort'] == 2) {
			$builder->orderBy('sell_price', $params['sortType']);
		} else {
			$builder->orderBy('click', 'desc');
		}
		return $builder->get();
	}

	/**
	 * 根据Id查询商品
	 * @param int $id
	 *
	 * @return App\Models\Goods
	 */
	public static function findById($id) {
		return Goods::find($id);
	}
}
