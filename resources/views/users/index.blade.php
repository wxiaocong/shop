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