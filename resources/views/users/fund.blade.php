@include('users.inc.header')
<link href="{{ elixir('css/users/order.css') }}" rel="stylesheet">
<style type="text/css">
.team-item{
    font-size: 16px;
    color: #777;
    padding: 0 1rem;
    float: left;
    line-height: 50px;
}
.pay-button{
    float:right;
    line-height: 50px;
    margin-right: 6px;
}
</style>
<header class="zyw-header">
    <div class="zyw-container white-color">
        <div class="head-l"><a href="javascript:history.back(-1);" target="_self"><i class="iconfont icon-fanhui1"></i></a></div>
        <h1>资金记录</h1>
    </div>
</header>
<section class="zyw-container">
    <div class="weui-tab">
        <div class="weui-tab__bd">
            <div class="order-group">
                <div class="order-group-item clearfix">
                    <div class="team-item">余额：{{sprintf("%.2f", $userInfo->balance/100)}}</div>
                    <div class="pay-button">
                        <a href="/home/withdraw" class="btn btn-warning">提现</a>
                    </div>
                </div>
                <div class="home-cont weui-cells" style="margin-bottom:56px;">
                    <a class="weui-cell weui-cell_access" href="/home/balance">
                        <div class="weui-cell__bd">
                            <p class="choose-text">余额明细</p>
                        </div>
                        <div class="weui-cell__ft choose-des">
                        </div>
                    </a>
                    <a class="weui-cell weui-cell_access" href="/home/income/0">
                        <div class="weui-cell__bd">
                            <p class="choose-text">佣金收入</p>
                        </div>
                        <div class="weui-cell__ft choose-des">
                        </div>
                    </a>
                    <a class="weui-cell weui-cell_access" href="#">
                        <div class="weui-cell__bd">
                            <p class="choose-text">提现记录</p>
                        </div>
                        <div class="weui-cell__ft choose-des"></div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@include('users.inc.footer')