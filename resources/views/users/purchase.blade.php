@include('users.inc.header')
<link href="{{ elixir('css/users/purchase.css') }}" rel="stylesheet">
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
      <div class="weui-panel__bd">
        <a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg">
          <div class="weui-media-box__hd">
            <img class="weui-media-box__thumb" src="{{ $goodsInfo->img }}">
          </div>
          <div class="weui-media-box__bd">
            <h4 class="weui-media-box__title">{{ $goodsInfo->name }}</h4>
            <div class="weui-media-box__desc">
                <div class="sku_price">
                    ¥<span>{{ sprintf("%.2f",(empty($promotion) ? $goodsInfo->sell_price : $promotion->price)/100) }}</span>
                    <del></del>
                </div>
                <div class="sku">{{ floor($goodsInfo->weight) }}g</div>
            </div>
          </div>
        </a>
      </div>
      <div class="weui-panel__ft">
        <div class="weui-cell weui-cell_access weui-cell_link">
          <div class="weui-cell__bd">购买数量</div>
          <div id="modifyNumDom" class="num_wrap">
                <span class="minus @if($num<=1) disabled @endif"></span>
                <input class="num" type="tel" onkeypress="return event.keyCode>=48&&event.keyCode<=57" value="{{min($num,$goodsInfo->number)}}">
                <span class="plus @if($num>=$goodsInfo->number) disabled @endif" ></span>
          </div>
        </div>    
      </div>
    </div>
    
    <div class="weui-form-preview">
      <div class="weui-form-preview__bd">
        <div class="weui-form-preview__item">
          <label class="weui-form-preview__label">商品金额</label>
          <span class="weui-form-preview__value">￥<span class="goodsMoney"></span></span>
        </div>
        <div class="weui-form-preview__item">
          <label class="weui-form-preview__label">运费</label>
          <span class="weui-form-preview__value">+ ￥<span class="freight">0.00</span></span>
        </div>
      </div>
    </div>
    
    <div id="operation">
        <a href="javascript:;" class="weui-btn weui-btn_default close-popup">实付款: ￥<span class="realPay"></span></a>
        @if ($maxSpec < 1)
        <a href="javascript:;" class="weui-btn weui-btn_warn footer-danger">库存不足</a>
        @else
        <a href="javascript:;" class="weui-btn weui-btn_warn confirm-order">提交订单</a>
        @endif
    </div>
</section>


<div id="expressAddress" class="weui-popup__container">
  <div class="weui-popup__overlay"></div>
  <div class="weui-popup__modal">
    <header class="zyw-header">
        <div class="zyw-container white-color">
            <div class="head-l">
                <a href="javascript:;" class="close-popup" target="_self"><i class="iconfont icon-fanhui1"></i></a>
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
<input type="hidden" id="spec_id" value="{{$goodsInfo->id}}" />
<input type="hidden" id="promotion_id" value="{{$goodsInfo->promotion_id ?? 0}}" />
<input type="hidden" id="max_spec" value="{{$maxSpec}}" />
<script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
<script src="{{asset('js/users/front.js')}}"></script>
<script src="{{asset('js/users/purchase.js')}}"></script>
</body>
</html>