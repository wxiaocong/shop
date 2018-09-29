<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Services\CaptchaService;
use App\Services\Users\UserService;
use App\Services\SmsLogService;
use App\Services\Users\WechatUserService;
use App\Daoes\SmsLogDao;
use Hash;
use RSA;
use Session;
use EasyWeChat;
//以下测试用
use App\Models\WechatNotify;
use App\Models\Users\WechatUser;
use App\Models\OrderRefund;

class LoginController extends Controller
{
    public function showLog()
    {
        echo file_get_contents('../storage/logs/laravel-' . date('Y-m-d') . '.log');
    }

    public function clearLog()
    {
        echo file_put_contents('../storage/logs/laravel-' . date('Y-m-d') . '.log', '');
    }
    public function show()
    {
        \Log::error('未知位置：' . request('login'));
    }
    public function showNotify()
    {
        $res = WechatNotify::orderBy('id', 'desc')->limit(20)->get();
        foreach ($res as $val) {
            echo $val->created_at;
            var_dump($val->message);
        }

        //退款记录
        $res = OrderRefund::orderBy('id', 'desc')->limit(10)->get();
        foreach ($res as $val) {
            dd($val);
        }
    }

    public function index()
    {
        if (empty(session('openid'))) {
            //微信浏览器获取用户openid
            if (isWeixin()) {
                $app   = EasyWeChat::officialAccount();
                $oauth = $app->oauth;
                session(array('target_url' => config('app.url') . '/login'));
                return $oauth->redirect();
            } else {
                $wechatUserInfo = null;
            }
        } else {
            //openid已绑定自动登录
            if (empty(session('user')->id)) {
                $wechatUserInfo = WechatUserService::findByOpenid(session('openid'));
                if (!empty($wechatUserInfo->user_id)) {
                    session(array('user' => UserService::findById($wechatUserInfo->user_id)));
                    return redirect(env('APP_URL'));
                }
            }
        }
        $captcha = CaptchaService::getCaptcha();
        return view('users.login', compact('captcha', 'wechatUserInfo'));
    }

    public function doLogin()
    {
        $phone    = trim(request('phone', ''));
        $password = trim(request('password', ''));
        $captcha  = trim(request('captcha', ''));

        $rightCaptcha = session('captcha', '');
        if (strtoupper($rightCaptcha) !== strtoupper($captcha)) {
            return response()->json(
                array(
                    'code'     => 500,
                    'messages' => array('验证码出错'),
                    'url'      => '',
                )
            );
        }
        if (!preg_match("/^1[34578]{1}\d{9}$/", $phone)) {
            return response()->json(
                array(
                    'code'     => 500,
                    'messages' => array('手机号格式错误'),
                    'url'      => '',
                )
            );
        }
        $userInfo = UserService::findByPhone($phone);
        if (empty($userInfo)) {
            return response()->json(
                array(
                    'code'     => 500,
                    'messages' => array('用户不存在或被锁定'),
                    'url'      => '',
                )
            );
        }
        $password = RSA::decrypt($password);
        if (!Hash::check(env('USER_PASSWORD_SALT') . $password, $userInfo->password)) {
            //记录登录错误
            UserService::findById($userInfo['id'])->increment('error_login', 1, array('error_ip' => getRealIp(), 'error_time' => date('Y-m-d H:i:s')));
            return response()->json(
                array(
                    'code'     => 500,
                    'messages' => array('密码不正确'),
                    'url'      => '',
                )
            );
        }
        //是否被锁定
        if ($userInfo->state != 1) {
            return response()->json(
                array(
                    'code'     => 500,
                    'messages' => array('用户被锁定'),
                    'url'      => '',
                )
            );
        }

        $updateUserData = array('last_time' => date('Y-m-d H:i:s'), 'last_ip' => getRealIp());
        //如果是微信登录，有openid，绑定用户
        if (!empty(session('openid')) && isWeixin()) {
            WechatUserService::updateUserBind(session('openid'), $userInfo->id);
            //更新用户表信息,登录更新
            $wechatInfo = WechatUserService::findByOpenid(session('openid'));

            $userInfo->headimgurl = $updateUserData['headimgurl'] = $wechatInfo->headimgurl;
            $userInfo->openid     = $updateUserData['openid']     = session('openid');
            if (empty($userInfo->nickname)) {
                $userInfo->nickname = $updateUserData['nickname'] = $wechatInfo->nickname;
            }
        }
        //更新登录记录
        UserService::getById($userInfo['id'])->increment('total_login', 1, $updateUserData);
        //将登录用户存储到session
        Session(array('user' => $userInfo));
        Session::forget('captcha');
        return response()->json(
            array(
                'code'     => 200,
                'messages' => array('登录成功'),
                'url'      => '/',
            )
        );
    }

    //找回密码页面
    public function findPwd()
    {
        return view('users.findPwd')->with('captcha', CaptchaService::getCaptcha());
    }

    //忘记密码
    public function changePwd()
    {
        $phone      = trim(request('phone', ''));
        $password   = request('password', '');
        $captcha    = trim(request('captcha', ''));
        $phone_code = trim(request('phone_code', ''));

        $rightCaptcha = session('captcha', '');
        if (strtoupper($rightCaptcha) !== strtoupper($captcha)) {
            return response()->json(
                array(
                    'code'     => 500,
                    'messages' => array('图形验证码错误'),
                    'url'      => '',
                )
            );
        }
        if (!preg_match("/^1[34578]{1}\d{9}$/", $phone)) {
            return response()->json(
                array(
                    'code'     => 500,
                    'messages' => array('手机号格式错误'),
                    'url'      => '',
                )
            );
        }
        $userInfo = UserService::findByPhone($phone);
        if (empty($userInfo)) {
            return response()->json(
                array(
                    'code'     => 500,
                    'messages' => array('用户不存在'),
                    'url'      => '',
                )
            );
        }
        $password = RSA::decrypt($password);
        if (strlen($password) < 6 || strlen($password) > 20) {
            return response()->json(
                array(
                    'code'     => 500,
                    'messages' => array('请输入6-20位密码'),
                    'url'      => '',
                )
            );
        }
        //短信验证码校验
        $smsCode = SmsLogDao::findByMobile($phone, getRealIp());
        if (empty($smsCode) || $phone_code != $smsCode->content) {
            return response()->json(
                array(
                    'code'     => 500,
                    'messages' => array('手机验证码错误'),
                    'url'      => '',
                )
            );
        }
        //更新验证码为已使用
        SmsLogDao::userdCode($phone, $phone_code);

        Session::forget('captcha');
        $user           = UserService::findById($userInfo['id']);
        $user->password = Hash::make(env('USER_PASSWORD_SALT') . $password);
        if ($user->save()) {
            return response()->json(
                array(
                    'code'     => 200,
                    'messages' => array('修改密码成功'),
                    'url'      => '/login',
                )
            );
        } else {
            return response()->json(
                array(
                    'code'     => 500,
                    'messages' => array('修改密码失败'),
                    'url'      => '',
                )
            );
        }
    }

    //获取手机验证码
    public function sendSmsCode()
    {
        $phone   = trim(request('phone', ''));
        $captcha = trim(request('captcha', ''));

        if (!preg_match("/^1[34578]{1}\d{9}$/", $phone)) {
            return response()->json(
                array(
                    'code'          => 500,
                    'messages'      => array('手机号格式错误'),
                    'url'           => '',
                    'effectiveTime' => 0,
                )
            );
        }

        $rightCaptcha = session('captcha', '');
        if (strtoupper($rightCaptcha) !== strtoupper($captcha)) {
            return response()->json(
                array(
                    'code'          => 500,
                    'messages'      => array('图形验证码错误!'),
                    'url'           => '',
                    'effectiveTime' => 0,
                )
            );
        }

        $userInfo = UserService::findByPhone($phone);
        if (empty($userInfo)) {
            return response()->json(
                array(
                    'code'          => 500,
                    'messages'      => array('用户不存在'),
                    'url'           => '',
                    'effectiveTime' => 0,
                )
            );
        }

        $smsResult = SmsLogService::sendPhoneCode($phone);
        return response()->json($smsResult);
    }

    public function logout()
    {
        //测试用退出取消绑定
//         WechatUser::where('openid', session('openid'))->update(array('user_id' => 0));

        Session::forget('user');
        return redirect('/');
    }
}
