@include('users.inc.header')
<link href="{{ asset('lib/blueimp-file-upload/css/jquery.fileupload.css') }}" rel="stylesheet">
<style type="text/css">
body{
    background-color:#fff;
}
.weui-cells{
    font-size:14px !important;
}
.weui-cells .weui-cell{
    border-bottom:none;
}
.weui-cell__ft,.weui-input{
    text-align:right;
    font-size: inherit;
}
p{
    margin:3px 0;
}
button.weui-btn_warn{
    bottom: 0;
    position: fixed;
    border-radius: 0;
    z-index:99;
}
.headimg{
    width: 60px;
    height: 60px;
    border-radius: 60px;
}
.toolbar, .toolbar .title {
    font-size: 1.1rem;
}
</style>
<header class="zyw-header">
    <div class="zyw-container white-color">
        <div class="head-l"><a href="javascript:history.back(-1);" target="_self"><i class="iconfont icon-fanhui1"></i></a></div>
        <h1>代理商申请</h1>
    </div>
</header>
<section class="zyw-container">
    <form action="" id="agent_form">
    <div class="weui-cells__title">选择合伙人</div>
    <div class="weui-cells weui-cells_radio">
        @foreach($agent as $k => $v)
        <label class="weui-cell weui-check__label" for="x{{$k}}">
          <div class="weui-cell__bd">
            <p>{{$v->type_name}}</p>
            <div>保证金：{{sprintf("%.2f", $v->price/100)}}</div>
          </div>
          <div class="weui-cell__ft">
            <input type="radio" class="weui-check" name="level" value="{{$v->id}}" id="x{{$k}}" @if($k == 0) checked="checked"@endif>
            <span class="weui-icon-checked"></span>
          </div>
        </label>
        @endforeach
    </div>
    <div class="weui-cells__title">用户信息</div>
    <div class="weui-cells weui-cells_form">
      <div class="weui-cell">
        <div class="weui-cell__hd"><label class="weui-label">姓名</label></div>
        <div class="weui-cell__bd">
          <input class="weui-input" name="agent_name" type="text" placeholder="请填写姓名">
        </div>
      </div>
      <div class="weui-cell">
        <div class="weui-cell__hd"><label class="weui-label">身份证</label></div>
        <div class="weui-cell__bd">
          <input class="weui-input" name="idCard" type="text" placeholder="请填写身份证">
        </div>
      </div>
      <div class="weui-cell">
        <div class="weui-cell__hd">
          <label class="weui-label">手机号码</label>
        </div>
        <div class="weui-cell__bd">
          <input class="weui-input" name="mobile" type="tel" placeholder="请填写手机号码">
        </div>
      </div>
      <div class="weui-cell">
          <div class="weui-cell__hd">
            <label class="weui-label">所属区域</label>
          </div>
          <div class="weui-cell__bd">
            <input class="weui-input" id="city-picker" type="text" value=""></a>
          </div>
      </div>
      <div class="weui-cell">
          <div class="weui-cell__hd"><label class="weui-label">详细地址</label></div>
          <div class="weui-cell__bd">
            <input class="weui-input" type="text" name="address" maxlength="200" value="" placeholder="请输入详情地址,不包括地区">
          </div>
      </div>
      <div class="weui-cells__title">转账凭证</div>
      <div class="weui-cell">
          <div class="weui-cell__bd">
            <p>支付宝</p>
          </div>
          <div class="weui-uploader__bd">
              <img src="{{ elixir('images/users/alipay.jpg') }}" style="width:150px;height:150px;" >、
          </div>
      </div>
      <div class="weui-cell">
        <div class="weui-cell__hd">
          <label class="weui-label">转账银行</label>
        </div>
        <div class="weui-cell__bd">{{$bank['name']}}</div>
      </div>
      <div class="weui-cell">
        <div class="weui-cell__hd">
          <label class="weui-label">银行卡号</label>
        </div>
        <div class="weui-cell__bd">{{$bank['holder']}}</div>
      </div>
      <div class="weui-cell">
        <div class="weui-cell__hd">
          <label class="weui-label">收款人</label>
        </div>
        <div class="weui-cell__bd">{{$bank['card']}}</div>
      </div>
      <div class="weui-cells__title">备注</div>
      <div class="weui-cells weui-cells_form">
        <div class="weui-cell">
          <div class="weui-cell__bd">
            <textarea class="weui-textarea" name="remark" placeholder="选填" rows="3"></textarea>
          </div>
        </div>
      </div>
    </div>
    <button class="weui-btn weui-btn_warn theme-bgcolor agent-submit">立即申请</button>
    </form>
</section>
<script src="{{ elixir('js/users/jquery.min.js') }}"></script>
<script src="{{ elixir('js/users/jquery-weui.min.js') }}"></script>
<script src="{{asset('js/users/city-picker.js')}}"></script>
<script src="{{ elixir('js/common/fileUpload.js') }}"></script>
<script type="text/javascript">
$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
$(".front_identity_card,.back_identity_card,.transfer_voucher").cnFileUpload();
$("#city-picker").cityPicker({
    title: "请选择地址"
});
$(".agent-submit").click(function() {
    var buttons = $(this);
    var oldBtnText = buttons.text();
    //省市区
    province = city = area = 0;
    var codes = $('#city-picker').attr('data-codes');
    if(codes) {
        region = codes.split(',');
        province = region[0];
        city = region[1];
        area = region[2];
    }
    if($("input[name='agent_name']").val().length < 1) {
        $.toast("填写代理商姓名", "cancel");
        return false;
    }
    var mobile = $("input[name='mobile']").val();
    if( ! isPoneAvailable(mobile)) {
            $.toast("手机号格式错误", "cancel");
            return false;
    }
    if($("input[name='address']").val().length < 1) {
        $.toast("填写代理商地址", "cancel");
        return false;
    }
    $.ajax({
       url:  "/agent",
       data: $('#agent_form').serialize()+'&province='+province+'&city='+city+'&area='+area,
       type: 'POST',
       dataType: 'json',
       beforeSend: function() {
           buttons.attr('disabled', 'true').text('提交中...');
       },
       success: function(jsonObject) {
           if (jsonObject.code == 200) {
               $.toast(jsonObject.messages);
               setTimeout(function(){
                   window.location.href = jsonObject.url;
               },1000)
           } else {
               $.toast(jsonObject.messages, "cancel");
           }
           buttons.removeAttr('disabled').text(oldBtnText);
       },
       error: function(xhr, type) {
           buttons.removeAttr('disabled').text(oldBtnText);
           $.toast("提交异常,请重试", "cancel");
       }
    });
    return false;
});
function isPoneAvailable(str) {
    var myreg=/^[1][3,4,5,6,7,8,9][0-9]{9}$/;
    if (!myreg.test(str)) {
        return false;
    } else {
        return true;
    }
}
</script>
</body>
</html>
