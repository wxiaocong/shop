@include('users.inc.header')
@include('users.inc.search')
<style type="text/css">
.special-price {
    color: #e93b3d;
    font-size: 1.1rem;
    margin-right: 2px;
}
.index-wares .wares-cont{
    background-color:#f6f6f6;
}
#goods_list .ware-box{
    background-color:#fff;
}
</style>
<section class="zyw-container">
    <div class="weui-tab">
        <div class="weui-tab__bd">
            <div id="tab_star" class="weui-tab__bd-item weui-tab__bd-item--active">
                <div class="index-wares">
                    <div class="wares-cont">
                        <ul id="goods_list" class="clearfix">
                        </ul>
                    </div>
                </div>
            </div>
         </div>
         <div class="weui-loadmore">
              <i class="weui-loading"></i>
              <span class="weui-loadmore__tips">正在加载</span>
         </div>
    </div>
</section>
@include('users.inc.footer')
<script>
var curPage = 1;
var loading = false;
var loadMore = true;
getGoodsData();//初始数据
//加载更多
$(document.body).infinite().on("infinite", function() {
  if(loading) return;
  loading = true;
  getGoodsData();
});
//获取数据
function getGoodsData() {
    if(loadMore) {
        $('.weui-loadmore__tips').html('正在加载');
        //取值
        $.post('/category/getGoodsList',{searchKey:"{{$searchKey}}",curPage:curPage},function(content){
            if(content.length > 0) {
                $("#goods_list").append(content);
            }
            if($('#goods_list .ware-box').length < {{$pageSize}}*curPage){
                loadMore = false;
                $('.weui-loading').hide();
                $('.weui-loadmore__tips').html('没有更多了...');
            }
            curPage += 1;
            loading = false;
        });
    }
}
</script>