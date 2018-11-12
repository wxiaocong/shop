<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UsersRequest;
use App\Services\Users\UserService;
use App\Services\PayLogsService;
use App\Services\AgentService;
use App\Services\AgentTypeService;
use App\Services\WithdrawService;
use EasyWeChat;
use Session;
use App\Utils\Page;

class HomeController extends Controller {
    public function index() {
        $data['userInfo'] = UserService::findById(session('user')->id);
        $data['levelArr'] = config('statuses.user.levelState');
        $data['agent'] = AgentService::findByUserId(session('user')->id);
        return view('users.home', $data);
    }

    //个人信息
    public function show() {
        $userInfo = UserService::findById(session('user')->id);
        return view('users.person')->with('userInfo', $userInfo)
            ->with('bank_no', config('system.bank_no'));
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
        $userInfo = UserService::findById($id);
        if (UserService::getById($id)->update($request->all())) {
            Session(array('user' => UserService::findById($id)));
            //修改昵称更新二维码
            if ($request['nickname'] != $userInfo->nickname) {
                unlink('./shareImg/'.$id.'.jpg');
            }
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
        $data['childId'] = $childId = intval(request('child', 0));
        $data['teamType'] = $type = intval(request('type'));
        $levelType = config('statuses.user.levelType');
        if (in_array($type, array_keys($levelType))) {
            //如果查询下级，检查childId是还否是当前用户下级
            if ($childId > 0) {
                $childInfo = UserService::findById($childId);
                if (empty($childInfo) || $childInfo->referee_id != session('user')->id) {
                    abort(404, '该会员不是您的下级!');
                }
            }
            $data['team'] = UserService::getTeam($type, $childId);
        }
        $data['levelState'] = config('statuses.user.levelState');
        $data['agentState'] = AgentTypeService::getAll();
        return view('users.team', $data);
    }

    //提现
    public function withdraw() {
        $data['bank_no'] = config('system.bank_no');
        $data['userInfo'] = UserService::findById(session('user')->id);
        if(empty($data['userInfo']->realname) || empty($data['userInfo']->bank_code) || empty($data['userInfo']->enc_bank_no)) {
            abort('404', '请先到个人中心完善银行卡信息');
        }
        return view('users.withdraw', $data);
    }

    //提现记录
    public function record() {
        return view('users.withdrawRecord')->with('pageSize',20);
    }


    public function getWithdraw()
    {
        $curPage = trimSpace(request('curPage', 1));
        $pageSize = trimSpace(request('pageSize', 20));
        $data['record'] = WithdrawService::getAllByUser(session('user')->id, $curPage, $pageSize);
        
        return view('users.withdrawRecordData', $data);
    }

}
