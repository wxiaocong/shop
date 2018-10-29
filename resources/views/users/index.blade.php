@include('users.inc.header')
<style type="text/css">
.weui-flex{
    text-align: center;
    background: #fff;
    padding: 6px;
    color:#329969;
}
.weui-flex__item img{
    width:50px;
    height:50px;
}
</style>
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
    <div class="weui-flex">
      <div class="weui-flex__item">
        <a href="/order">
        <img src="{{ elixir('images/users/1.jpg') }}">
        <div>订单</div>
        </a>
      </div>
      <div class="weui-flex__item">
        <a href="/wechat/shareQrCode/0">
        <img src="{{ elixir('images/users/2.jpg') }}">
        <div>分享</div>
        </a>
      </div>
      <div class="weui-flex__item">
        <a href="https://mp.weixin.qq.com/s/Y87e1mMB3GaIOC9nR0eZvw">
        <img src="{{ elixir('images/users/3.jpg') }}">
        <div>模式</div>
        </a>
      </div>
      <div class="weui-flex__item">
        <a href="https://mp.weixin.qq.com/s/W5uUDTPMjBJSENAfT8JJYA">
        <img src="{{ elixir('images/users/4.jpg') }}">
        <div>企业</div>
        </a>
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
            <img style="width:100%;" src="{{ elixir('images/users/service.jpg') }}" >
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