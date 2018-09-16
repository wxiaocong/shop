@include('users.inc.header')
<style type="text/css">
body{
    background-color:#fff;
}
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
button.weui-vcode-btn{
    width: 108px;
    font-size: 14px;
}
</style>
<header class="zyw-header">
    <div class="zyw-container white-color">
        <div class="head-l"><a href="javascript:{{$_COOKIE['lastRecord'] ?? "self.location='/'"}};" target="_self"><i class="iconfont icon-fanhui1"></i></a></div>
        <h1>注册</h1>
        <div class="head-r"><a href="/login">登录</a></div>
    </div>
</header>
<section class="zyw-container">
    <textarea id="pubkey" class="pubkey hidden">-----BEGIN PUBLIC KEY-----MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCX9KeF+LPmJL5S4krtnDHqWG3xudzkeWDvjLHkXGECKIA66u5Zg2n1RiPdccZnW/4SNp7gpnjW4noFuDcLrYfQkppuWkIW324jqUHH2tclMMr2eAOq0LLFKSFn1Hs97Bf/sWoklDKwt+JRgtFhMRiENspM/c9dYtjSe5F7kq9JKwIDAQAB-----END PUBLIC KEY-----</textarea>
    <div class="weui-cell">
        <div class="weui-cell__hd"><label class="weui-label">手机号</label></div>
        <div class="weui-cell__bd">
            <input class="weui-input" id="phone" type="number" maxlength="11" oninput="if(value.length>11)value=value.slice(0,11)" pattern="[0-9]*" placeholder="请输入手机号">
        </div>
    </div>
    <div class="weui-cell weui-cell_vcode">
        <div class="weui-cell__hd">
            <label class="weui-label">图形验证码</label>
        </div>
        <div class="weui-cell__bd">
            <input class="weui-input" id="captcha" maxlength="4" type="text" placeholder="请输入验证码">
        </div>
        <div class="weui-cell__ft">
            <img onclick="changeCaptcha()" src="{{ $captcha }}" style="width:108px" id="captchaImg" />
        </div>
    </div>
    <div class="weui-cell weui-cell_vcode">
        <div class="weui-cell__hd">
            <label class="weui-label">手机验证码</label>
        </div>
        <div class="weui-cell__bd">
            <input class="weui-input" id="phone_code" type="number" oninput="if(value.length>4)value=value.slice(0,4)" placeholder="请输入验证码">
        </div>
        <div class="weui-cell__ft">
            <button class="weui-vcode-btn theme-color"  data='/register/sendSmsCode'>获取验证码</button>
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__hd"><label class="weui-label">密码</label></div>
        <div class="weui-cell__bd">
            <input class="weui-input" id="password" maxlength="20" type="password" placeholder="请输入6-20位密码">
            <i class="iconfont icon-buxianshimima"></i>
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__hd"><label class="weui-label">确认密码</label></div>
        <div class="weui-cell__bd">
            <input class="weui-input" id="check_password" type="password" placeholder="确认密码">
            <i class="iconfont icon-buxianshimima"></i>
        </div>
    </div>
    <div class="weui-cell"></div>
<!--     <label for="weuiAgree" class="weui-agree"> -->
<!--         <input id="weuiAgree" type="checkbox" class="weui-agree__checkbox"> -->
<!--         <span class="weui-agree__text"> -->
<!--         阅读并同意<a href="javascript:void(0);">《相关条款》</a> -->
<!--       </span> -->
<!--     </label> -->
    <div class="login-btn"><a href="javascript:;" class="weui-btn weui-btn_warn theme-bgcolor register-submit">注册</a></div>
</section>
<script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
<script src="{{asset('js/users/bootstrap.min.js')}}"></script>
<script src="{{ asset('lib/jsencrypt/jsencrypt.min.js') }}"></script>
<script src="{{asset('js/users/login.js')}}"></script>
</body>
</html>