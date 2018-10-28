@forelse($orderList as $val)
<div class="order-group-item clearfix">
    <div class="order-item-box">
        <h4 class="order-item-id">订单编号：{{$val->order_sn}}
            <span class="order-item-state theme-color pull-right">{{$order_state[$val->state]}}</span>
        </h4>
        <div class="media"">
            @if (count(explode(',',$val->img)) > 1)
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    @foreach(explode(',',$val->img) as $img)
                    <div class="swiper-slide"><img src="{{$img}}" class="order-item-img"></div>
                    @endforeach
                </div>
            </div>
            @else
            <a href="javascript:;" class="pull-left">
                <img src="{{$val->img}}" alt="" class="media-object order-item-img">
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
    <p class="text-right order-item-sum">下单人：{{$val->nickname}}，共计{{$val->num}}件商品，合计{{sprintf("%.2f",$val->payment/100)}}元）</p>
    <div class="order-item-btn pull-right">
        @if ($val->state == 2)
        <a href="javascript:;" class="btn btn-sm btn-primary theme-bdcolor theme-bgcolor confirm_recipt" data="{{$val->order_sn}}">发货</a>
        @endif
    </div>
</div>
@empty
@endforelse
