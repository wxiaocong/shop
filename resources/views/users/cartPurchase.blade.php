@include('users.inc.header')
<link href="{{ elixir('css/users/purchase.css') }}" rel="stylesheet">
<style type="text/css">
.swiper-slide{
    height: 100px;
}
.seckill-ware img {
    height: 80px;
    margin-top: 10px;
}
.swiper-wrapper{
    width:80%;
    float: left;
}
.seckill-wares{
    width: 80%;
    overflow: scroll;
    float:left;
}
.seckill-wares::-webkit-scrollbar {
    display: none;
}
#cart-cnt{
    padding-top: 40px;
    float: right;
}
#cart-cnt span{
    padding-right: 13px;
    position: relative;
    text-align: right;
    color: #999;
}
#cart-cnt span:after{
    content: " ";
    display: inline-block;
    height: 8px;
    width: 8px;
    border-width: 2px 2px 0 0;
    border-color: #c8c8cd;
    border-style: solid;
    -webkit-transform: matrix(.71,.71,-.71,.71,0,0);
    transform: matrix(.71,.71,-.71,.71,0,0);
    position: absolute;
    top: 50%;
    margin-top: -5px;
    right: 6px;
    margin-right: 6px;
}
.pop-num{
    float: right;
    line-height: 30px;
}
.weui-media-box_appmsg .weui-media-box__hd{
    width: 80px;
    height: 80px;
    max-width: 80px;
    max-height: 80px;
    float: left;
    border: 1px solid #dedede;
    padding: 2px;
}
</style>
<header class="zyw-header">
    <div class="zyw-container white-color">
        <div class="head-l">
            <a href="javascript:{{$_COOKIE['lastRecord'] ?? "self.location='/'"}};" target="_self"><i class="iconfont icon-fanhui1"></i></a>
        </div>
        <h1>确认订单</h1>
    </div>
</header>
<section class="zyw-container">
    <div class="weui-panel weui-cell weui-cell_access" id="openPopup">
        <div class="weui-cell__bd">
            <i class="iconfont icon-dizhi"></i>
            <input type="hidden" id="express_id" value="{{$defaultAddress->id ?? 0}}">
            <div id="area">
                @if ($defaultAddress)
                    <span class="area-person">{{ $defaultAddress->to_user_name }}&nbsp;&nbsp;&nbsp;&nbsp;{{ $defaultAddress->mobile }}</span>
                    @if ($defaultAddress->isDefault == 1)<span class="isDefault">默认</span>@endif<br>
                    <span class="area-address">{{ $defaultAddress->region }}{{ $defaultAddress->address }}</span>
                @else
                    <span class='no-address'>请填写收货地址</span>
                @endif
            </div>
        </div>
        <span class="weui-cell__ft"></span>
    </div>
    <div class="weui-panel weui-panel_access">
      <div class="weui-panel__hd">商品信息</div>
      @if(count($spec) == 1)
      <div class="weui-panel__bd">
        @foreach($goods as $key=>$good)
        <a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg">
          <div class="weui-media-box__hd">
            <img class="weui-media-box__thumb" src="{{ $good['img'] }}">
          </div>
          <div class="weui-media-box__bd">
            <h4 class="weui-media-box__title">{{ $good['name'] }}</h4>
            <div class="weui-media-box__desc">
                <div class="sku_price">
                    ¥ <span>{{ sprintf("%.2f",$good['price']/100) }}</span>
                    <div class="pop-num">x{{ $good['num'] }}</div>
                </div>
            </div>
          </div>
        </a>
        @endforeach
      </div>
      @else
      <div class="seckill-bd">
            <div class="seckill-wares">
                <div class="swiper-wrapper">
                    @foreach($goods as $key=>$good)
                        <div class="swiper-slide seckill-ware">
                            <img src="{{$good['img']}}" alt="">
                        </div>
                    @endforeach
                </div>
            </div>
            <div id="cart-cnt" class="open-popup" data-target="#cartList"><span>共{{$totalNum}}件&nbsp;&nbsp;</span></div>
      </div>
    </div>
    @endif
    <div class="weui-form-preview">
      <div class="weui-form-preview__bd">
        <div class="weui-form-preview__item">
          <label class="weui-form-preview__label">商品金额</label>
          <span class="weui-form-preview__value">￥<span>{{$totalPrice/100}}</span></span>
        </div>
        <div class="weui-form-preview__item">
          <label class="weui-form-preview__label">运费</label>
          <span class="weui-form-preview__value">+ ￥<span class="freight">0.00</span></span>
        </div>
      </div>
    </div>
    
    <div id="operation">
        <a href="javascript:;" class="weui-btn weui-btn_default close-popup">实付款: ￥<span>{{$totalPrice/100}}</span></a>
        <a href="javascript:;" class="weui-btn weui-btn_warn confirm-order">提交订单</a>
    </div>
</section>


<div id="expressAddress" class="weui-popup__container">
  <div class="weui-popup__overlay"></div>
  <div class="weui-popup__modal">
    <header class="zyw-header">
        <div class="zyw-container white-color">
            <div class="head-l">
                <a href="javascript:;" class="close-popup"><i class="iconfont icon-fanhui1"></i></a>
            </div>
            <h1>收货地址</h1>
        </div>
    </header>
    <section class="zyw-container">
        <div class="panel-addr">
        </div>
        <div class="footer-opera">
            <a href="javascript:window.location.href='/address/create'" class="weui-btn weui-btn_warn theme-bgcolor person-submit">新建地址</a>
        </div>
    </section>
  </div>
</div>
<div id="cartList" class="weui-popup__container">
  <div class="weui-popup__overlay"></div>
  <div class="weui-popup__modal">
    <header class="zyw-header">
    <div class="zyw-container white-color">
        <div class="head-l">
            <a href="javascript:;" class='close-popup'><i class="iconfont icon-fanhui1"></i></a>
        </div>
        <h1>商品清单</h1>
        <div class="head-r" id="rem_s">共{{$totalNum}}件</div>
    </div>
    </header>
    <div class="weui-panel__bd" style="margin-top:45px;">
        @foreach($goods as $key=>$good)
        <a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg">
          <div class="weui-media-box__hd">
            <img class="weui-media-box__thumb" src="{{ $good['img'] }}">
          </div>
          <div class="weui-media-box__bd">
            <h4 class="weui-media-box__title">{{ $good['name'] }}</h4>
            <div class="weui-media-box__desc">
                <div class="sku_price" style="display: inline-block;">
                    ¥<span>{{ sprintf("%.2f",$good['price']/100) }}</span>
                </div>
                <div class="pop-num">x{{ $good['num'] }}</div>
            </div>
          </div>
        </a>
        @endforeach
    </div>
  </div>
</div>
<input type="hidden" id="spec" value="{{serialize($spec)}}" />
<script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
<script src="{{asset('js/users/swiper.min.js')}}"></script>
<script src="{{asset('js/users/front.js')}}"></script>
<script src="{{asset('js/users/cart-purchase.js')}}"></script>
</body>
</html>