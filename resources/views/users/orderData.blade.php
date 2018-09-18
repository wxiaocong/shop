@forelse($orderList as $val)
<div class="order-group-item clearfix">
    <div class="order-item-box">
        <h4 class="order-item-id">订单编号：{{$val->order_sn}}
            <span class="order-item-state theme-color pull-right">{{$order_state[$val->state]}}</span>
        </h4>
        <div class="media" onClick="window.location.href='/order/detail/{{$val->order_sn}}'">
            @if (count(explode(',',$val->img)) > 1)
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    @foreach(explode(',',$val->img) as $img)
                    <div class="swiper-slide"><img src="{{$img}}?x-oss-process=image/resize,w_80,h_80" class="order-item-img"></div>
                    @endforeach
                </div>
            </div>
            @else
            <a href="javascript:;" class="pull-left">
                <img src="{{$val->img}}?x-oss-process=image/resize,w_80,h_80" alt="" class="media-object order-item-img">
            </a>
            <div class="media-body">
                <div class="order-item-info">
                    <h5 class="order-item-title">{{$val->goods_name}}</h5>
                    <div class="weui-media-box__desc">{{$val->values}}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
    <p class="text-right order-item-sum">共计{{$val->num}}件商品，合计{{sprintf("%.2f",$val->payment/100)}}（含运费{{sprintf("%.2f",$val->express_fee/100)}}元）</p>
    <div class="order-item-btn pull-right">
        @if (in_array($val->state,array(1)))
        <a href="javascript:;" class="btn btn-sm btn-default cancel_order" data="{{$val->order_sn}}">取消订单</a>
        @endif
        @if ($val->state == 1)
        <a href="/order/cashPay/{{$val->order_sn}}" class="btn btn-sm btn-primary theme-bdcolor theme-bgcolor pay-order">去支付</a>
        @endif
        @if ($val->state == 3)
        <a href="javascript:;" class="btn btn-sm btn-primary theme-bdcolor theme-bgcolor confirm_recipt" data="{{$val->order_sn}}">确认收货</a>
        @endif
    </div>
</div>
@empty
@endforelse
