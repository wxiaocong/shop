@include('users.inc.header')
@include('users.inc.shortcut')
<style type="text/css">
body{
    background-color:#fff;
}
.weui-cell__ft,.weui-input{
    text-align:left;
    font-size: 13px !important;
}
p{
    margin:3px 0;
}
button.weui-btn_warn{
    bottom: 0;
    position: fixed;
    border-radius: 0;
    z-index:9;
}
.weui-cells{
    font-size:13px;
}
.weui-cells{
    margin-top:0;
}
.toolbar, .toolbar .title {
    font-size: 1rem;
}
</style>
<header class="zyw-header">
    <div class="zyw-container white-color">
        <div class="head-l"><a href="javascript:window.location.href='{{session('expressReferer') ?? '/address'}}'" target="_self"><i class="iconfont icon-fanhui1"></i></a></div>
        <h1>@if (request()->address) 编辑@else 新增@endif收货地址</h1>
        <div class="head-r"><i class="iconfont icon-gengduo"></i></div>
    </div>
</header>
<section class="zyw-container">
    <form id="edit_address_form">
    <input class="express_address_id" name="id" value="{{ $addressInfo->id ?? '' }}" type="hidden" />
    <div class="home-cont weui-cells mb-625">
        <div class="weui-cells">
          <div class="weui-cell">
            <div>
              <p>收货人：</p>
            </div>
            <div class="weui-cell__bd">
              <input class="weui-input" type="text" maxlength="20" name="to_user_name" value="{{ $addressInfo->to_user_name ?? '' }}" >
            </div>
          </div>
          <div class="weui-cell">
            <div>
              <p>手机号码：</p>
            </div>
            <div class="weui-cell__bd">
              <input class="weui-input"  type="number" maxlength="11" oninput="if(value.length>11)value=value.slice(0,11)" pattern="[0-9]*" name="mobile" value="{{ $addressInfo->mobile ?? '' }}" >
            </div>
          </div>
          <div class="weui-cell">
            <div>
              <p>所在地区：</p>
            </div>
            <div class="weui-cell__bd">
              <input name="region" class="weui-input" id="city-picker" type="text" value="{{ $addressInfo->region ?? '' }}">
            </div>
          </div>
          <div class="weui-cell">
            <div style="width:70px;">
              <p>详情地址：</p>
            </div>
            <div class="weui-cell__bd">
              <input class="weui-input" type="text" maxlength="200" name="address" value="{{ $addressInfo->address ?? '' }}" placeholder="街道、楼牌号等">
            </div>
          </div>
        </div>
    </div>
    <button  class="weui-btn weui-btn_warn theme-bgcolor address-submit">保存</button>
    </form>
</section>
<script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
<script src="{{elixir('js/users/city-picker.js')}}"></script>
<script src="{{elixir('js/users/front.js')}}"></script>
<script type="text/javascript">
$(document).ready(function(e) {
$(this).keydown(function (e){
    if(e.which == "13"){
        $(".address-submit").click();
    }
});
$("#city-picker").cityPicker({
    title: "请选择地址"
}).click(function(){
    $(this).focus();
});
$('.address-submit').click(function(){
    var express_address_id = $('.express_address_id').val();
    var to_user_name = $("input[name='to_user_name']").val().trim();
    var mobile = $("input[name='mobile']").val().trim();
    var address = $("input[name='address']").val().trim();

    //省市区
    province = city = area = 0;
    var codes = $('#city-picker').attr('data-codes');
    if(codes) {
        region = codes.split(',');
        province = region[0];
        city = region[1];
        area = region[2];
    }

    if (to_user_name.length < 2) {
        $.toast("收货人至少2字符", "cancel");
        return false;
    }
    if (to_user_name.length > 20) {
        $.toast("收货人最多20字符", "cancel");
        return false;
    }
    if( ! isPoneAvailable(mobile)) {
        $.toast("手机号格式错误", "cancel");
        return false;
    }
    if(! express_address_id && ! codes) {
        if(province < 1 || city < 1 || area < 1) {
            $.toast("请选择所在地区", "cancel");
            return false;
        }
    }
    if (address.length < 1) {
        $.toast("请输入详情地址", "cancel");
        return false;
    }
    if (address.length > 200) {
        $.toast("输入详情地址最多200字符", "cancel");
        return false;
    }
    var buttons = $(this);
    var oldBtnText = buttons.text();
    var action = $('.express_address_id').val() > 0 ? 'patch' : 'post';
    var url = action == 'patch' ? '/address/' + express_address_id : '/address';

    $.ajax({
        url: url,
        data: {to_user_name:to_user_name,mobile:mobile,province:province,city:city,area:area,address:address},
        type: action,
        dataType: 'json',
        beforeSend: function() {
            buttons.attr('disabled', 'true').text('提交中...');
        },
        success: function(jsonObject) {
            if (jsonObject.code == 200 && jsonObject.url) {
                setTimeout(function(){
                    window.location.replace(jsonObject.url);
                },1000);
                $.toast(jsonObject.messages);
            } else {
                $.toast(jsonObject.messages, "forbidden");
            }
            buttons.removeAttr('disabled').text(oldBtnText);
        },
        error: function(xhr, type) {
            buttons.removeAttr('disabled').text(oldBtnText);
        }
    });
});
function isPoneAvailable(str) {
    var myreg=/^[1][3,4,5,6,7,8,9][0-9]{9}$/;
    if (!myreg.test(str)) {
        return false;
    } else {
        return true;
    }
}
})
</script>
</body>
</html>