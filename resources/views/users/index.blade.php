@include('users.inc.header')
<section class="zyw-container">
    @if (!empty($adPositions))
    <div class="swiper-container">
        <div class="swiper-wrapper ad-swiper">
            @foreach ($adPositions as $ad)
            <a href="{{$ad->url}}" class="swiper-slide"><img src="{{$ad->img}}" alt="{{$ad->title}}"></a>
            @endforeach
        </div>
        <!-- 如果需要分页器 -->
        <div class="swiper-pagination"></div>
    </div>
    @endif
    <div class="weui-tab">
        <div class="weui-navbar">
        @foreach($category as $key=>$val)
        <a class="weui-navbar__item @if($key == 0) weui-bar__item--on @endif" href="#tab{{$val->id}}">
          {{$val->name}}
        </a>
        @endforeach
        </div>
        <div class="weui-tab__bd">
        @foreach($category as $key=>$val)
        <div id="tab{{$val->id}}" class="weui-tab__bd-item @if($key == 0) weui-tab__bd-item--active @endif">
          <div class="wares-cont">
            <ul class="clearfix" style="width:100%;margin-bottom:0;">
                @if(!empty($goods[$val->id]))
                @foreach($goods[$val->id] as $good)
                <li class="ware-box">
                    <a href="\goods\{{$good['id']}}">
                        <div class="ware-img">
                            @if(!empty($good['img'])) <img src="{{$good['img']}}"> @endif
                        </div>
                    </a>
                </li>
                @foreach
                @endif
            </ul>
          </div>
        </div>
        @endforeach
        </div>
    </div>
    <div class="index-wares">
        <div style="width:100%;margin-bottom: 60px;">
            <img style="width:100%;margin-bottom: 60px;" src="{{ elixir('images/users/service.jpg') }}" >
        </div>
    </div>
</section>
@include('users.inc.footer')
<script src="{{asset('js/users/swiper.min.js')}}"></script>
<script type="text/javascript">
// 轮播
$(document).ready(function () {
    // 顶部轮播图
    var mySwiper = new Swiper ('.swiper-container', {
        // 如果需要分页器
        autoplay:{
            disableOnInteraction: false,//默认true
            delay:2000,//默认3000
        },
        pagination: {
            el: '.swiper-pagination'
        }
    });
});
</script>