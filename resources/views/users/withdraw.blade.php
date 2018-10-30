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
            <span class="yhk">{{$bank_no[$userInfo->bank_code]}}：{{$userInfo->enc_bank_no}}</span>
            <span class="dz">实名认证：{{$userInfo->realname}}</span>
        </div>
        <div class="t-moneys">
            <span class="txje">提现金额</span>
            <span class="rmb">￥</span>
            <span class="kyye">当前零钱余额：<span id="cur-balance">{{sprintf("%.2f", ($userInfo->balance-$userInfo->lockBalance)/100)}}</span>元，<a href="javascript:;" id="getall">全部提现</a></span>
            <input type="number" max="{{($userInfo->balance-$userInfo->lockBalance)/100}}" min="100" value="" id="getmoneys" class="t-input">
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
        var amount = parseInt($("#getmoneys").val()) + 0;
        var maxAmount = $("#getmoneys").prop('max');
        if (amount < 100) {
            $.toast('最小提现金额为100元', "forbidden");
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
                    $.alert("尊敬的用户您好，您的提款申请我们已经收到，我们客服会在1-2个工作日处理。请您留意银行短信提醒", jsonObject.messages, function() {
                        window.location.href = jsonObject.url;
                    });
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
