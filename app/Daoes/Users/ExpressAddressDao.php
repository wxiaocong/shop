<?php

namespace App\Daoes\Users;

use App\Daoes\BaseDao;
use App\Models\Users\ExpressAddress;

class ExpressAddressDao extends BaseDao {
	/**
	 * 根据Id查询分类
	 * @param int $id
	 *
	 * @return App\Models\ExpressAddress
	 */
	public static function findById($id) {
		return ExpressAddress::where('user_id', session('user')->id)->find($id);
	}

	/**
	 * 获取默认地址
	 * @param int $id
	 *
	 */
	public static function getDefault() {
		return ExpressAddress::where(['user_id' => session('user')->id])->orderBy('is_default', 'desc')->first();
	}

	/**
	 * 设置默认地址
	 * @param int $id
	 *
	 */
	public static function setDefault($id) {
		ExpressAddress::where(['user_id' => session('user')->id, 'is_default' => 1])->update(['is_default' => 0]);
		return ExpressAddress::where(['id' => $id, 'user_id' => session('user')->id])->update(['is_default' => 1]);
	}

	/**
	 * 删除收货地址
	 *
	 * @return App\Models\Category
	 */
	public static function destroy($id) {
		return ExpressAddress::where(['id' => $id, 'user_id' => session('user')->id])->delete();
	}

	/**
	 * 查询收货地址
	 *
	 * @return App\Models\Category
	 */
	public static function getList($user_id = 0) {
		return ExpressAddress::join('areas as a', 'express_address.province', '=', 'a.id')
			->join('areas as b', 'express_address.city', '=', 'b.id')
			->join('areas as c', 'express_address.area', '=', 'c.id')
			->where('express_address.user_id', $user_id)
			->orderBy('express_address.is_default', 'desc')
			->select('express_address.id', 'express_address.to_user_name', 'express_address.mobile', 'express_address.address', 'express_address.is_default', 'a.area_name as province_name', 'b.area_name as city_name', 'c.area_name as area_name')
			->get();
	}
}
