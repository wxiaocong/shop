@include('users.inc.header')
<style type="text/css">
body{
    background-color:#fff;
}
a{
    color: #e93b3d;
}
</style>
<div class="weui-msg">
  <div class="weui-msg__icon-area"><img style="width:180px;" src="{{ elixir('images/users/crying.png') }}"></div>
  <div class="weui-msg__text-area">
    <p class="weui-msg__desc">{{$exception->getMessage()}}</p>
  </div>
  <div class="weui-msg__opr-area">
    <p class="weui-btn-area">
        <span>您可以回到 <a href="/">网站首页</a> 或 <a href="javascript:window.history.back(-1)">返回上一页</a></span>
    </p>
  </div>
</div>
@include('users.inc.footer')