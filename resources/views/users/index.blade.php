@include('users.inc.header')
@include('users.inc.search')
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
    <div class="index-wares">
        <div class="wares-title" style="color:#e93b3d;">精品推荐</div>
        <div class="wares-cont">
            <ul class="clearfix" style="padding-bottom: 10px;">
                @forelse($recommends as $recommend)
                <li class="ware-box">
                    <a href="\goods\{{$recommend->id}}">
                        <div class="ware-img">
                            @if(!empty($recommend->img))<img src="{{$recommend->img}}">@endif
                            <span class="ware-vip">热卖</span>
                        </div>
                        <h3 class="ware-title">{{$recommend->name}}</h3>
                        <span class="ware-prince red-color">￥{{ sprintf("%.2f",$recommend->sell_price/100)}}</span>
                    </a>
                </li>
                @empty
                @endforelse
            </ul>
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