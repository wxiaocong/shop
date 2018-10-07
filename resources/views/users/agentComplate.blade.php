@include('users.inc.header')
<style type="text/css">
body {
    background-color: #fff;
}
.weui-btn_primary{
    background-color:#90C846;
}
</style>
<header class="zyw-header">
    <div class="zyw-container white-color">
        <div class="head-l">
            <a href="javascript:history.back(-1);" target="_self"><i class="iconfont icon-fanhui1"></i></a>
        </div>
        <h1>订单支付完成</h1>
        <div class="head-r">
            <a href="/home">完成</a>
        </div>
    </div>
</header>
<section class="zyw-container">
    <div class="weui-msg">
        <div class="weui-msg__icon-area">
            <i class="@if($code == 200) weui-icon-success @else weui-icon-warn @endif weui-icon_msg"></i>
        </div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title"></h2>
            <p class="weui-msg__desc">{{$messages}}</p>
        </div>
        <div class="weui-msg__opr-area">
            <p class="weui-btn-area">
              <a href="/home" class="weui-btn weui-btn_primary">用户用户</a>
            </p>
        </div>
    </div>
</section>
</body>
</html>