<?php

namespace App\Http\Controllers\Users;

use App\Daoes\SmsLogDao;
use App\Http\Controllers\Controller;
use App\Services\CaptchaService;
use App\Services\SmsLogService;
use App\Services\Users\UserService;
use App\Services\Users\WechatUserService;
use EasyWeChat;
use Hash;
use RSA;
use Session;

class RegisterController extends Controller
{
    public function index()
    {
        $data = array();
        return view('users.register', $data)->with('captcha', CaptchaService::getCaptcha());
    }

    public function doRegister()
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
        if (!empty($userInfo)) {
            return response()->json(
                array(
                    'code'     => 500,
                    'messages' => array('用户已存在'),
                    'url'      => '',
                )
            );
        }
        $password = RSA::decrypt($password);
        if (strlen($password) < 6) {
            return response()->json(
                array(
                    'code'     => 500,
                    'messages' => array('密码至少6位'),
                    'url'      => '',
                )
            );
        }

        //短信验证码校验
        $smsCode = SmsLogDao::findByMobile($phone, getRealIp());
        if (!$smsCode || $phone_code != $smsCode->content) {
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
        //添加用户
        $data = array(
            'created_at' => date('Y-m-d H:i:s'),
            'mobile'     => $phone,
            'password'   => Hash::make(env('USER_PASSWORD_SALT') . $password),
            'last_time'  => date('Y-m-d H:i:s'),
            'last_ip'    => getRealIp(),
        );
        //微信写入用户信息，绑定用户
        if (isWeixin()) {
            if (empty(session('openid'))) {
                //微信浏览器获取用户openid
                $app   = EasyWeChat::officialAccount();
                $oauth = $app->oauth;
                session(array('target_url' => config('app.url') . '/register'));
                return $oauth->redirect();
            } else {
                $wechatUserInfo = WechatUserService::findByOpenid(session('openid'));
                if (!empty($wechatUserInfo)) {
                    $data['headimgurl'] = $wechatUserInfo->headimgurl;
                    $data['nickname']   = $wechatUserInfo->nickname;
                    $data['openid']     = $wechatUserInfo->openid;
                }
            }
        }
        UserService::addUser($data);
        $userInfo = UserService::findByPhone($phone);
        //绑定用户
        if (isWeixin() && session('openid')) {
            WechatUserService::updateUserBind(session('openid'), $userInfo->id);
        }
        Session(array('user' => $userInfo));
        Session::forget('captcha');
        return response()->json(
            array(
                'code'     => 200,
                'messages' => array('注册成功'),
                'url'      => env('APP_URL'),
            )
        );
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
        if (!empty($userInfo)) {
            return response()->json(
                array(
                    'code'          => 500,
                    'messages'      => array('用户已存在'),
                    'url'           => '',
                    'effectiveTime' => 0,
                )
            );
        }

        $smsResult = SmsLogService::sendPhoneCode($phone);
        return response()->json($smsResult);
    }
}
