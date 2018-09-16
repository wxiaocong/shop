@forelse ($goodsList as $val)
<li class="col-sm-6 col-xs-6 ware-box">
    <a href="/goods/{{ $val->id }}">
        <div class="ware-img">
            <img src="{{empty($val->img) ? elixir('images/users/carnetmotors.jpg') : $val->img}}" alt="">
        </div>
        <h3 class="ware-title">{{$val->name}}</h3>
        @if(empty($val->award_value))
        <span class="ware-prince theme-color">￥{{sprintf("%.2f",$val->sell_price/100)}}</span>
        @else
        <span class="special-price">￥{{sprintf("%.2f",json_decode($val->award_value)->price/100)}}</span>
        <del class="del-price">￥{{sprintf("%.2f",$val->sell_price/100)}}</del>
        @endif
    </a>
</li>
@empty
@endforelse
