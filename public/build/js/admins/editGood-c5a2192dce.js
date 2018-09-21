var newRelationSpec = null;
$('#tab1').on('click', '.good-sku-new-relation-spec', function() {
	$('#oldGoodSku').find('.old-good-sku-id:checked').each(function() {
		$(this).attr('checked', false);
	});

	newRelationSpec = $(this);
	$('#oldGoodSku').modal('show');
});

$('#oldGoodSku').on('click', '.good-sku-new-relation-spec-close', function() {
	$('#oldGoodSku').find('.old-good-sku-id:checked').each(function() {
		$(this).attr('checked', false);
	});

	newRelationSpec = null;
	$('#oldGoodSku').modal('show');
});

$('#oldGoodSku').on('click', '.good-sku-new-relation-spec-submit', function() {
	if ($('#oldGoodSku').find('.old-good-sku-id:checked').length > 0) {
		$('#oldGoodSku').find('.old-good-sku-id:checked').each(function() {
			var trObj = newRelationSpec.parent().parent();
			var oldTrObj = $(this).parent().parent().parent().parent();
			//设置关联的values值
			$(this).parent().parent().find('.old-good-sku-relation-attr-values').val(trObj.find('input[name="goodSku[specValues][]"]').val());
			//设为为不可选
			$(this).attr('disabled', true);
			//取消选中
			$(this).attr('checked', false);

			//将关联的原有信息写入
			trObj.find('input[name="goodSku[id][]"]').val($(this).val());
			trObj.find('input[name="goodSku[name][]"]').val($(this).parent().parent().find('.old-good-sku-name').val());
			trObj.find('input[name="goodSku[custNo][]"]').val(oldTrObj.find('.old-good-sku-cust-no').val());
			trObj.find('input[name="goodSku[barCode][]"]').val(oldTrObj.find('.old-good-sku-bar-code').val());
			trObj.find('input[name="goodSku[storeNum][]"]').val(oldTrObj.find('.old-good-sku-store-num').val());
			trObj.find('input[name="goodSku[warningNum][]"]').val(oldTrObj.find('.old-good-sku-warning-num').val());
			trObj.find('input[name="goodSku[sellPrice][]"]').val(oldTrObj.find('.old-good-sku-sell-price').val());
			trObj.find('input[name="goodSku[memberPrice][]"]').val(oldTrObj.find('.old-good-sku-member-price').val());
			trObj.find('input[name="goodSku[wholesalePrice][]"]').val(oldTrObj.find('.old-good-sku-wholesale-price').val());
			trObj.find('input[name="goodSku[costPrice][]"]').val(oldTrObj.find('.old-good-sku-cost-price').val());
			trObj.find('input[name="goodSku[weight][]"]').val(oldTrObj.find('.old-good-sku-weight').val());
			var state = oldTrObj.find('.old-good-sku-state').val();
			if (state == 2) {
				trObj.find('select[name="goodSku[state][]"]').find('option:contains("上架")').attr('selected', false);
				trObj.find('select[name="goodSku[state][]"]').find('option:contains("下架")').attr('selected', true);
			}

			var valuesInput = trObj.find('.file-input').next();
			var html = '';
			oldTrObj.find('.old-good-sku-img').each(function() {
				html += '<div class="pic pull-left">';
				html += '<img class="img-thumbnail" style="margin-top:12px;margin-right:14px;width:160px;height:160px" src="' + $(this).val() + '?x-oss-process=image/resize,w_160,h_160" alt="' + $(this).val() + '?x-oss-process=image/resize,w_160,h_160">';
				html += '<p class="text-center">';
				html += '<a href="javascript:void(0);" class="del-good-spec-old-img">';
				html += '<i class="operator fa fa-close" title="删除"></i>';
				html += '<input type="hidden" name="goodSku[pic][' + valuesInput.val() + '][]" value="' + $(this).val() + '"/>';
				html += '</a>';
				html += '</p>';
				html += '</div>';
			});
			if (html != '') {
				valuesInput.after(html);
			}

			//隐藏关联按钮
			newRelationSpec.addClass('hidden');
		});

		newRelationSpec = null;
		$('#oldGoodSku').modal('hide');
	} else {
		showErrorNotice(new Array('没有关联数据'));
	}
});

$('#oldGoodSku').on('click', '.old-good-sku-id', function() {
	var value = $(this).attr('checked');
	if (value == true || value == 'checked') {
		$(this).attr('checked', false);
	} else {
		$('#oldGoodSku').find('.old-good-sku-id:checked').not($(this)).each(function() {
			//取消选中
			$(this).attr('checked', false);
		});

		$(this).attr('checked', true);
	}
});


