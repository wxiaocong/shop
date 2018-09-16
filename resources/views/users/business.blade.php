@include('users.inc.header')
<link href="{{ asset('lib/blueimp-file-upload/css/jquery.fileupload.css') }}" rel="stylesheet">
<style type="text/css">
body{
    background-color:#fff;
}
.weui-cells{
    font-size:14px !important;
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
        <div class="head-l"><a href="javascript:{{$_COOKIE['lastRecord'] ?? "self.location='/'"}};" target="_self"><i class="iconfont icon-fanhui1"></i></a></div>
        <h1>商家信息</h1>
    </div>
</header>
<section class="zyw-container">
    <form action="" id="edit_business_form">
    <div class="home-cont weui-cells mb-625">
        <div class="weui-cell weui-cell_access">
          <div class="weui-cell__bd">
            <p>公司名称：</p>
          </div>
          <div class="weui-cell__hd">
            @if($userInfo->business_audit_state == 0 || $userInfo->business_audit_state == 3)
            <input type="text" name="company_name"  class="weui-input" maxlength="200" value="{{ $userInfo->company_name }}" />
            @else
            {{ $userInfo->company_name }}
            @endif
          </div>
        </div>
        <div class="weui-cell weui-cell_access">
          <div class="weui-cell__bd">
            <p>所属区域：</p>
          </div>
          <div class="weui-cell__hd">
            @if($userInfo->business_audit_state == 0 || $userInfo->business_audit_state == 3)
            <a class="weui-cell__ft"><input class="weui-input" id="city-picker" type="text" value="{{ $userInfo->region }}"></a>
            @else
            {{ $userInfo->region }}
            @endif
          </div>
        </div>
        <div class="weui-cell weui-cell_access">
          <div class="weui-cell__bd"><p>公司地址：</p></div>
          <div class="weui-cell__hd">
            @if($userInfo->business_audit_state == 0 || $userInfo->business_audit_state == 3)
            <input class="weui-input" type="text" name="company_address" maxlength="200" value="{{ $userInfo->company_address }}" placeholder="请输入详情地址,不包括地区">
            @else
            {{ $userInfo->company_address }}
            @endif
          </div>
        </div>
        <div class="weui-cell weui-cell_access">
          <div class="weui-cell__bd">
            <p>店铺工位：</p>
          </div>
          <div class="weui-cell__hd">
            @if($userInfo->business_audit_state == 0 || $userInfo->business_audit_state == 3)
            <input type="number" name="shop_site" maxlength="10" class="weui-input" value="{{ $userInfo->shop_site }}" />
            @else
            {{ $userInfo->shop_site }}
            @endif
          </div>
        </div>
        <div class="weui-cell">
          <div class="weui-cell__bd">
            <p src="{{ $userInfo->business_license }}">营业执照：</p>
          </div>
          <div class="weui-uploader__bd">
              @if($userInfo->business_audit_state == 0)
              <img class="business_license"  name="business_license"  style="width:150px;height:100px;" >
              @else
              <img class="business_license"  name="business_license" src="{{$userInfo->business_license}}" style="width:150px;height:100px;" >
              @endif
          </div>
        </div>
        <div class="weui-cell">
          <div class="weui-cell__bd">
            <p>门头照片：</p>
          </div>
          <div class="weui-uploader__bd">
              @if($userInfo->business_audit_state == 0)
              <img class="doorhead_photo"  name="doorhead_photo" style="width:150px;height:100px;" >
              @else
              <img class="doorhead_photo"  name="doorhead_photo" src="{{ $userInfo->doorhead_photo }}" style="width:150px;height:100px;" >
              @endif
          </div>
        </div>
        @if($userInfo->business_audit_state != 0)
        <div class="weui-cell weui-cell_access">
          <div class="weui-cell__bd">
            <p>审核状态：</p>
          </div>
          <div class="weui-cell__hd">
            {{ translateStatus('user.businessAuditState', $userInfo->business_audit_state) }}
          </div>
        </div>
        @endif
    </div>
    @if(in_array($userInfo->business_audit_state,array(0,3)))
    <button class="weui-btn weui-btn_warn theme-bgcolor business-submit">提交</button>
    @endif
    </form>
</section>
<script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
<script src="{{asset('js/users/city-picker.js')}}"></script>

<script src="{{ elixir('js/common/h5FileUpload.js') }}"></script>
<script type="text/javascript">
$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
@if($userInfo->business_audit_state == 0 || $userInfo->business_audit_state == 3)
$(".business_license,.doorhead_photo").cnFileUpload();
@endif
$("#city-picker").cityPicker({
    title: "请选择地址"
});
$(".business-submit").click(function() {
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
    if($("input[name='company_name']").val().length < 1) {
        $.toast("填写公司名称", "cancel");
        return false;
    }
    if($("input[name='company_address']").val().length < 1) {
        $.toast("填写公司地址", "cancel");
        return false;
    }
    if($("input[name='shop_site']").val().length < 1) {
        $.toast("填写店铺工位", "cancel");
        return false;
    }
    if($("input[name='business_license']").val().length < 1) {
        $.toast("请上传营业执照", "cancel");
        return false;
    }
    if($("input[name='doorhead_photo']").val().length < 1) {
        $.toast("请上传门头照片", "cancel");
        return false;
    }
    $.ajax({
       url:  "/home/saveBusiness",
       data: $('#edit_business_form').serialize()+'&company_province='+province+'&company_city='+city+'&company_area='+area,
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
</script>
</body>
</html>
