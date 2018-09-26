@include('users.inc.header')
<style type="text/css">
.order-select{
    color:#e93b3d;
}
.weui-flex{
    text-align:center;
    padding:10px;
}
.filter i{
    color:#e93b3d;
}
.weui-popup__modal{
    width:90%;
    right:0;
    background: #fff;
    transform: translate3d(100%,0,0);
}
.weui-popup__modal .weui-flex-item{
    border: 1px solid #e5e5e5;
    background: #e5e5e5;
    padding: 3px 10px;
    border-radius: 3px;
    width: 30%;
    word-break: break-all;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.weui-popup__modal .weui-flex-item:nth-child(2){
    margin:0 5%;
}
.weui-popup__modal .weui-flex-item.on-select{
    border:1px solid #e93b3d;
    background:#fff;
    color:#e93b3d;
}
.weui-popup__modal a.weui-btn{
    border-radius: 0;
    width: 50%;
    float: left;
    margin:0;
}
.weui-popup__modal #button-list{
    position: fixed;
    bottom: 0;
    width: 100%;
}
#button-list .weui-btn:after{
    border-radius:0;
}
.weuishaixuan{
    position: fixed;
    width: 100%;
    z-index: 1;
    height:42px;
}
.zyw-container>.weui-tab{
    padding-top:42px;
}
.special-price {
    color: #e93b3d;
    font-size: 1.1rem;
    margin-right: 4px;
}
.del-price{
    font-size: 1rem;
}
.index-wares .wares-cont{
    margin-top:12px;
    background-color:#f6f6f6;
}
#goods_list .ware-box{
    background-color:#fff;
}
</style>
@include('users.inc.search')
<section class="zyw-container">
    <div class="weui-panel weui-flex weuishaixuan">
        <div class="weui-flex__item" data="1">销量<i class="iconfont icon-biaotou-kepaixu"></i></div>
        <div class="weui-flex__item order-select" data="2">人气</div>
        <div class="weui-flex__item" data="3">价格<i class="iconfont icon-biaotou-kepaixu"></i></div>
        <div class="weui-flex__item filter" >筛选<i class="iconfont icon-shaixuan"></i></div>
    </div>
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
<div id="filter_container" class="weui-popup__container">
  <div class="weui-popup__overlay"></div>
  <div class="weui-popup__modal">
    <div class="weui-cell">分类</div>
    <div class="weui-cell-flex category-flex">
        <div class="weui-flex">
          <div class="weui-flex-item on-select" data="0">所有</div>
          @forelse ($categorys as $k=>$category)
            <div class="weui-flex-item" data="{{ $category->id }}">{{ $category->name }}</div>
            @if ($k%3 == 1)
        </div>
        <div class="weui-flex">
            @endif
          @empty
          @endforelse
        </div>
    </div>
    <div class="weui-cell">是否有货</div>
    <div class="weui-cell-flex stock-flex">
        <div class="weui-flex">
          <div class="weui-flex-item on-select" data="0">全部</div>
          <div class="weui-flex-item" data="1">有货</div>
          <div class="weui-flex-item" data="2">无货</div>
        </div>
    </div>
    <div id="button-list">
    <a href="javascript:;" class="weui-btn weui-btn_default close-popup">返回</a>
    <a href="javascript:;" class="weui-btn weui-btn_warn confirm-search">确定</a>
    </div>
  </div>
</div>
@include('users.inc.footer')
<script>
//关闭popup，兼容ios
$('.weui-popup__overlay').click(function(){
    $.closePopup();
});
var curPage = 1;
var loading = false;
var loadMore = true;
getGoodsData();//初始数据
//排序
$('.weui-panel').on('click',".weui-flex__item:not('.filter')",function(){
    $(this).addClass('order-select').siblings().not('.filter').removeClass('order-select').children('.iconfont').removeClass('icon-paixu-shengxu').removeClass('icon-paixu-jiangxu').addClass('icon-biaotou-kepaixu');

    var sort = $(this).not('.filter').children('.iconfont');
    if(sort){
        if(sort.hasClass('icon-paixu-jiangxu')){
            sort.removeClass('icon-paixu-jiangxu').addClass('icon-paixu-shengxu');
        }else{
            sort.removeClass('icon-biaotou-kepaixu').removeClass('icon-paixu-shengxu').addClass('icon-paixu-jiangxu');
        }
    }
    resetParam();
});
//加载更多
$(document.body).infinite().on("infinite", function() {
  if(loading) return;
  loading = true;
  getGoodsData();
});
//搜索
$('.filter').click(function(){
    $("#filter_container").popup('open');
});
//搜索选择
$('.weui-popup__modal').on('click','.weui-flex-item',function(){
    $(this).parents('.weui-cell-flex').find('.weui-flex-item').removeClass('on-select');
    $(this).addClass('on-select');
});
//执行搜索
$('.confirm-search').click(function(){
    $.closePopup();
    resetParam();
});
//重置参数 获取数据
function resetParam() {
    $("#goods_list").empty();
    curPage = 1;
    loading = false;
    loadMore = true;
    $('.weui-loading').show();
    getGoodsData();
}
//获取数据
function getGoodsData() {
    if(loadMore) {
        $('.weui-loadmore__tips').html('正在加载');
        //排序
        var sort = $('.weui-panel .order-select').attr('data');
        var sortType = 'asc'
        if($('.weui-panel .order-select').children('.iconfont').hasClass('icon-paixu-jiangxu')) {
            sortType = 'desc'
        }
        //搜索
        var category_id = $('.category-flex .on-select').attr('data');
        var hasStock = $('.stock-flex .on-select').attr('data');
        //取值
        $.post('/category/getGoodsList',{sort:sort,sortType:sortType,category_parent_id:{{ request()->category }},category_id:category_id,hasStock:hasStock,curPage:curPage},function(content){
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