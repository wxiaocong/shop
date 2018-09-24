<?php

namespace App\Services\Users;

use App\Daoes\Users\UserDao;

class UserService {
	/**
	 * 根据openid查询用户
	 * @param  string $openid
	 *
	 * @return App\Models\Users\User
	 */
	public static function findByOpenid($openid) {
		return UserDao::findByOpenid($openid);
	}

	/**
	 * 根据phone查询用户
	 * @param  string $phone
	 *
	 * @return App\Models\Users\User
	 */
	public static function findByPhone($phone) {
		return UserDao::findByPhone($phone);
	}

	public static function addUser($data) {
		return UserDao::addUser($data);
	}

	/**
	 * 保存更新user
	 * @param array $request
	 * @param number $id
	 */
	public static function saveOrUpdate($openid, $request) {
		return UserDao::saveOrUpdate($openid, $request);
	}

	/**
	 * 根据Id查询用户
	 * @param int $id
	 *
	 */
	public static function findById($id) {
		return UserDao::findById($id);
	}

	/**
	 * 获取用户
	 * @param int $id
	 *
	 */
	public static function getById($id) {
		return UserDao::getById($id);
	}

	/**
	 * 判断某个字段是否已经存在某个值
	 * @param  int $id
	 *
	 * @return boolean
	 */
	public static function existColumn($key, $name, $id = 0) {
		$result = UserDao::existColumn($key, $name, $id);
		if ($result) {
			return array(
				'code' => 500,
				'messages' => array('昵称已存在'),
				'url' => '',
			);
		}
	}

	/**
	 * 分页查询
	 * @param  int $curPage
	 * @param  int $pageSize
	 * @param  array $params
	 *
	 * @return array
	 */
	public static function findByPageAndParams($curPage, $pageSize, $params = array()) {
		return UserDao::findByPageAndParams($curPage, $pageSize, $params);
	}

	/**
	 * 保存
	 * @param  App\Models\Users\User $user
	 *
	 * @return array
	 */
	public static function update($user) {
		$user = UserDao::save($user, null);
		if (!$user) {
			return array(
				'code' => 500,
				'messages' => array('更新用户失败'),
				'url' => '',
			);
		}

		return array(
			'code' => 200,
			'messages' => array('更新用户成功'),
			'url' => '',
		);
	}
}
