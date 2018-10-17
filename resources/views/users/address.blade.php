@include('users.inc.header')
@include('users.inc.shortcut')
<link href="{{ elixir('css/users/address.css') }}" rel="stylesheet">
<header class="zyw-header">
    <div class="zyw-container white-color">
        <div class="head-l">
            <a href="javascript:history.back(-1);" target="_self">
                <i class="iconfont icon-fanhui1"></i>
            </a>
        </div>
        <h1>收货地址管理</h1>
        <div class="head-r"><i class="iconfont icon-gengduo"></i></div>
    </div>
</header>
<section class="zyw-container">
    <div class="home-cont weui-cells mb-625">
        @forelse ($addressList as $val)
        <div class="weui-panel weui-panel_access">
            <div class="weui-panel__bd">
                <div class="weui-media-box weui-media-box_appmsg">
                    <div class="weui-media-box__bd">
                        <p class="weui-media-box__top">
                            <span>{{ $val->to_user_name }}</span>
                            <span style="float:right;">{{ $val->mobile }}</span>
                        </p>
                        <p class="weui-media-box__desc">{{ $val->province_name }}{{ $val->city_name }}{{ $val->area_name }}{{ $val->address }}</p>
                    </div>
                </div>
            </div>
            <div class="weui-panel__ft">
                <div class="weui-cells weui-cells_checkbox">
                  <label class="weui-cell weui-check__label" for="s{{$val->id}}">
                    <div class="weui-cell__hd">
                      <input type="checkbox" class="weui-check" name="checkbox1" data="{{$val->id}}" id="s{{$val->id}}"  @if ($val->is_default == 1)checked="checked" disabled @endif>
                      <i class="weui-icon-checked"></i>
                    </div>
                    <div class="weui-cell__bd">
                      <p>@if ($val->is_default == 0)设为默认@else 默认地址@endif</p>
                    </div>
                  </label>
                  <div class="weui-cell__edit">
                    <a href="/address/{{ $val->id }}/edit"><i class="iconfont icon-bianji1"></i> 编辑 </a>
                    <a href="javascript:delAddress({{ $val->id }});"><i class="iconfont icon-shanchu"></i> 删除 </a>
                  </div>
                </div>
            </div>
        </div>
        @empty
        @endforelse
    </div>
    <a href="javascript:window.location.replace('/address/create')" class="weui-btn weui-btn_warn theme-bgcolor person-submit">新建地址</a>
</section>
<script src="{{ elixir('js/users/jquery.min.js') }}"></script>
<script src="{{ elixir('js/users/jquery-weui.min.js') }}"></script>
<script src="{{elixir('js/users/front.js')}}"></script>
<script>
    $('.zyw-container').on('click', '.weui-check', function(){
       $(this).parents('.weui-panel').siblings('.weui-panel').find('.weui-check').removeAttr('checked');
       $('.weui-check').attr('disabled',true);
       $.getJSON('/address/setDefault',{id:$(this).attr('id').slice(1)},function(jsonObject){
           if (jsonObject.code == 200) {
               $.toast(jsonObject.messages);
               setTimeout(function(){
                   window.location.href = jsonObject.url;
               },1000);
            } else {
                $.toast(jsonObject.messages, "forbidden");
                setTimeout(function(){
                    window.location.reload();
                },1000);
            }
       });
    });

    //删除收货地址
    function delAddress(id)
    {
        $.confirm("确定删除该收货地址吗？", '', function() {
            $.ajax({
                url: '/address/' + id,
                type: 'delete',
                dataType: 'json',
                success: function(jsonObject) {
                    if (jsonObject.code == 200) {
                        $.toast(jsonObject.messages);
                        setTimeout(function(){
                            window.location.href = jsonObject.url;
                        },1000);
                     } else {
                         $.toast(jsonObject.messages, "forbidden");
                     }
                }
            });
        })
    }
</script>
</body>
</html>
