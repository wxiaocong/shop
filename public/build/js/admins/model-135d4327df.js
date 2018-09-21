$(document).ready(function () {
	$('.add-attribute').click(function() {
		var template = $('.attribute-row-template').clone().html();
		$('.edit-model-form').find('.attribute-tr:last').after(template);
	});

	$('.edit-model-form').on('click', '.del-attribute', function() {
        if($('.edit-model-form').find('.attribute-tr').length > 1) {
            $(this).parent().parent().remove();
        }
	});

	$('.edit-model-form').on('click', '.attribute-search-check', function() {
		var checkValue = $(this).siblings('[name="attribute[isSearch][]"]')[0];
		if($(this).prop("checked")) {
			checkValue.value = 1;
		} else {
			checkValue.value = 0;
		}
	});

    $('.edit-model-form').on('click', '.attribute-spec-check', function() {
        var checkValue = $(this).siblings('[name="attribute[isSpec][]"]')[0];
        if($(this).prop("checked")) {
            checkValue.value = 1;

            $(this).parent().parent().find('select[name="attribute[showType][]"]').html('<option value="1">单选框</option>');
        } else {
            checkValue.value = 0;

            var html = '';
            html += '<option value="1">单选框</option>';
            html += '<option value="2">复选框</option>';
            html += '<option value="3">下拉框</option>';
            html += '<option value="4">输入框</option>';
            $(this).parent().parent().find('select[name="attribute[showType][]"]').html(html);
        }
    });

    $('.model-form').on('click', '.model-del', function() {
        var id = $(this).find('input').val()
        $(this).cnConfirm('确定要删除模型?', function() {
            $.ajax({
                url: '/admin/model/' + id,
                type: 'delete',
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
        });
    });

    $('.model-batch-del').click(function() {
        if ($("input[name='id[]']:checked").length == 0) {
            $(this).cnAlert('请选择要批量删除的模型', 5);
            return false;
        }
        $(this).cnConfirm('确定要批量删除模型?', function() {
            var checkedBox = $("input[name='id[]']:checked");
            var ids = new Array();
            checkedBox.each(function() {
                var id = $(this).val();
                ids.push(id);
            });
            $.ajax({
                url: '/admin/model/destroyAll',
                data: {
                    'ids[]': ids
                },
                type: 'post',
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
        });     
    });

	$('.edit-model-form').validate({
        submitHandler: function(form) {
            var buttons = $('.model-submit');
            var oldBtnText = buttons.text();
            var action = $('.model-id').val() == "" ? 'post' : 'put';
            var url = action == 'put' ? '/admin/model/' + $('.model-id').val() : '/admin/model';
            $.ajax({
                url:  url,
                data: $(form).serialize(),
                type: action,
                dataType: 'json',
                beforeSend: function() {
                    buttons.attr('disabled', 'true').text('提交中...');
                },
                success: function(jsonObject) {
                    if (jsonObject.code == 200) {
                        window.location.href = jsonObject.url;
                    } else {
                        showErrorNotice(jsonObject.messages);
                    }
                    buttons.removeAttr('disabled').text(oldBtnText);
                },
                error: function(xhr, type) {
                    ajaxResponseError(xhr, type);
                    buttons.removeAttr('disabled').text(oldBtnText);
                }
            });
        },
        // errorPlacement  
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        // rules
        rules: {
            name: {
                required: true
            }
        },
        messages: {
            name: {
                required: "请输入模型名称"
            }
        }
    });
});

