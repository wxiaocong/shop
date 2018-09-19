@include('users.inc.header')
<link href="{{ elixir('css/users/home.css') }}" rel="stylesheet">
<link href="{{ asset('css/font-awesome.css') }}" rel="stylesheet">
<header class="zyw-header">
    <div class="zyw-container white-color">
        <div class="head-l">
            <a href="javascript:{{$_COOKIE['lastRecord'] ?? "self.location='/'"}};" target="_self"><i class="iconfont icon-fanhui1"></i></a>
        </div>
        <h1>个人中心</h1>
    </div>
</header>
<section class="zyw-container">
    <div class="home-head">
        <div class="head-img">
            <div class="head-main">
                <div class="my-head">
                    <div class="my-head-img">
                        <a href="/home/{{ session('user')->id }}"><img src="{{ $userInfo->headimgurl ?? elixir('images/users/mylogo.png') }}"></a>
                    </div>
                </div>
                <div class="my-head-msg">
                    <div class="my-head-name"><span>{{ $userInfo->nickname }}</span></div>
                    <div class="my-head-user">用户名：{{ $userInfo->mobile }}</div>
                    @if ($userInfo->business_audit_state == config('statuses.user.businessAuditState.pass.code'))
                        <li><i class="home-icon fa fa-star"></i>商家用户</li>
                    @else
                        <li><i class="home-icon fa fa-user"></i>&nbsp;个人用户</li>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="home-cont weui-cells mt-625">
        <a class="weui-cell weui-cell_access" href="/order">
            <div class="weui-cell__bd">
                <p class="choose-text"><i class="iconfont icon-dingdan"></i> 全部订单</p>
            </div>
            <div class="weui-cell__ft choose-des all-order">查看所有订单</div>
        </a>
    </div>
    <div class="weui-flex home-order white-bgcolor mb-625">
        <a href="/order?order_type=1" class="weui-flex__item">
            <div class="weui-flex__icon"><i class="iconfont icon-weibiaoti2fuzhi04"></i></div>
            <p class="weui-flex__label">待付款</p>
        </a>
        <a href="/order?order_type=2" class="weui-flex__item">
            <div class="weui-flex__icon"><i class="iconfont icon-daifahuo"></i></div>
            <p class="weui-flex__label">待发货</p>
        </a>
        <a href="/order?order_type=3" class="weui-flex__item">
            <div class="weui-flex__icon"><i class="iconfont icon-icon3"></i></div>
            <p class="weui-flex__label">待收货</p>
        </a>
        <a href="/order?order_type=8" class="weui-flex__item">
            <div class="weui-flex__icon"><i class="iconfont icon-yiwancheng"></i></div>
            <p class="weui-flex__label">已完成</p>
        </a>
    </div>
    <div class="home-cont weui-cells" style="margin-bottom:56px;">
        <a class="weui-cell weui-cell_access" href="/home/1">
            <div class="weui-cell__bd">
                <p class="choose-text"><i class="iconfont icon-gerenziliao1"></i> 我的资料</p>
            </div>
            <div class="weui-cell__ft choose-des">
            </div>
        </a>
        <a class="weui-cell weui-cell_access" href="/address">
            <div class="weui-cell__bd">
                <p class="choose-text"><i class="iconfont icon-ziyuan"></i> 收货地址</p>
            </div>
            <div class="weui-cell__ft choose-des"></div>
        </a>
    </div>
</section>
@include('users.inc.footer')
