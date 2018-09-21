$(document).ready(function() {
    var loading = false;
    var maxSpec = parseInt($('#max_spec').val());
    var specId = parseInt($('#spec_id').val());
    var promotionId = parseInt($('#promotion_id').val());
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
        if(promotionId > 0) {
            $.showLoading();
            var specData = {'spec_id':$('#spec_id').val(), 'num':number};//请求数据
            $.ajax({
                url:  '/goods/changeNum',
                data: specData,
                type: 'post',
                async: false,
                dataType: 'json',
                success: function(jsonObject) {
                    $.hideLoading();
                    if(jsonObject.award_value && jsonObject.promotion_number >= number){ //有活动
                        var award =  JSON.parse(jsonObject.award_value);
                        price = (parseInt(award.price)/100).toFixed(2);
                        $('.sku_price del').html('');
                    } else {
                        if (jsonObject.award_value && jsonObject.promotion_number < number) {
                            $('.sku_price del').html('活动价限购'+jsonObject.promotion_number+'件');
                        }
                        price = (parseInt(jsonObject.sell_price)/100).toFixed(2);
                    }
                    $('.sku_price span').html(price);
                    maxSpec = parseInt(jsonObject.number || 0);//最大可售
                    changeMoney();
                },
                error: function(xhr, type) {
                    $.hideLoading();
                    $.toast("数据异常,请重试", "cancel");
                }
             });
        }
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
            data:{express_id:express_id,num:num,spec_id:specId},
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