@include('users.inc.header') 
@include('users.inc.search')
<style type="text/css">
#page-infinite-navbar {
    padding-bottom: 56px;
}
.weui-navbar {
    top: 44px;
}
.weui-navbar+.weui-tab__bd {
    padding-top: 94px;
}
.on-going{
    color: #e93b3d;
    font-size: 12px;
    border: 1px solid #e93b3d;
    padding: 0px 5px;
    top: 5px;
    right: 5px;
    position: absolute;
    width: 48px;
    font-weight: bold;
}
.tomorrow-promotion{
    color: gray;
    font-size: 12px;
    border: 1px solid gray;
    padding: 0px 5px;
    top: 5px;
    right: 5px;
    position: absolute;
    width: 48px;
    font-weight: bold;
}
.ware-box{
    padding-left:5px;
    padding-right:5px;
}
.special-price {
    color: #e93b3d;
    font-size: 16px;
    margin-right: 4px;
}
</style>
<div class="weui-tab" id='page-infinite-navbar'>
    <div class="weui-navbar">
        <a href='#tab1' class="weui-navbar__item weui-bar__item--on">当天特价({{substr_replace($promotionTime['startTime'],':','-2',0).'~'.substr_replace($promotionTime['endTime'],':','-2',0)}})</a>
        <a href='#tab2' class="weui-navbar__item">明日特价</a>
    </div>
    <div class="weui-tab__bd">
        <div id="tab1"
            class="weui-tab__bd-item weui-tab__bd-item--active">
            <div class="doc-head weui-tab__bd">
                <div class="index-wares">
                    <div class="wares-cont">
                        <ul id="promotions_list1" class="clearfix">
                            @forelse ($onGoingPromotion as $val)
                            <li class="col-sm-6 col-xs-6 ware-box">
                                <a href="/goods/{{ $val->condition }}">
                                    <div class="ware-img">
                                        <img src="{{$val->img}}" alt="">
                                    </div>
                                    <h3 class="ware-title">{{$val->name}}</h3>
                                    <span class="special-price">￥{{sprintf("%.2f",json_decode($val->award_value)->price/100)}}</span>
                                    <del class="del-price">￥{{sprintf("%.2f",$val->sell_price/100)}}</del>
                                    @if($isGoing)
                                        @if(json_decode($val->award_value)->totalNum > $val->selled_num && json_decode($val->award_value)->totalNum * $multipleLimit > $val->order_num)
                                        <span class="on-going">进行中</span>
                                        @else
                                        <span class="tomorrow-promotion">已售罄</span>
                                        @endif
                                    @else
                                    <span class="tomorrow-promotion">未开始</span>
                                    @endif
                                </a>
                            </li>
                            @empty
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div id="tab2" class="weui-tab__bd-item">
            <div class="doc-head weui-tab__bd">
                <div class="index-wares">
                    <div class="wares-cont">
                        <ul id="promotions_list2" class="clearfix">
                            @forelse ($tomorrowPromotion as $val)
                            <li class="col-sm-6 col-xs-6 ware-box">
                                <a href="/goods/{{ $val->condition }}">
                                    <div class="ware-img">
                                        <img src="{{$val->img}}" alt="">
                                    </div>
                                    <h3 class="ware-title">{{$val->name}}</h3>
                                    <span class="ware-prince theme-color">￥{{sprintf("%.2f",json_decode($val->award_value)->price/100)}}</span>
                                    <del class="del-price">￥{{sprintf("%.2f",$val->sell_price/100)}}</del>
                                    <span class="tomorrow-promotion">未开始</span>
                                </a>
                            </li>
                            @empty
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('users.inc.footer')