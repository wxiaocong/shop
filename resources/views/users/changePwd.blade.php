@include('users.inc.header')
<style type="text/css">
body{
    background-color:#fff;
}
.weui-input{
    font-size:13px;
}
.weui-cells{
    margin-top:0;
}
.login-btn{
    margin-top:10px;
}
</style>
<header class="zyw-header">
    <div class="zyw-container white-color">
        <div class="head-l"><a href="javascript:{{$_COOKIE['lastRecord'] ?? "self.location='/'"}};" target="_self"><i class="iconfont icon-fanhui1"></i></a></div>
        <h1>修改密码</h1>
    </div>
</header>
<section class="zyw-container">
    <textarea id="pubkey" class="pubkey hidden">-----BEGIN PUBLIC KEY-----MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCX9KeF+LPmJL5S4krtnDHqWG3xudzkeWDvjLHkXGECKIA66u5Zg2n1RiPdccZnW/4SNp7gpnjW4noFuDcLrYfQkppuWkIW324jqUHH2tclMMr2eAOq0LLFKSFn1Hs97Bf/sWoklDKwt+JRgtFhMRiENspM/c9dYtjSe5F7kq9JKwIDAQAB-----END PUBLIC KEY-----</textarea>
    <div class="weui-cells">
      <div class="weui-cell">
        <div class="weui-cell__bd">
          <input class="weui-input" id="oldPwd" type="password" placeholder="请输入原始密码">
        </div>
      </div>
    </div>
    <div class="weui-cells">
      <div class="weui-cell">
        <div class="weui-cell__bd">
          <input class="weui-input" id="newPwd" type="password" placeholder="请输入新密码">
        </div>
      </div>
    </div>
    <div class="weui-cells">
      <div class="weui-cell">
        <div class="weui-cell__bd">
          <input class="weui-input" id="checkPwd" type="password" placeholder="请再次输入新密码">
        </div>
      </div>
    </div>
    <div class="login-btn"><a href="javascript:;" class="weui-btn weui-btn_warn theme-bgcolor changePwd-submit">提交</a></div>
</section>
<script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
<script src="{{asset('js/users/bootstrap.min.js')}}"></script>
<script src="{{ asset('lib/jsencrypt/jsencrypt.min.js') }}"></script>
<script src="{{asset('js/users/login.js')}}"></script>
</body>
</html>