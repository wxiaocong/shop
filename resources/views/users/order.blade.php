@include('users.inc.header')
<link href="{{ elixir('css/users/order.css') }}" rel="stylesheet">
<style type="text/css">
.swiper-container{
    background:#f6f6f6;
}
.swiper-slide, .swiper-wrapper{
    width:auto;
    height:auto;
}
.swiper-slide img{
    width:auto;
    margin-top:8px;
    margin-bottom:8px;
}
.media-object{
    display:inline;
}
</style>
<header class="zyw-header">
    <div class="zyw-container white-color">
        <div class="head-l"><a href="javascript:self.location='/home'" target="_self"><i class="iconfont icon-fanhui1"></i></a></div>
        <h1>订单列表</h1>
    </div>
</header>
<section class="zyw-container">
    <div class="weui-tab">
        <div class="weui-navbar">
            @foreach($showState as $k=>$v)
            <a href="/order?order_type={{$k}}" class="weui-navbar__item @if($orderType == $k) weui-bar__item--on @endif">{{$v}}</a>
            @endforeach
        </div>
        <div class="weui-tab__bd">
            <div class="order-group">
            
            </div>
        </div>
        <div class="weui-loadmore">
              <i class="weui-loading"></i>
              <span class="weui-loadmore__tips">正在加载</span>
         </div>
    </div>
</section>
@include('users.inc.footer')
<script src="{{asset('js/users/swiper.min.js')}}"></script>
<script>
$(document).ready(function() {
var curPage = 1;
var loading = false;
var loadMore = true;

changeData({{$orderType}});//初始数据

//加载更多
$(document.body).infinite().on("infinite", function() {
  if(loading) return;
  loading = true;
  changeData($('.weui-bar__item--on').attr('data'));
});

// 取消订单
function cancelOrder(){
    $('.order-group').on('click','.cancel_order',function(){
        var orderSn = $(this).attr('data');
        $.confirm({
            title: '订单取消提示',
            text: '确定取消订单吗?',
            onOK: function () {
                $.ajax({
                    url:  '/order/cancle',
                    data: {order_sn:orderSn},
                    type: 'post',
                    dataType: 'json',
                    success: function(jsonObject) {
                        if (jsonObject.code == 200) {
                            $.toast(jsonObject.messages);
                            setTimeout(function(){
                                window.location.reload();
                            },1000);
                         } else {
                             $.toast(jsonObject.messages, "forbidden");
                         }
                    }
                })
            }
        });
    })
}
//确认收货
function confirmReceipt(){
    $('.order-group').on('click','.confirm_recipt',function(){
        var orderSn = $(this).attr('data');
        $.confirm({
            title: '订单确认收货提示',
            text: '确认收到货了吗?',
            onOK: function () {
                $.ajax({
                    url:  '/order/confirmReceipt',
                    data: {order_sn:orderSn},
                    type: 'post',
                    dataType: 'json',
                    success: function(jsonObject) {
                        if (jsonObject.code == 200) {
                            $.toast(jsonObject.messages);
                            setTimeout(function(){
                                window.location.reload();
                            },1000);
                         } else {
                             $.toast(jsonObject.messages, "forbidden");
                         }
                    }
                })
            }
        });
    });
}
//切换
function changeData(order_type){
    if(loadMore) {
        $('.weui-loadmore__tips').html('正在加载');
        $.ajax({
            url: '/order/getData',
            type: 'POST',
            data:{order_type:order_type,curPage:curPage},
            success: function(content) {
                if(content.length > 0) {
                    $('.order-group').append(content);
                    cancelOrder();
                    confirmReceipt();
                } 
                if($('.order-group .order-group-item').length < {{$pageSize}}*curPage) {
                    loadMore = false;
                    $('.weui-loading').hide();
                    $('.weui-loadmore__tips').html('没有更多了...');
                }
                curPage += 1;
                loading = false;

                var swiper = new Swiper('.swiper-container', {
                    slidesPerView: 4,
                    spaceBetween: 20,
                    freeMode: true,
                    pagination: {
                      el: '.swiper-pagination',
                      clickable: true,
                    },
                });
            }
        });
    }
}
})
</script>