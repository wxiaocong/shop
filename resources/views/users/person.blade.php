@include('users.inc.header')
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
        <div class="head-l"><a href="javascript:history.back(-1);" target="_self"><i class="iconfont icon-fanhui1"></i></a></div>
        <h1>个人信息</h1>
    </div>
</header>
<section class="zyw-container">
    <form action="" id="edit_person_form">
    <div class="home-cont weui-cells mb-625">
          <div class="weui-cell">
            <div class="weui-cell__bd">
              <p>头像</p>
            </div>
            <div class="weui-cell__ft">
                <img class="headimg" src="{{ $userInfo->headimgurl ?? elixir('images/users/mylogo.png') }}">
            </div>
          </div>
          <div class="weui-cell weui-cell_access">
            <div class="weui-cell__bd">
              <p>昵称</p>
            </div>
            <div class="weui-cell__bd">
            <input type="text" name="nickname"  class="weui-input" value="{{ $userInfo->nickname ?? '' }}" />
            </div>
          </div>
          <div class="weui-cell weui-cell_access">
            <div class="weui-cell__bd">
              <p>姓名</p>
            </div>
            <div class="weui-cell__bd">
            <input type="text" name="realname"  class="weui-input" value="{{ $userInfo->realname ?? '' }}" />
            </div>
          </div>
          <div class="weui-cell weui-cell_access">
            <div class="weui-cell__bd">
              <p>手机</p>
            </div>
            <div class="weui-cell__bd">
            <input type="text" name="mobile"  class="weui-input" value="{{ $userInfo->mobile ?? '' }}" />
            </div>
          </div>
          <div class="weui-cell weui-cell_access">
            <div class="weui-cell__bd">
              <p>邮箱</p>
            </div>
            <div class="weui-cell__bd">
            <input type="email" name="email" maxlength="60"  class="weui-input" value="{{ $userInfo->email }}" />
            </div>
          </div>
          <div class="weui-cell weui-cell_access">
            <div class="weui-cell__bd">
              <p>性别</p>
            </div>
            <div class="weui-cell__ft"><input class="weui-input" name="sex" id="picker-sex" type="text" value="@if ($userInfo->sex==1)男@elseif ($userInfo->sex==2) 女@endif"></div>
          </div>
          <div class="weui-cell weui-cell_access">
            <div class="weui-cell__bd">
              <p>生日</p>
            </div>
            <div class="weui-cell__ft">
                <input type="text" name="birthday" class="weui-input" id='datetime-picker' value="" />
            </div>
          </div>
    </div>
    <button class="weui-btn weui-btn_warn theme-bgcolor person-submit">保存</button>
    </form>
</section>
<script src="{{ elixir('js/users/jquery.min.js') }}"></script>
<script src="{{ elixir('js/users/jquery-weui.min.js') }}"></script>
<script type="text/javascript">
$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
$("#picker-sex").picker({
  title: "请选择性别",
  cols: [
    {
      textAlign: 'center',
      values: ['男', '女']
    }
  ]
});
 $("#datetime-picker").datetimePicker({
     times:function(){return false;},
     @if($userInfo->birthday)
     value:"{{$userInfo->birthday}} "
     @endif
});
$(".person-submit").click(function() {
    var buttons = $(this);
    var oldBtnText = buttons.text();
    //验证邮箱
    if(!isEmail($("input[name='email']"))) {
        $.toast('邮箱格式错误', "cancel");
        return false;
    }
    $.ajax({
       url:  "/home/1",
       data: $('#edit_person_form').serialize(),
       type: 'PATCH',
       dataType: 'json',
       beforeSend: function() {
           buttons.attr('disabled', 'true').text('修改中...');
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
});

function isEmail(str){
    if(str)
        return true;
    var reg=new RegExp(/^([a-zA-Z0-9._-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/);
    return reg.test(str);
}
</script>
</body>
</html>
