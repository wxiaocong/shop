<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UsersRequest;
use App\Services\Users\UserService;
use App\Services\PayLogsService;
use EasyWeChat;
use Session;
use App\Utils\Page;

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
    
    //资金记录
    public function fund()
    {
        $userInfo = UserService::findById(session('user')->id);
        return view('users.fund', array('userInfo' => $userInfo));
    }

    public function balance()
    {
        return view('users.balance')->with('pageSize',20);
    }

    public function getPayLogData()
    {
        $curPage = trimSpace(request('curPage', 1));
        $pageSize = trimSpace(request('pageSize', 20));
        $data['payLog'] = PayLogsService::getAllByUser(session('user')->id, $curPage, $pageSize);
        $data['payTypeArr'] = array_column(config('statuses.payLog.payType'),'text','code');
        return view('users.payLogData', $data);
    }
    
    //佣金收入
    public function income()
    {
        $data['pageSize'] = Page::PAGESIZE;
        $data['payType']= intval(request('payType', 0));
        return view('users.income', $data);
    }
    
    //获取资金记录列表数据
    public function getData() {
        $param['userId'] = session('user')->id;
        $param['payType'] = intval(request('payType', 0));
        $param['orderBy'] = array('id' => 'desc');
        $param['pageType'] = config('statuses.payLog.pageType.fund');
        
        $curPage = trimSpace(request('curPage', 1));
        $pageSize = trimSpace(request('pageSize', Page::PAGESIZE));
        
        $data['fundList'] = PayLogsService::findByPage($curPage, $pageSize, $param);
        $data['payTypeArr'] = array_column(config('statuses.payLog.payType'),'text','code');
        return view('users.incomeData', $data);
    }
    
    //我的团队
    public function myTeam() {
        $data['teamType'] = $type = intval(request('type'));
        $levelType = config('statuses.user.levelType');
        if (in_array($type, array_keys($levelType))) {
            $data['team'] = UserService::getTeam($type);
        }
        $data['levelState'] = config('statuses.user.levelState');
        return view('users.team', $data);
    }

    //提现
    public function withdraw() {
        $data['userInfo'] = UserService::findById(session('user')->id);
        return view('users.withdraw', $data);
    }

}
