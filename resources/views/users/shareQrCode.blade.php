@include('users.inc.header')
<header class="zyw-header">
    <div class="zyw-container white-color">
        <div class="head-l"><a href="javascript:history.back(-1);" target="_self"><i class="iconfont icon-fanhui1"></i></a></div>
        <h1>我的推广二维码</h1>
    </div>
</header>
<section class="zyw-container" style="padding-bottom:0;">
    <img src="{{$imgSrc}}" style="width:100%;">
</section>
<script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script type="text/javascript">
if (isWeiXin()) {
    wx.config({!!$shareConfig!!});
    wx.ready(function () {
        //分享到朋友圈
        wx.onMenuShareTimeline({
            title: "植得艾",
            imgUrl: "{{env('APP_URL') . $imgSrc}}",
            success: function () {
                $.toast("分享成功", "text");
            },
            cancel: function () {
                $.toast("取消分享", "text");
            }
        });
        //发送给朋友
        wx.onMenuShareAppMessage({
            title: "植得艾",
            desc: "我在植得艾发现了一个不错的商品，赶快来看看吧。",
            imgUrl:  "{{env('APP_URL') . $imgSrc}}",
            success: function () {
                $.toast("分享成功", "text");
            },
            cancel: function () {
                $.toast("取消分享", "text");
            }
        });
    });
}
</script>
</body>
</html>