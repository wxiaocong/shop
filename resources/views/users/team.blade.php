@include('users.inc.header')
<link href="{{ elixir('css/users/order.css') }}" rel="stylesheet">
<style type="text/css">
.team-item{
    font-size: 13px;
    color: #777;
    padding: 0 1rem;
}
</style>
<header class="zyw-header">
    <div class="zyw-container white-color">
        <div class="head-l"><a href="javascript:window.location.href='/home';" target="_self"><i class="iconfont icon-fanhui1"></i></a></div>
        <h1>我的团队</h1>
    </div>
</header>
<section class="zyw-container">
    <div class="weui-tab">
        <div class="weui-navbar">
            <a href="/home/myTeam/0/{{$childId}}" class="weui-navbar__item @if($teamType == 0) weui-bar__item--on @endif">全部</a>
            <a href="/home/myTeam/1/{{$childId}}" class="weui-navbar__item @if($teamType == 1) weui-bar__item--on @endif">游客</a>
            <a href="/home/myTeam/2/{{$childId}}" class="weui-navbar__item @if($teamType == 2) weui-bar__item--on @endif">艾达人</a>
            <a href="/home/myTeam/3/{{$childId}}" class="weui-navbar__item @if($teamType == 3) weui-bar__item--on @endif">艾天使</a>
        </div>
        <div class="weui-tab__bd">
            <div class="order-group">
                <div class="order-group-item clearfix">
                    <h4 class="team-item">成员数：{{count($team ?? 0)}}</h4>
                </div>
                @forelse($team as $val)
                <div class="order-group-item clearfix">
                    <div class="order-item-box">
                        <div class="media">
                            <a href="@if($childId>0) javascript:; @else /home/myTeam/0/{{$val->id}} @endif" class="pull-left">
                                <img src="{{empty($val->headimgurl) ? elixir('images/users/mylogo.png') : $val->headimgurl}}" alt="" class="media-object order-item-img">
                            </a>
                            <div class="media-body">
                                <div class="order-item-info">
                                    <h5 class="order-item-title">昵称：{{$val->nickname}}</h5>
                                    <div class="order-item-title">关注时间：{{$val->subscribe_time ? date('Y-m-d H:i:s',$val->subscribe_time) : ''}}</div>
                                    <div class="weui-media-box__desc">级别：{{$levelState[$val->level]}}&nbsp;&nbsp;{{$agentState[$val->agentLevel]['type_name'] ?? ''}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                @endforelse
            </div>
        </div>
    </div>
</section>
@include('users.inc.footer')
