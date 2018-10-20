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
.table{
    background: #fff;
    text-align: center;
}
.table>thead>tr>th {
    font-weight: normal;
    text-align: center;
    border-bottom:none;
}
#fundData th,#fundData td{
  padding: 8px 1px;
}
</style>
<header class="zyw-header">
    <div class="zyw-container white-color">
        <div class="head-l"><a href="javascript:self.location='/home/fund'" target="_self"><i class="iconfont icon-fanhui1"></i></a></div>
        <h1>提现记录</h1>
    </div>
</header>
<section class="zyw-container">
    <div class="weui-tab">
        <div class="weui-tab__bd">
            <div class="order-group">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>金额</th>
                      <th>手续费</th>
                      <th>状态</th>
                      <th>时间</th>
                    </tr>
                  </thead>
                  <tbody id="fundData">
                  
                  </tbody>
                </table>
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

changeData();//初始数据

//加载更多
$(document.body).infinite().on("infinite", function() {
  if(loading) return;
  loading = true;
  changeData();
});

//切换
function changeData(){
    if(loadMore) {
        $('.weui-loadmore__tips').html('正在加载');
        $.ajax({
            url: '/home/getWithdraw',
            type: 'POST',
            data:{curPage:curPage},
            success: function(content) {
                if(content.length > 0) {
                    $('#fundData').append(content);
                }
                if($('.order-group .order-group-item').length < {{$pageSize}}*curPage) {
                    loadMore = false;
                    $('.weui-loading').hide();
                    $('.weui-loadmore__tips').html('没有更多了...');
                }
                curPage += 1;
                loading = false;
            }
        });
    }
}
})
</script>