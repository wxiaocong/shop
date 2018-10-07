$(document).ready(function() {
    var loading = false;
    var maxSpec = parseInt($('#max_spec').val());
    var specId = parseInt($('#spec_id').val());
    var number = parseInt($('.num').val());
    var price = Number($('.sku_price span').text());
    $.post('/address/getExpressAddress',function(content){
        $('.panel-addr').html(content);
    });
    $('#openPopup').click(function(){
        $("#expressAddress").popup('open');
    });
    //更换地址
    $('.panel-addr').on('click','.weui-media-box__bd',function(){
        var tar = $(this);
        $('#area').html(tar.html());
        $.closePopup();
        tar.parents('.weui-panel').prependTo('.panel-addr');
        $('.panel-addr').find('.icon-gou').prependTo(tar.parent('.weui-media-box'));
        $('#express_id').val(tar.attr('data'));
    });
    //数量变更
    $('.num').change(function(){
        number = parseInt($(this).val());
        if(number <= 1){
            number = 1;
            $('.minus').addClass('disabled');
        } else {
            $('.minus').removeClass('disabled');
        }
        if(number >= maxSpec){
            number = maxSpec;
            $('.plus').addClass('disabled');
        } else {
            $('.plus').removeClass('disabled');
        }
        $(this).val(number);
        changeSpec();
    });
    $('.minus').click(function(){
        number = parseInt($('.num').val());
        number = number - 1;
        if(number <= 1){
            number = Math.min(1,maxSpec);
            $('.minus').addClass('disabled');
        }
        $('.plus').removeClass('disabled');
        $('.num').val(number);
        changeSpec();
    });
    $('.plus').click(function(){
        number = parseInt($('.num').val());
        number = number + 1;
        if(number >= maxSpec){
            number = maxSpec;
            $('.plus').addClass('disabled');
        }
        $('.minus').removeClass('disabled');
        $('.num').val(number);
        changeSpec();
    });
    //初始化
    changeSpec();
    //数量变更，如果有活动，符合条件更改价格
    function changeSpec(){
        changeMoney();
    }
    //金额变更
    function changeMoney(){
        var newMoney = (number * price).toFixed(2);
        $('.goodsMoney').text(newMoney);
        $('.realPay').text(Number(newMoney) + Number($('.freight').text()));
    }
    //提交订单
    $('.confirm-order').click(function(){
        var buttons = $(this);
        var express_id = $("#express_id").val();
        var num = Number($('.num').val());
        if (num < 1) {
            $.toast("未选择商品数量", "cancel");
            return false;
        }
        if(num > maxSpec) {
            $.toast("库存不足", "cancel");
            return false;
        }
    
        if(express_id < 1){
            $.toast("请填写收货地址", "cancel");
            return false;
        }
        $.showLoading();
        $.ajax({
            url: '/order',
            type: 'POST',
            data:{express_id:express_id,num:num,spec_id:specId,remark:$('#remark').val()},
            dataType: 'json',
            beforeSend: function() {
                buttons.attr('disabled', 'true');
            },
            success: function(jsonObject) {
                $.hideLoading();
                if (jsonObject.code == 200) {
                    window.location.replace(jsonObject.url);
                } else {
                    $.toast(jsonObject.messages, "forbidden");
                }
                buttons.removeAttr('disabled');
            },
            error: function(xhr, type) {
                $.hideLoading();
                $.toast(type,"forbidden");
                buttons.removeAttr('disabled');
            }
        });
        return false;
    });
})