@include('users.inc.header')
<link href="{{ elixir('css/users/withdraw.css') }}" rel="stylesheet">
<header class="zyw-header">
    <div class="zyw-container white-color">
        <div class="head-l"><a href="javascript:history.back(-1);" target="_self"><i class="iconfont icon-fanhui1"></i></a></div>
        <h1>余额提现</h1>
    </div>
</header>
<section class="zyw-container">
    <div class="tixian-box">
        <div class="tobank">
            <span class="dzyh">提现到</span>
            <span class="yhk">微信零钱</span>
            <span class="dz"></span>
        </div>
        <div class="t-moneys">
            <span class="txje">提现金额</span>
            <span class="rmb">￥</span>
            <span class="kyye">当前零钱余额：<span id="cur-balance">{{sprintf("%.2f", $userInfo->balance/100)}}</span>元，<a href="javascript:;" id="getall">全部提现</a></span>
            <input type="number" max="{{($userInfo->balance-$userInfo->lockBalance)/100}}" value="" id="getmoneys" class="t-input">
            <button id="getout">提现</button>
        </div>
     </div>
</section>
@include('users.inc.footer')
<script type="text/javascript">
    $('#getall').click(function(){
        $('#getmoneys').val($('#cur-balance').html());
    });
    $('#getout').click(function(){
        var buttons = $(this);
        var amount = parseInt($("#getmoneys").val() + 0);
        var maxAmount = $("#getmoneys").prop('max');
        if (amount < 1) {
            $.toast('提现金额错误', "forbidden");
            return false;
        }
        if (amount > maxAmount) {
            $.toast('余额不足', "forbidden");
            return false;
        }
        $.showLoading();
        $.ajax({
            url: '/order/withdraw',
            type: 'POST',
            data:{amount:amount},
            dataType: 'json',
            beforeSend: function() {
                buttons.attr('disabled', 'true');
            },
            success: function(jsonObject) {
                $.hideLoading();
                if (jsonObject.code == 200) {

                } else {
                    $.toast(jsonObject.messages, "forbidden");
                }
                buttons.removeAttr('disabled');
            },
            error: function(xhr, type) {
                buttons.removeAttr('disabled');
                $.hideLoading();
            }
        })
    });
</script>