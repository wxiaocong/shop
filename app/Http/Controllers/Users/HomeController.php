<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UsersRequest;
use App\Services\Users\UserService;
use Session;

class HomeController extends Controller {
	public function index() {
		$data['userInfo'] = UserService::findById(session('user')->id);
		$data['levelArr'] = config('statuses.user.levelState');
		return view('users.home', $data);
	}

	//个人信息
	public function show() {
		$userInfo = UserService::findById(session('user')->id);
		return view('users.person', array('userInfo' => $userInfo));
	}

	//保存个人信息
	public function update(UsersRequest $request, int $id) {
		$id = session('user')->id;
		$result = UserService::existColumn('nickname', $request['nickname'], $id);
		if ($result) {
			return response()->json($result);
		}
		if ($request['sex'] == '男') {
			$request['sex'] = 1;
		} elseif ($request['sex'] == '女') {
			$request['sex'] = 2;
		} else {
			$request['sex'] = 0;
		}
		//日期控件默认值问题，结尾有空格
		$request['birthday'] = empty(trim($request['birthday'])) ? null : trim($request['birthday']);

		if (UserService::getById($id)->update($request->all())) {
			Session(array('user' => UserService::findById($id)));
			return response()->json(array(
				'code' => 200,
				'messages' => array('保存成功'),
				'url' => '/home',
			));
		} else {
			return response()->json(array(
				'code' => 500,
				'messages' => array('保存失败'),
				'url' => '',
			));
		}
	}
}
