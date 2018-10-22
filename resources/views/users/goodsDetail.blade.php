@include('users.inc.header')
@include('users.inc.shortcut')
<style type="text/css">
.col-xs-6{
    padding:0;
}
#goods-content{
    max-width: 100% !important;
    overflow: hidden;
}
#goods-content img｛
    max-width:100%;
｝
.swiper-slide img{
    max-height:240px;
}
.spec-num{
    height:60px;
    margin-bottom: 56px;
    padding-right:10px;
}
.spec-num>div{
    line-height:60px;
}
.spec-num>div:first-child{
    float:left;
}
.time-limit-special-price{
    padding: 5px 10px;
    border: 1px solid #e93b3d;
    margin-right: 6px;
    color: #e93b3d;
    font-weight: bold;
}
.special-price{
    color:#e93b3d;
    font-size:20px;
    margin-right:6px;
}
.money-flag{
    color:#e93b3d;
    font-size:13px;
}
.onceNum{
    font-size:12px;
    color:#e93b3d;
}
del{
    font-size:12px;
    color:gray;
}
.weui-count .weui-count__number{
    width:45px;
}
.footer-danger{
    background:gray !important;
    text-align:center;
}
.swiper-slide{
    height:260px;
}
.item-img{
    height:260px;
}
.item-spec-layer .spec-info .spec-info-hd{
    clear:both;
}
.buy-now{
    text-align:center;
}
.zyw-footer .col-xs-12{
    padding-left:0;
    padding-right:0;
}
</style>
<header class="zyw-header">
    <div class="zyw-container white-color">
        <div class="head-l">
            <a href="javascript:history.back(-1);" target="_self"><i class="iconfont icon-fanhui1"></i></a>
        </div>
        <h1>商品详情</h1>
        <div class="head-r"><i class="iconfont icon-gengduo"></i></div>
    </div>
</header>
<section class="zyw-container">
    <!-- Swiper -->
    <div class="item-img">
        <div class="swiper-wrapper">
            @forelse ($defaultImgs as $img)
            <div class="swiper-slide"><img src="{{ $img }}" ></div>
            @empty
            <div class="swiper-slide"></div>
            @endforelse
        </div>
        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>
    </div>
    <div class="item-details white-bgcolor clearfix">
        <h3 class="details-title">{{ $goodsInfo['name'] }}</h3>
        <span id="spec-price">
        </span>
    </div>
    <div class="item-choose weui-cells mt-625">
        <a class="weui-cell weui-cell_access open-popup" href="javascript:;" data-target="#item_spec">
            <div class="weui-cell__bd">
                <p class="choose-text">规格</p>
            </div>
            <div class="weui-cell__ft choose-des"><span id="specAttr">{{$goodsInfo->values}}</span></div>
        </a>
        <div id="item_spec" class="weui-popup__container popup-bottom">
            <div class="weui-popup__overlay"></div>
            <div class="weui-popup__modal">
                <div class="item-spec-layer white-bgcolor">
                    <div class="spec-head clearfix">
                        <div class="spec-head-img"><img src="{{$goodsInfo->img}}" ></div>
                        <strong class="spec-head-prince theme-color">￥
                        <span>
                        </span>
                        </strong>
                        <p class="spec-head-intro">商品编号：<span>{{ $goodsInfo->cust_partno }}</span></p>
                    </div>
                    <div class="spec-info clearfix">
                        @foreach($skus as $sku)
                        <div class="spec-info-hd">{{$sku->name}}</div>
                        <div class="spec-info-bd">
                            <ul data="{{$sku->id}}">
                                @forelse(explode(',',$sku->value) as $value)
                                <li>{{$value}}</li>
                                @empty
                                @endforelse
                            </ul>
                        </div>
                        @endforeach
                    </div>
                    <form method="get" action="/goods/purchase" id="purchase-form">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" name="spec_id" id="spec_id" value="{{ $goodsInfo->id }}">
                    <input type="hidden" name="goods_id" id="goods_id" value="{{ $goodsInfo->goods_id }}">
                    <div class="spec-length mb-625 spec-num clearfix">
                        <div class="weui-cell__bd">
                            <p class="length-hd">数量</p>
                        </div>
                        <div class="weui-cell__ft">
                            <div class="weui-count">
                                <a class="weui-count__btn weui-count__decrease"></a>
                                <input class="weui-count__number" name="num" type="number" value="0">
                                <a class="weui-count__btn weui-count__increase"></a>
                            </div>
                        </div>
                    </div>
                    @if ($goodsInfo->number > 0)
                    <a class="buy-now item-layer-button theme-bgcolor white-color" type="submit">立即购买</a>
                    @else
                    <a href="javascript:;" class="item-layer-button white-color footer-danger">库存不足</a>
                    @endif
                    <a href="javascript:;" class="close-popup spec-close"><i class="fa fa-close"></i></a>
                    </form>
                </div>
            </div>
        </div>
        <a class="weui-cell weui-cell_access open-popup" href="javascript:;" data-target="#item_parameter">
            <div class="weui-cell__bd">
                <p class="choose-text">产品参数</p>
            </div>
            <div class="weui-cell__ft choose-des">
            </div>
        </a>
        <div id="item_parameter" class="weui-popup__container popup-bottom">
            <div class="weui-popup__overlay"></div>
            <div class="weui-popup__modal">
                <div class="item-parameter-layer white-bgcolor">
                    <h3 class="parameter-title">产品参数</h3>
                    <table class="table table-condensed parameter-table">
                        @forelse($attrs as $attr)
                        <tr>
                            <th>{{$attr->name}}</th>
                            <td>{{$attr->values}}</td>
                        </tr>
                        @empty
                        @endforelse
                        <tr>
                            <th>分类</th>
                            <td>{{$goodsInfo->category->name??''}}</td>
                        </tr>
                        <tr>
                            <th>重量</th>
                            <td><span class="spec-weight">{{$goodsInfo->weight}}</span> g</td>
                        </tr>
                    </table>
                    <button class="item-layer-button theme-bgcolor white-color close-popup" type="submit">确定</button>
                </div>
            </div>
        </div>
    </div>
    <div class="item-precent white-bgcolor" id="item-precent">
        <h4>图文详情</h4>
        <div id="goods-content">
            {!! $goodsInfo->goods->content !!}
            <br>
        </div>
    </div>
</section>
<footer class="zyw-footer">
    <div class="zyw-container white-bgcolor clearfix">
        @if ($goodsInfo->number)
        <div class="col-sm-12 col-xs-12">
            <a href="javascript:;" class="buy-now footer-btn footer-warning">立即购买</a>
        </div>
        @else
        <div class="col-sm-6 col-xs-6">
            <a href="javascript:window.location.href='/category/{{$goodsInfo->category_parent_id}}'" class="footer-btn footer-warning">查看类似 </a>
        </div>
        <div class="col-sm-6 col-xs-6">
            <a href="javascript:;" class="footer-btn footer-danger">库存不足</a>
        </div>
        @endif
    </div>
</footer>
<script src="{{ elixir('js/users/jquery.min.js') }}"></script>
<script src="{{ elixir('js/users/jquery-weui.min.js') }}"></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script src="{{asset('js/users/swiper.min.js')}}"></script>
<script src="{{asset('js/users/front.js')}}"></script>
<script src="{{asset('js/users/goods-detail.js')}}"></script>
<script>
if (isWeiXin()) {
    wx.config({!!$shareConfig!!});
    wx.ready(function () {
        //分享到朋友圈
        wx.onMenuShareTimeline({
            title: "{{ $goodsInfo['name'] }}",
            imgUrl: "{{env('APP_URL') . $goodsInfo->img}}",
            link: "{{$shareLink}}",
            success: function () {
                $.toast("分享成功", "text");
            },
            cancel: function () {
                $.toast("取消分享", "text");
            }
        });
        //发送给朋友
        wx.onMenuShareAppMessage({
            title: "{{ $goodsInfo['name'] }}",
            desc: "我在植得艾发现了一个不错的商品，赶快来看看吧。",
            imgUrl:  "{{env('APP_URL') . $goodsInfo->img}}",
            link: "{{$shareLink}}",
            success: function () {
                $.toast("分享成功", "text");
            },
            cancel: function () {
                $.toast("取消分享", "text");
            }
        });
    });
}
</script>
</body>
</html>
