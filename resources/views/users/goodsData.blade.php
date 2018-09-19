@forelse ($goodsList as $val)
<li class="col-sm-6 col-xs-6 ware-box">
    <a href="/goods/{{ $val->id }}">
        <div class="ware-img">
            @if(!empty($val->img))<img src="{{$val->img}}" >@endif
        </div>
        <h3 class="ware-title">{{$val->name}}</h3>
        <span class="ware-prince theme-color">￥{{sprintf("%.2f",$val->sell_price/100)}}</span>
    </a>
</li>
@empty
@endforelse
