//订单取消
$('.order-cancel-button').click(function() {
	$(this).cnConfirm('确定要取消此订单?', function() {
        orderSubmit('/admin/order/' + $('.order-id').val() + '/cancel', 'post', {});
    });
});
//订单退款
$('.order-refundment-button').click(function() {
	$(this).cnConfirm('确定要退款退单?', function() {
        orderSubmit('/admin/order/' + $('.order-id').val() + '/refundment', 'post', {});
    });
});
//订单发货
$('.order-deliver-button').click(function() {
    var orderSn = $('.order-sn').val();
	art.dialog({
		id:'deliver',
		title: '【发货】' + '订单编号:' + orderSn,
        content: $('.order-deliver-template').html(),
        cancelVal:'关闭',
		okVal:'发货',
	    ok:function(iframeWin, topWin){
	    	var idKeyNumValues = new Array();
	    	var flag = false;
	    	$('.order-deliver-form').find('input[name="orderGoodNums[]"]').each(function() {
	    		var num = $(this).val();
	    		var id = $(this).next().val();
	    		if (num != null && num != '' && num != 'undefined' && num > 0) {
	    			flag = true;
	    		} else {
	    			num = 0;
	    		}
	    		if (idKeyNumValues[id] == null) {
		    		idKeyNumValues[id] = num;
	    		} else if (num > 0) {
		    		idKeyNumValues[id] = num;
	    		}
	    	});
	    	if (!flag) {
				alert('请输入商品的发货数量');
	    		return false;
	    	}

	    	var nums = new Array();
	    	var ids = new Array();
	    	for (id in idKeyNumValues) {
	    		ids.push(id);
	    		nums.push(idKeyNumValues[id]);
	    	}

	    	var expressName = $('input[name="expressName"]').val();
	    	var expressNo = $('input[name="expressNo"]').val();
	    	if (expressName == null || expressName == '' || expressName == 'undefined') {
	    		alert('请输入快递物流公司');
	    		return false;
	    	}
	    	if (expressNo == null || expressNo == '' || expressNo == 'undefined') {
	    		alert('请输入快递单号');
	    		
	    		return false;
	    	}
	    	$.ajax({
		        url:  '/admin/order/' + $('.order-id').val() + '/deliver',
		        data: {'orderGoodIds[]':ids, 'orderGoodNums[]':nums, 'expressName':expressName, 'expressNo':expressNo},
		        type: 'post',
		        dataType: 'json',
		        success: function(jsonObject) {
		            if (jsonObject.code == 200) {
		                window.location.href = jsonObject.url;
		            } else {
		                showErrorNotice(jsonObject.messages);
		                return false;
		            }
		        },
		        error: function(xhr, type) {
		            ajaxResponseError(xhr, type);
		            return false;
		        }
		    });
	    },
	    cancel:function (){
	    	return true;
		}
	});
});


function orderSubmit(url, type, data)
{
	$.ajax({
        url:  url,
        data: data,
        type: type,
        dataType: 'json',
        success: function(jsonObject) {
            if (jsonObject.code == 200) {
                window.location.href = jsonObject.url;
            } else {
                showErrorNotice(jsonObject.messages);
            }
        },
        error: function(xhr, type) {
            ajaxResponseError(xhr, type);
        }
    });
}