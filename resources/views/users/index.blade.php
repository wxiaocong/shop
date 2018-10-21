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
        <a class="weui-navbar__item weui-bar__item--on" href="#tab1">
          选项一
        </a>
        <a class="weui-navbar__item" href="#tab2">
          选项二
        </a>
        <a class="weui-navbar__item" href="#tab3">
          选项三
        </a>
        </div>
        <div class="weui-tab__bd">
        <div id="tab1" class="weui-tab__bd-item weui-tab__bd-item--active">
          <h1>页面一</h1>
        </div>
        <div id="tab2" class="weui-tab__bd-item">
          <h1>页面二</h1>
        </div>
        <div id="tab3" class="weui-tab__bd-item">
          <h1>页面三</h1>
        </div>
        </div>
    </div>
    <div class="index-wares">
        <div class="wares-cont">
            <ul class="clearfix" style="width:100%;margin-bottom:0;">
                @forelse($recommends as $recommend)
                <li class="ware-box">
                    <a href="\goods\{{$recommend->id}}">
                        <div class="ware-img">
                            @if(!empty($recommend->img))<img src="{{$recommend->img}}">@endif
                        </div>
                    </a>
                </li>
                @empty
                @endforelse
            </ul>
        </div>
        <div style="width:100%;margin-bottom: 60px;">
            <!-- <img style="width:100%;margin-bottom: 60px;" src="{{ elixir('images/users/service.jpg') }}" > -->
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
</body>
</html>