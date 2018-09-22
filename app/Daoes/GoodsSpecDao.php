<?php

namespace App\Daoes;

use App\Daoes\BaseDao;
use App\Models\Admins\Attribute;
use App\Models\GoodsSpec;
use Illuminate\Support\Facades\DB;

class GoodsSpecDao extends BaseDao {

	/**
	 * 热卖推荐
	 */
	public static function recommend() {
		//不显示积分商品 500
		return GoodsSpec::join('category as c', 'goods_spec.category_parent_id', '=', 'c.id')
			->where('goods_spec.state', 0)
			->where('c.parent_id', '!=', 500)
			->orderByRaw(DB::raw("IF(goods_spec.number>0,1,0) desc"))
			->orderBy('sale_num', 'desc')
			->orderBy('click', 'desc')
			->limit(config('system.recommend'))
			->select('goods_spec.*')
			->get();
	}

	//获取商品sku
	public static function getSkuByGoods($goods_id) {
		$result = GoodsSpec::where(array('goods_id' => $goods_id, 'state' => 0))->groupBy('goods_id')->select(DB::raw("GROUP_CONCAT(attr_ids) AS attr_ids"))->first();
		if (!empty($result)) {
			$attrs = explode(',', $result->attr_ids);
			return Attribute::whereIn('id', array_unique($attrs))->get();
		}
		return null;
	}

	/**
	 * 查询sku
	 *
	 * @return App\Models\GoodsSpec
	 */
	public static function findById($id, $params = array()) {
		$builder = GoodsSpec::with('goods', 'category');
		if (array_key_exists('states', $params) && count($params['states']) > 0) {
			$builder->whereIn('state', $params['states']);
		} else {
			$builder->where('state', config('statuses.good.state.putaway.code'));
		}

		return $builder->find($id);
	}

	/**
	 * 查询
	 * @param  array $params
	 *
	 * @return array
	 */
	public static function findByParams($params) {
		$builder = GoodsSpec::select();

		if (array_key_exists('goodsId', $params) && $params['goodsId'] > 0) {
			$builder->where('goods_id', $params['goodsId']);
		}
		if (array_key_exists('notIn', $params)) {
			foreach ($params['notIn'] as $key => $value) {
				$builder->whereNotIn($key, $value);
			}
		}
		if (array_key_exists('isNumber', $params) && $params['isNumber']) {
			$builder->where('number', '>', 0);
		}

		return $builder->get();
	}

	/**
	 * 根据Ids查询商品
	 * @param array $ids
	 * @param boolean $isQueryAll (是否查询所有数据,包含软删除的数据)
	 *
	 */
	public static function findByIds($ids, $isQueryAll) {
		if ($isQueryAll) {
			return GoodsSpec::withTrashed()->whereIn('id', $ids)->get();
		} else {
			return GoodsSpec::whereIn('id', $ids)->get();
		}
	}

	//确认订单切换数量
	public static function changeNum($spec_id = 0, $num = 1) {
		$returnObj = GoodsSpec::where('goods_spec.id', $spec_id)->select('goods_spec.id', 'goods_spec.number', 'goods_spec.sell_price', 'goods_spec.state')->first();
		if ($returnObj->state > 0) {
			//已下架
			$returnObj->number = 0;
		}
		return $returnObj;

	}

	/**
	 * 查找购物车添加的商品
	 * @param number $goods_id
	 * @param array $spec
	 * @param number $spec_id
	 */
	public static function getSpec($goods_id, $spec, $spec_id = 0) {
		$builder = GoodsSpec::where('goods_id', $goods_id);
		if (empty($spec) && $spec_id) {
			//单sku
			$returnArr = $builder->where('id', $spec_id)->first()->toArray();
		} else {
			//多sku
			$attrSpec = $builder->get();
			$returnArr = $specArr = $result = array();
			foreach ($attrSpec as $j => $k) {
				$specArr[$k->id] = array_combine(explode(',', $k->attr_ids), explode(',', $k->values));
				$result[$k->id] = $k;
			}
			foreach ($specArr as $m => $n) {
				if (empty(array_diff_assoc($n, $spec))) {
					$returnArr = $result[$m];
					break;
				}
			}
		}
		return $returnArr;
	}

	/**
	 * 前台切换sku
	 * @param number $goods_id
	 * @param array $spec
	 * @param number $spec_id
	 * @param number $num
	 * @return array|object
	 */
	public static function changeSpec($goods_id, $spec, $spec_id = 0, $num = 1) {
		//没有根据角色查询不同价格
		$builder = GoodsSpec::where('goods_spec.goods_id', $goods_id);
		$builder->select('goods_spec.id', 'goods_spec.name', 'goods_spec.attr_ids', 'goods_spec.values', 'goods_spec.number', 'goods_spec.sell_price', 'goods_spec.weight', 'goods_spec.img', 'goods_spec.imgs', 'goods_spec.state');
		//单sku
		if (empty($spec) && $spec_id) {
			$returnArr = $builder->where('goods_spec.id', $spec_id)->first();
		} else {
			$attrSpec = $builder->get();
			//当前sku
			$returnArr = $specArr = $result = array();
			foreach ($attrSpec as $j => $k) {
				$specArr[$k['id']] = array_combine(explode(',', $k['attr_ids']), explode(',', $k['values']));
				$result[$k['id']] = $k;
			}
			foreach ($specArr as $m => $n) {
				if (empty(array_diff_assoc($n, $spec))) {
					$returnArr = $result[$m];
					break;
				}
			}
		}
		if ($returnArr->state > 0) {
			//已下架
			$returnArr->number = 0;
		}
		return $returnArr;
	}

	//spec商品详情
	public static function findSpecGoodsById($spec_id) {
		return GoodsSpec::join('goods as g', 'goods_spec.goods_id', '=', 'g.id')
			->where(array('goods_spec.id' => $spec_id, 'goods_spec.state' => 0))
			->select('goods_spec.*', 'g.description', 'g.unit')
			->first();
	}

	/**
	 * 支付完成更新库存
	 * @param  int $order_id
	 */
	public static function updateGoodsSpecNum($order_id) {
		$order_id = intval($order_id);
		return DB::update("UPDATE `goods_spec` s,`order_goods` g SET s.`number` = s.`number` - g.`num`, s.`wait_number` = s.`wait_number` + g.`num`, s.`sale_num` = s.`sale_num` + g.`num` WHERE s.id = g.spec_id AND g.`order_id` = $order_id");
	}

	/**
	 * 批量删除
	 * @param  array   $ids
	 *
	 * @return boolean
	 */
	public static function batchDelete($ids) {
		$count = GoodsSpec::destroy($ids);
		if ($count > 0) {
			return true;
		}
		return false;
	}

	/**
	 * 退款，相加库存数量,相减销售数量,相减sku待发货数量
	 * @param  int $id
	 * @param  int $number
	 *
	 * @return
	 */
	public static function incrementNumber($id, $number) {
		return DB::update('UPDATE `goods_spec` SET `number` = `number` + ' . $number . ', `sale_num` = `sale_num` - ' . $number . ', `wait_number` = `wait_number` - ' . $number . ' WHERE `id` = ?', array($id));
	}

	/**
	 * 退款失败，相减库存数量,相加销售数量,相加sku待发货数量
	 * @param  int $id
	 * @param  int $number
	 *
	 * @return
	 */
	public static function decrementNumber($id, $number) {
		return DB::update('UPDATE `goods_spec` SET `number` = `number` - ' . $number . ', `sale_num` = `sale_num` + ' . $number . ', `wait_number` = `wait_number` + ' . $number . ' WHERE `id` = ?', array($id));
	}

	/**
	 * 发货,相减待发货数量
	 * @param  int $id
	 * @param  int $number
	 *
	 * @return
	 */
	public static function decrementWaitNumber($id, $number) {
		return GoodsSpec::find($id)->decrement('wait_number', $number);
	}

	/**
	 * 相加待发货数量
	 * @param  int $id
	 * @param  int $number
	 *
	 * @return
	 */
	public static function incrementWaitNumber($id, $number) {
		return GoodsSpec::find($id)->increment('wait_number', $number);
	}
}
