@include('users.inc.header')
<style type="text/css">
.weui-cells{
    margin-top:0;
}
.weui-cells .weui-cell{
    border:none;
}
.icon-weixinzhifu{
    color: #1DC823;
    font-size: 23px;
    padding: 0 8px;
}
.pay-type{
    font-size: 14px;
    vertical-align: middle;
    font-weight: normal;
    bottom: 3px;
    position: relative;
}
.payment{
    color:red;
}
.pay-now{
    text-align: center;
}
.weui-toast{
    width:10em;
}
.weui-dialog__title{
    font-size:16px;
}
.weui-dialog__bd{
    font-size:12px;
}
.weui-dialog__ft .weui-dialog__btn:first-child{
    color:gray;
    font-size:16px;
}
.weui-dialog__ft .weui-dialog__btn:last-child{
    color:#e93b3d;
    font-size:16px;
}
</style>
<header class="zyw-header">
    <div class="zyw-container white-color">
        <div class="head-l">
            <a href="javascript:self.location='/order/detail/{{$orderInfo->order_sn}}'" target="_self"><i class="iconfont icon-fanhui1"></i></a>
        </div>
        <h1>收银台</h1>
    </div>
</header>
<section class="zyw-container">
    <div class="weui-panel weui-cell weui-cell_access">
        <div class="weui-cell__bd" style="text-align: right;">
            <div>需支付: <span class="payment">{{sprintf("%.2f",$orderInfo->payment/100)}}元</span></div>
        </div>
    </div>
    <div class="weui-panel weui-panel_access">
        <div class="weui-cells weui-cells_radio">
        <label class="weui-cell weui-check__label" for="x11">
          <div class="weui-cell__bd">
            <div>
                <i class="iconfont icon-weixinzhifu"></i>
                <span class="pay-type">微信支付</span>
            </div>
          </div>
          <div class="weui-cell__ft">
            <input type="radio" class="weui-check" name="radio1" id="x11" checked="checked">
            <span class="weui-icon-checked"></span>
          </div>
        </label>
        </div>
    </div>
    <a class="pay-now item-layer-button theme-bgcolor white-color" type="submit">{{sprintf("%.2f",$orderInfo->payment/100)}}元</a>
</section>


<script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script src="{{elixir('js/users/front.js')}}"></script>
<script>
$('.weui-check__label').each(function(){
    if($(this).find('.weui-check').attr('checked') == 'checked') {
        $('.pay-now').html($(this).find('.pay-type').html()+$('.payment').html());
    }
});
//订单付款
$('.pay-now').click(function(){
    var buttons = $(this);
    var ordersn = "{{$orderInfo->order_sn}}";
    $.showLoading();
    $.ajax({
        url: '/order/prepay',
        type: 'POST',
        data:{ordersn:ordersn},
        dataType: 'json',
        beforeSend: function() {
            buttons.attr('disabled', 'true');
        },
        success: function(jsonObject) {
            $.hideLoading();
            if (jsonObject.code == 200) {
                if (isWeiXin()) {
                    var config = jsonObject.data.config;
                    wx.config({
                        debug: false,
                        appId: config.appId,
                        timestamp: config.timestamp,
                        nonceStr: config.nonceStr,
                        signature: config.signType,
                        jsApiList: ['chooseWXPay']
                    });
                    wx.ready(function () {
                        wx.chooseWXPay({
                            timestamp: config.timestamp,
                            nonceStr: config.nonceStr,
                            package: config.package,
                            signType: config.signType,
                            paySign: config.paySign,
                            success: function (res) {
                                // 支付成功后的回调函数
                                if (res.errMsg == "chooseWXPay:ok") {
                                    window.location.replace('/order/orderComplate/'+jsonObject.data.order_sn);
                                } else {
                                    $.toast(res.errMsg, "text");
                                }
                            },
                            cancel: function(res) {
                                $.toast('支付取消', "text");
                            }
                        });
                    });
                    wx.error(function(res){
                        $.toast(res.errMsg, "text");
                    });
                } else {
                    window.location.href = jsonObject.h5Url;
                    $.modal({
                        title: "请确认微信支付是否已完成",
                        text: '1.如果您 已在打开微信支付内支付成功，请点击"已完成支付"按钮',
                        buttons: [
                          { text: "取消", onClick: function(){ $.closeModal();} },
                          { text: "已完成支付", onClick: function(){ window.location.href = jsonObject.url;} },
                        ]
                    });
                }
            } else {
                $.toast(jsonObject.messages, "forbidden");
            }
            buttons.removeAttr('disabled');
        },
        error: function(xhr, type) {
            buttons.removeAttr('disabled');
            $.hideLoading();
        }
    });
    return false;
});
</script>
</body>
</html>