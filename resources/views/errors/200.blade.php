@include('users.inc.header')
<style type="text/css">
body{
    background-color:#fff;
}
</style>
<div class="weui-msg">
  <div class="weui-msg__icon-area"><i class="weui-icon-success weui-icon_msg"></i></div>
  <div class="weui-msg__text-area">
    <h2 class="weui-msg__title"></h2>
    <p class="weui-msg__desc">{{$exception->getMessage()}}</p>
  </div>
  <div class="weui-msg__opr-area">
    <p class="weui-btn-area">
    </p>
  </div>
</div>
@include('users.inc.footer')