$(document).ready(function() {
    var loading = false;
  //商品滑动
    var swiper = new Swiper('.seckill-wares', {
        slidesPerView: 3.5,
        spaceBetween: 5,
        freeMode: true
    });
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
    //提交订单
    $('.confirm-order').click(function(){
        var buttons = $(this);
        var express_id = $("#express_id").val();
    
        if(express_id < 1){
            $.toast("请填写收货地址", "cancel");
            return false;
        }
        $.showLoading();
        $.ajax({
            url: '/order/cartStore',
            type: 'POST',
            data:{express_id:express_id,spec:$('#spec').val()},
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