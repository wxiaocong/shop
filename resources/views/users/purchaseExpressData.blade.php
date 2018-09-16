@forelse ($addressList as $key=>$val)
<div class="weui-panel weui-panel_access">
    <div class="weui-panel__bd">
        <div class="weui-media-box weui-media-box_appmsg">
            @if ($val->is_default == 1)<i class="iconfont icon-gou"></i>@endif
            <div class="weui-media-box__bd" data="{{$val->id}}">
                <span class="area-person">{{ $val->to_user_name }}&nbsp;&nbsp;&nbsp;&nbsp;{{ $val->mobile }}</span>
                @if ($val->is_default == 1)<span class="isDefault">默认</span>@endif<br>
                <span class="area-address">{{ $val->province_name }}{{ $val->city_name }}{{ $val->area_name }}{{ $val->address }}</span>
            </div>
            <a href="/address/{{ $val->id }}/edit"><i class="iconfont icon-bianji1"></i></a>
        </div>
    </div>
</div>
@empty
@endforelse