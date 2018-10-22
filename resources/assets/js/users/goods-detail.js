$(document).ready(function() {
    $.toast.prototype.defaults.duration = 500;//提示锁定时间
    var swiper = resetSwip();
    var MAX = 0;//最大下单
    var number = 1;//下单数量
    $('.weui-count__decrease').click(function (e) {
        number = number - 1;
        if (number < 1) {
            number = 0;
            $('.weui-count__number').val(number);
        } else {
            changeSpec(); 
        }
    });
    $('.weui-count__increase').click(function (e) {
        number = number + 1;
        if (number > MAX) {
            number = MAX;
        } else {
            changeSpec();
        }
    });
    $('.weui-count__number').change(function(){
        number = $(this).val();
        if(number > MAX) {
            number = MAX;
        }
        if (number < 1){
            number = 0;
            $(this).val(number);
        } else {
            changeSpec();
        }
    });
    //关闭popup，兼容ios
    $('.zyw-container').on('click','.weui-popup__overlay',function(){
        $.closePopup();
    });
    //切换规格
    $('.item-spec-layer').on('click','li',function(){
        $(this).addClass('active').siblings().removeClass('active');
        number = 1;
        changeSpec();
    });
    //默认选中当前规格
    if($('.spec-info').length > 0){
        var specValue = $('#specAttr').text().split(',');
        $('.spec-info ul li').each(function(){
            if(specValue.indexOf($(this).text()) >= 0) {
                $(this).addClass('active');
            }
        });
        changeSpec();
    };
    //立即购买
    $('.buy-now').click(function(){
        var number = parseInt($('.weui-count__number').val() || "0");
        if (number == 0 || MAX == 0 || number > MAX){
            $.toast("库存不足", "cancel");
            return false;
        }
        $('.spec-info ul').each(function(){
            $('#purchase-form').append($("<input type='hidden' name='spec["+$(this).attr('data')+"]' value='"+$(this).find('.active').text()+"' />"));
        });
        $('#purchase-form').submit();
        return false;
    });
    //改变属性，重新获取价格等
    function changeSpec(){
        $.showLoading();
        var attrValue = []; //页面显示规格
        var tmpAttr = '';
        var specData = {'goods_id':$('#goods_id').val(), 'spec_id':$('#spec_id').val(), 'num':number,'spec':{}};//请求数据
        $('.spec-info ul').each(function(){
            tmpAttr = $(this).find('.active').text();
            specData.spec[$(this).attr('data')] = tmpAttr;
            attrValue.push(tmpAttr);
        });
        $('#specAttr').html(attrValue.join());
        $.ajax({
            url:  '/goods/changeSpec',
            data: specData,
            type: 'post',
            dataType: 'json',
            success: function(jsonObject) {
                $.hideLoading();
                var slideHtml = '';
                var specHtml = '';
                var slige_imgs = [];
                var price = (parseInt(jsonObject.sell_price)/100).toFixed(2);
                specHtml = "<strong class='details-prince theme-color pull-left'>￥<span>"+price+"</span></strong>";
                $('#spec-price').html(specHtml);
                MAX = parseInt(jsonObject.number || 0);//最大可售
                if(MAX < 1) {
                    $('.buy-now').attr('disabled','disabled').addClass('footer-danger').html('库存不足');
                } else {
                    $('.buy-now').removeAttr('disabled').removeClass('footer-danger').html('立即购买');
                }
                
                $('.spec-head-img img').prop('src',jsonObject.img);//sku小图
                $('.details-title').html(jsonObject.name);//主名称
                $('.spec-weight').html(jsonObject.weight);//重量
                //sku选择页面信息修改
                $('.weui-count__number').val(number);
                $('.spec-head-prince span').html(price);
                $('.spec-head-intro span').html(jsonObject.cust_partno);

                //组图变更
                slige_imgs = JSON.parse(jsonObject.imgs);
                if(slige_imgs.length > 0) {
                    if(slige_imgs.length > 1) {
                        slige_imgs.splice(0,1);
                    }
                    for(index in slige_imgs){
                        slideHtml += "<div class='swiper-slide'><img src='" + slige_imgs[index] + "' ></div>";
                    }
                    $('.swiper-wrapper').html(slideHtml);
                    swiper = resetSwip();
                } else {
                    $('.swiper-wrapper').html('');
                    $('.swiper-pagination').html('');
                    swiper.removeAllSlides();
                }
            },
            error: function(xhr, type) {
                $.hideLoading();
                $.toast("数据异常,请重试", "cancel");
            }
         });
    }
    //重置swiper
    function resetSwip() {
        return new Swiper('.item-img', {
            autoplay:true,
            delay: 5000,
            loop:true,
            slidesPerView: 1,
            spaceBetween: 0,
            observer: true,
            observeParents: true,
            keyboard: {
                enabled: true,
            },
            pagination: {
                el: '.swiper-pagination',
                type: 'fraction',
            },
        });
    }
})
