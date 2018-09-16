@include('users.inc.header')
@include('users.inc.shortcut')
<style type="text/css">
.icon-dizhi{
    float: left;
    line-height: 40px;
    padding-right: 10px;
}
#area{
    float:left;
}
.area-address{
    font-size:12px;
}
.weui-panel{
    margin:8px 0 !important;
}
#grad1 {
    height: 50px;
    line-height: 50px;
    padding-left: 10px;
    color: #000;
    background: #fff;
}
.weui-media-box__title{
    font-size: 14px;
}
.spec_desc{
    margin-top:10px;
    font-size:12px;
}
.real-pay{
    border-top: 1px solid #e5e5e5;
    text-align: right;
    line-height: 40px;
    font-size: 15px;
    padding-right:10px;
}
.real-pay span{
    color:#e93b3d;
}
.zyw-container{
    padding-bottom: 40px;
}
.footer-opera{
    height: 50px;
    position: fixed;
    bottom: 0;
    background: #fff;
    width: 100%;
    border-top: 1px solid #f6f6f6;
}
.footer-opera .opera-button{
    border: 1px solid #999;
    padding: 6px 8px;
    font-size: 15px;
    border-radius: 3px;
    margin-top: 8px;
    display: inline-block;
    float: right;
    margin-right: 10px;
    background: #fff;
}
.pay_order{
    color:#e93b3d;
    border-color:#e93b3d !important;
}
</style>
<header class="zyw-header">
    <div class="zyw-container white-color">
        <div class="head-l"><a href="javascript:{{$_COOKIE['lastRecord'] ?? "self.location='/'"}};" target="_self"><i class="iconfont icon-fanhui1"></i></a></div>
        <h1>订单详情</h1>
        <div class="head-r"><i class="iconfont icon-gengduo"></i></div>
    </div>
</header>
<section class="zyw-container">
    <div id="grad1">
        {{$orderState[$orderInfo->state]}}
    </div>
    <div class="weui-panel weui-cell">
            <i class="iconfont icon-dizhi"></i>
            <div id="area">
                <div id="area">
                    <span class="area-person">{{$orderInfo->receiver_name}}&nbsp;&nbsp;&nbsp;&nbsp;{{$orderInfo->receiver_mobile}}</span>
                    <br>
                    <span class="area-address">{{$orderInfo->receiver_area}} {{$orderInfo->receiver_address}}</span>
                </div>
        </div>
    </div>
    <div class="weui-panel weui-panel_access">
      <div class="weui-panel__bd">
        @foreach($orderGoodsInfo as $val)
        <a href="/goods/{{$val->spec_id}}" class="weui-media-box weui-media-box_appmsg">
          <div class="weui-media-box__hd">
            <img class="weui-media-box__thumb" src="{{empty($val->goods_img) ? elixir('images/users/carnetmotors.jpg') : $val->goods_img}}">
          </div>
          <div class="weui-media-box__bd">
            <div class="weui-media-box__title">{{ $val->goods_name }}</div>
            <div class="weui-media-box__desc">
                <div class="spec_desc">数量:{{$val->num}} @if(!empty($val->spec_values))规格:{{$val->spec_values}} @endif</div>
                <div class="spec_desc">￥{{sprintf("%.2f",$val->price/100)}}</div>
            </div>
          </div>
        </a>
        @endforeach
      </div>
     </div>

     <div class="weui-panel">
        <div class="weui-cells__title">
            <div>订单编号：{{$orderInfo->order_sn}}</div>
            <div>下单时间：{{$orderInfo->created_at}}</div>
            @if ($orderInfo->state==1 || $orderInfo->state==2)
            <div>物流支付：运费到付</div>
            @endif
        </div>
        @if(!empty($orderInfo->pay_time))
        <div class="weui-cells__title">
            <div>支付方式：微信支付</div>
            <div>交易单号：{{$orderInfo->transaction_id}}</div>
            <div>支付时间：{{$orderInfo->pay_time}}</div>
        </div>
        @endif
        @if (count($orderShipingList) > 0)
        <div class="weui-cells__title">
            <div>物流支付：运费到付</div>
            @foreach($orderShipingList as $orderShiping)
            <div>发货时间：{{ $orderShiping['express_time'] }}</div>
            <div>物流公司：{{ $orderShiping['express_name'] }}</div>
            <div>物流单号：{{ $orderShiping['express_no'] }}</div>
            @endforeach
        </div>
        @endif

        <div class="weui-form-preview">
            <div class="weui-form-preview__bd">
                <div class="weui-form-preview__item">
                  <label class="weui-form-preview__label">商品总额</label>
                  <span class="weui-form-preview__value">{{sprintf("%.2f",$orderInfo->payment/100)}}</span>
                </div>
                <div class="weui-form-preview__item">
                  <label class="weui-form-preview__label">运费</label>
                  <span class="weui-form-preview__value">{{sprintf("%.2f",$orderInfo->express_fee/100)}}</span>
                </div>
              </div>
              <div class="real-pay">实付款：<span>￥{{sprintf("%.2f",($orderInfo->payment-$orderInfo->express_fee)/100)}}</span></div>
        </div>
     </div>
</section>
@if ($orderInfo->state == 1)
<div class="footer-opera">
<a  href="/order/cashPay/{{$orderInfo->order_sn}}" class="opera-button pay_order" >去支付</a>
<a href="javascript:;" class="opera-button cancle_order">取消订单</a>
</div>
@endif
@if ($orderInfo->state == 3)
<div class="footer-opera">
<a href="javascript:;" class="opera-button confirm_recipt" >确认收货</a>
</div>
@endif
<script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
<script src="{{asset('js/users/front.js')}}"></script>
<script>
$(document).ready(function() {
    var orderSn = "{{$orderInfo->order_sn}}";
    //取消订单
    $('.cancle_order').click(function(){
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
    });
    //确认收货
    $('.confirm_recipt').click(function(){
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
})
</script>
</body>
</html>
