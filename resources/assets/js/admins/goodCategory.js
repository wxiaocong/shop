//显示分类数据信息
function showCategory(categoryId,level)
{
	if (level == 0) {
		$("#cate").animate({scrollTop:0}, 'fast');
	}
	if (level < 3) {
		$.ajax({
	       url: '/admin/goods/category/' + categoryId + '/findByParentId',
	       type: 'get',
	       dataType: 'json',
	       success: function(jsonObject) {
	           if (jsonObject.code == 200) {
	                var datas = jsonObject.datas;
	                var name = 'second';
              		if (level+1 == 2) {
              			name = 'three';
              		}

	                if (datas.length > 0) {
	              		var html = '';
	                    for (var i = 0; i < datas.length; i++) {
	                    	if (name == 'three') {
	                    		html += '<li><label>';
	                    	} else {
	                    		html += '<li onclick="showCategory(' + datas[i]['id'] + ',' + (level+1) + ');"><label>';
	                    	}
							html += '<input name="categoryVal" type="radio" value="' + datas[i]['id'] + '" onchange="selectCategory(this' + ',' + (level+1) + ');"/>';
							html += '<span>' + datas[i]['name'] + '</span></label></li>';
	                    }
	                    if (name == 'second') {
	           				$('.category-box-dialog').find('.categroy-second' ).html(html).removeClass('hidden');
	           				$('.category-box-dialog').find('.categroy-three').html('').addClass('hidden');
	           			} else {
	           				$('.category-box-dialog').find('.categroy-three').html(html).removeClass('hidden');
	           			}
	           		} else {
	           			if (name == 'second') {
	           				$('.category-box-dialog').find('.categroy-second' ).html('').addClass('hidden');
	           				$('.category-box-dialog').find('.categroy-three').html('').addClass('hidden');
	           			} else {
	           				$('.category-box-dialog').find('.categroy-three').html('').addClass('hidden');
	           			}
	           		}
	           	} else {
	               tips(jsonObject.messages);
	           	}
	       },
	       error: function(xhr, type) {
	           ajaxResponseError(xhr, type);
	       }
	    });
    }
}

//选择分类数据
function selectCategory(obj, level)
{
	var categoryId = $(obj).val();
	if($(obj).prop('checked')){
		if (level == 0) {
			$(obj).prop('checked',false);
		} else {
			var html = '<ctrlarea style="margin-right:5px;">';
			html += '<input type="hidden" value="' + categoryId + '" name="goodCategoryId">';
			html += '<button class=\"btn btn-default category-del\" type=\"button\">';
			html += '<span>' + $(obj).next().html() + '</span></button></ctrlarea>';
			$('.category-box').empty();
			$('.category-box').append(html);
		}
	} else {
		$('.category-box').empty();
	}
}

$('.category-box').on('click', '.category-del', function() {
	var obj = $(this).parent();
	$(this).cnConfirm('确定要删除此分类?', function() {
        obj.remove();
    });
});