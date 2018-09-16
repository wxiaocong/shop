@include('users.inc.header')
<style type="text/css">
.weui-input{
    width: calc(100% - 40px);
}
.icon-xianshimima,.icon-buxianshimima{
    font-size:20px;
    padding: 4px;
}
.head-r a{
    color:#252525;
}
.captcha-cell{
    padding:0 0 0 15px;
    height:49px;
}
.weui-cell__ft{
    height:100%;
}
#captchaImg{
    height:100%;
    width:108px
}
</style>
<link href="{{ elixir('css/users/login.css') }}" rel="stylesheet">
<header class="zyw-header">
    <div class="zyw-container white-color">
        <div class="head-l"><a href="javascript:{{$_COOKIE['lastRecord'] ?? "self.location='/'"}};" target="_self"><i class="iconfont icon-fanhui1"></i></a></div>
        <h1>登录</h1>
        <div class="head-r"><a href="/register">注册</a></div>
    </div>
</header>
<section class="zyw-container">
    <div class="login-img"><img src="{{ $wechatUserInfo->headimgurl ?? elixir('images/users/mylogo.png') }}" alt=""></div>
    <textarea id="pubkey" class="pubkey hidden">-----BEGIN PUBLIC KEY-----MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCX9KeF+LPmJL5S4krtnDHqWG3xudzkeWDvjLHkXGECKIA66u5Zg2n1RiPdccZnW/4SNp7gpnjW4noFuDcLrYfQkppuWkIW324jqUHH2tclMMr2eAOq0LLFKSFn1Hs97Bf/sWoklDKwt+JRgtFhMRiENspM/c9dYtjSe5F7kq9JKwIDAQAB-----END PUBLIC KEY-----</textarea>
    <div class="weui-cells">
        <div class="weui-cell">
            <div class="weui-cell__bd">
                <input class="weui-input" id="phone" type="number" oninput="if(value.length>11)value=value.slice(0,11)" pattern="[0-9]*" placeholder="请输入手机号">
            </div>
        </div>
    </div>
    <div class="weui-cells">
        <div class="weui-cell">
            <div class="weui-cell__bd">
                <input class="weui-input" id="password" type="password" placeholder="请输入密码">
                <i class="iconfont icon-buxianshimima"></i>
            </div>
        </div>
    </div>
    <div class="weui-cells">
        <div class="weui-cell captcha-cell">
            <div class="weui-cell__bd">
                <input class="weui-input" id="captcha" type="text" maxlength="4" placeholder="请输入验证码">
            </div>
            <div class="weui-cell__ft">
                <img onclick="changeCaptcha()" src="{{ $captcha }}" id="captchaImg" />
            </div>
        </div>
    </div>
    <div class="weui-cells__tips text-right"><a href="/login/findPwd">忘记密码？</a></div>
    <div class="login-btn"><a href="javascript:;" class="weui-btn weui-btn_warn theme-bgcolor login-submit">登录</a></div>
</section>
<script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
<script src="{{ asset('lib/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('lib/jsencrypt/jsencrypt.min.js') }}"></script>
<script src="{{asset('js/users/login.js')}}"></script>
</body>
</html>